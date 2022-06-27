import {Calendar} from "@fullcalendar/core";
import resourceTimelinePlugin  from "@fullcalendar/resource-timeline";
import Routing from "../Routing";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin, {DateClickArg} from "@fullcalendar/interaction";
import frLocale from "@fullcalendar/core/locales/fr";
import {swaleDangerAlert, swaleDangerAndRedirect, swaleWarning} from "./swal";
import axios from "axios";
import {DateSelectArg, EventClickArg} from "@fullcalendar/common";
import {closeAjaxModal, ModalOption, openAjaxModal} from "./modal";
import {importSelect2} from "../app";
import {$, findInDataset} from "../utils";

export const declarePlanning = (planningId: string) => {

    // TODO : Récupération des ressources
    // TODO : Puis récupération des events liés au ressources


    let planning: Calendar;
    const planningElement: HTMLElement | null = document.getElementById(planningId);

    if (!(!!planningElement))
        return console.error("L'élément HTML planning n'a pas été trouvé");

    const url = Routing.generate("event_resources");
    axios.get(url).then(result => {
        const resources = result.data["resources"];
        const events = result.data["events"];

        // Ajout de la propriété title sur les ressources
        resources.forEach((resource: any) => {
           resource.title = resource.resourceName;
           resource.parentId = resource.resourceClass;
        });

        resources.push(
            { id: "App\\Entity\\Room\\Room", title: "Salles" },
            { id: "App\\Entity\\User", title: "Employés" },
            { id: "App\\Entity\\Patient", title: "Patients" },
        );

        planning = new Calendar(planningElement, {
            plugins: [ resourceTimelinePlugin, interactionPlugin ],
            initialView: 'resourceTimeline',
            headerToolbar: {
                left: "prev,next",
                center: "title",
                right: "resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth"
            },
            editable: true,
            dayMaxEvents: true, // when too many events in a day, show the popover
            selectable: true,
            timeZone: "UTC",
            locale: frLocale,
            resources: resources,
            events: events,
            eventDataTransform: (data) => {
                // On transforme nos datas event en objet que le calendrier pourra traiter
                return {
                    id: data.id,
                    resourceId: data.resource.id,
                    allDay: !!data.allDay,
                    start: data.startAt,
                    end: data.endAt,
                    title: data.title,
                    color: data.color,
                    backgroundColor: data.color
                };
            },
            eventDrop: info => editEventDateResource(info),
            eventResize: info => editEventDate(info),
            eventClick: info => openShowEventModal(info)
        });

        planning.render();

        // L'event listener doit être déclaré uniquement si on à un calendar.
        // C'est pour ça qu'il est dans cette méthode et pas perdu à la racine de ce fichier.
        // Lorsqu'une modal s'ouvre, on import select2 afin de l'avoir dans la modal
        document.addEventListener("modal.loaded", (e) => {
            importSelect2(true);
            bindShowEventModalActionButtons();
        })
    });
}

export const declareCalendar = (calendarId: string, resourceId: number, resourceClass: string) => {
    // TODO : Faire un type optionCalendar (Comme sur modal.ts) ou on peux préciser si l'on désire différent plugins du planning ?

    let calendar: Calendar;
    const calendarElement: HTMLElement | null = document.getElementById(calendarId);

    if (!(!!calendarElement) || !(!!resourceId) || !(!!resourceClass))
        return console.error("Il faut renseigner tout les paramètres pour la déclaration du calendrier");

    const url = Routing.generate("event_resource_id",{
        class : resourceClass,
        id: resourceId
    })

    calendar = new Calendar(calendarElement, {
        plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek addNewEvent'
        },
        editable: true,
        dayMaxEvents: true, // when too many events in a day, show the popover
        selectable: true,
        locale: frLocale,
        timeZone: "UTC",
        events: { url: url },
        eventDataTransform: (data) => {
            // On transforme nos datas event en objet que le calendrier pourra traiter
            return {
                id: data.id,
                allDay: !!data.allDay,
                start: data.startAt,
                end: data.endAt,
                title: data.title,
                color: data.color,
                backgroundColor: data.color
            };
        },
        customButtons: {
            addNewEvent: {
                text: 'Ajouter un évènement',
                click: function() {
                    openAddEventModal(null, resourceId, resourceClass);
                }
            }
        },
        eventDrop: info => editEventDate(info),
        eventResize: info => editEventDate(info),
        select: info => openAddEventModal(info, resourceId, resourceClass),
        eventClick: info => openShowEventModal(info)
    });

    calendar.render();

    // L'event listener doit être déclaré uniquement si on à un calendar.
    // C'est pour ça qu'il est dans cette méthode et pas perdu à la racine de ce fichier.
    // Lorsqu'une modal s'ouvre, on import select2 afin de l'avoir dans la modal
    document.addEventListener("modal.loaded", (e) => {
        importSelect2(true);
        bindShowEventModalActionButtons();
    })
}

