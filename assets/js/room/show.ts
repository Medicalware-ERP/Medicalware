import {$, findInDataset} from "../utils";
import {swaleWarning, swaleDangerAndRedirect} from "../util/swal";
import Routing from "../Routing";
import {DateSelectArg} from '@fullcalendar/common';
import {Calendar} from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from '@fullcalendar/core/locales/fr';
import interactionPlugin, {DateClickArg, Draggable} from '@fullcalendar/interaction';
import generateDatable from "../datatable/datatableGeneric";
import axios from "axios";
import {loadCurrentTab} from "../layout/layout_show";
import {openAjaxModal} from "../util/modal";
import {importSelect2} from "../app";

const initShow = () => {
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer une salle."
        const url = Routing.generate('app_archive_room', {
            id: button.dataset.room
        });

        swaleDangerAndRedirect(text, url);
    };

    $(".btn-delete-room")?.addEventListener('click', callback);
}


document.addEventListener('layout.room-information.loaded', () => {
    initShow();
});

const initRoomPlanning = () => {
    let showRoomCalendar: Calendar;
    const calendarElement: HTMLElement | null = document.getElementById("room-show-planning");
    const roomId = findInDataset($("#room-show-planning") as HTMLElement, "roomId");

    if (!(!!calendarElement))
        return;

    const url = Routing.generate("event_resource_id",{
        class : "App\\Entity\\Room\\Room",
        id: roomId
    })

    showRoomCalendar = new Calendar(calendarElement, {
        plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        editable: true,
        dayMaxEvents: true, // when too many events in a day, show the popover
        selectable: true,
        locale: frLocale,
        timeZone: "UTC",
        events: { url: url },
        eventDataTransform: (data) => {
            // On transforme nos datas en objet que le calendrier peux traiter
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
        eventDrop: info => editEventDate(info),
        eventResize: info => editEventDate(info),
        dateClick: info => openPlanningFormModal(info, roomId),
        select: info => openPlanningFormModal(info, roomId)
    });

    showRoomCalendar.render();

    document.querySelector("#btn-room-planning-add")?.addEventListener("click", () => {
        openPlanningFormModal(null, roomId);
    });
}

document.addEventListener('layout.room-planning.loaded', () => {
    initRoomPlanning();
});

document.addEventListener('DOMContentLoaded', () => {
    initShow();
    initRoomPlanning();
});

const editEventDate = (info: any) => {
    // TODO : toLocaleString() convertie le UTC en +2, il faut empêcher ça
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

const openPlanningFormModal = (info: DateClickArg | DateSelectArg | null, roomId: string, eventId: string | null = null) =>
{
    // S'il y a un eventId, il s'agit d'une édition
    if (!!eventId) {
        // TODO : Édition d'un évènement
    } else {
        //Sinon, d'un ajout
        let allDay: boolean = false;
        let startAt: string = "";
        let endAt: string = "";
        console.log(info, allDay)
        if (info !== null) {
            allDay = info.allDay;

            // Si c'est DateClickArg
            if ("dateStr" in info && info.dateStr) {
                startAt = info.dateStr;
                endAt = info.dateStr;
            } else if ("startStr" in info && info.startStr) {
                // Sinon si c'est DateSelectArg
                startAt = info.startStr;
                endAt = info.endStr;
            }
        }

        const url = Routing.generate("event_add",{
            class : "App\\Entity\\Room\\Room",
            id: roomId,
            allDay: allDay,
            startAt: startAt,
            endAt: endAt
        });

        openAjaxModal(url, `Ajouter un évènement à la salle #${roomId}`);
    }
}

document.addEventListener("modal.loaded", () => {
    importSelect2(true);
})