// Demande de confirmation de déplacement / resize d'un évènement (changement de dateTime uniquement)
const editEventDate = (info: any) => {
    const dateStart = info?.event?.start;
    const dateEnd = info?.event?.end;
    const text = `Vous allez déplacer l'évènement sur la période du ${dateStart?.toLocaleString('fr-FR', { timeZone: 'UTC' })} au ${dateEnd?.toLocaleString('fr-FR', { timeZone: 'UTC' })}`;

    swaleWarning(text).then(res => {
        if (res.isConfirmed) {
            const url = Routing.generate("event_edit_time", {
                id: info?.event?.id,
                startAt: dateStart?.toISOString(),
                endAt: dateEnd?.toISOString()
            });

            axios.get(url).then();
        } else {
            info.revert();
        }
    });
}

// Demande de confirmation de déplacement d'un évènement (changement de dateTime et de ressource)
const editEventDateResource = (info: any) => {
    const dateStart = info?.event?.start;
    const dateEnd = info?.event?.end;
    const newResource = info?.newResource;

    // Si on essaye de déplacer un évènement sur une fausse ressource (group)
    if (!!newResource && !(!!parseInt(newResource.id))) {
        const dangerText = `Vous ne pouvez pas déplacer un évènement sur un titre de groupe (${newResource.title})`;
        info.revert();
        return swaleDangerAlert(dangerText).then();
    }

    let text = `Vous allez déplacer l'évènement sur la période du ${dateStart?.toLocaleString('fr-FR', { timeZone: 'UTC' })} au ${dateEnd?.toLocaleString('fr-FR', { timeZone: 'UTC' })}`;
    if (!!newResource) text += ` dans la ressource "${newResource.title}"`

    swaleWarning(text).then(res => {
        if (res.isConfirmed) {
            const url = Routing.generate("event_edit_time_resource", {
                id: info?.event?.id,
                startAt: dateStart?.toISOString(),
                endAt: dateEnd?.toISOString(),
                newResourceId: !!newResource ? newResource.id : null
            });

            axios.get(url).then();
        } else {
            info.revert();
        }
    });
}

// Ouverture de la modal ajout d'un évènement
const openAddEventModal = (info: DateSelectArg | null, resourceId: number, resourceClass: string) => {
    //Sinon, d'un ajout
    let allDay: boolean = false;
    let startAt: string = "";
    let endAt: string = "";

    if (info !== null) {
        allDay = info.allDay;
        startAt = info.startStr;
        endAt = info.endStr;
    }

    const url = Routing.generate("event_add",{
        class : resourceClass,
        id: resourceId,
        allDay: allDay,
        startAt: startAt,
        endAt: endAt
    });

    const modalOption: ModalOption = {
        title: "Ajouter un évènement",
        removeAction: false
    }

    openAjaxModal(url, modalOption);
}

// Ouverture de la modal détails d'un évènement
const openShowEventModal = (info: EventClickArg) => {
    const event = info.event;

    const url = Routing.generate("event_show",{
        id: event.id
    });

    const modalOption: ModalOption = {
        title: `Visualisation de l'évènement "${event.title}"`,
        removeAction: true
    }

    openAjaxModal(url, modalOption);
}

// Binding sur les boutons d'action de la modal détails d'un évènement
const bindShowEventModalActionButtons = () => {
    // Au clique sur le bouton edit
    $("#btn-event-show-edit")?.addEventListener("click", () => {
        const url = Routing.generate('event_edit', {
            id: findInDataset($("#btn-event-show-edit") as HTMLElement, "eventId")
        });

        // On ferme la showEventModal
        closeAjaxModal();

        const modalOption: ModalOption = {
            title: `Édition d'un évènement`,
            removeAction: false
        }

        // Et on fait place à la modal d'édition
        openAjaxModal(url, modalOption);
    });

    // Au clique sur le bouton supprimer
    $("#btn-event-show-delete")?.addEventListener("click", () => {
        const url = Routing.generate('event_delete', {
            id: findInDataset($("#btn-event-show-edit") as HTMLElement, "eventId")
        });

        // On demande si on veux supprimer l'évènement, si oui on le supprime
        swaleDangerAndRedirect("Vous-êtes sur le point de supprimer un évènement", url, "#modal").then();
    });
};