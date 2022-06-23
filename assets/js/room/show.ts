import {$, findInDataset} from "../utils";
import {swaleWarning, swaleDangerAndRedirect} from "../util/swal";
import Routing from "../Routing";
import {Calendar} from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from '@fullcalendar/core/locales/fr';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import generateDatable from "../datatable/datatableGeneric";
import axios from "axios";
import {loadCurrentTab} from "../layout/layout_show";

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

    if (!(!!calendarElement))
        return;

    const url = Routing.generate("event_resource_id",{
        class : "App\\Entity\\Room\\Room",
        id: findInDataset($("#room-show-planning") as HTMLElement, "roomId")
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
        dateClick: info => console.error("TODO, envoyé sur formulaire modal, et pre remplir info de date et allDays", info),
        select: info => console.error("TODO, envoyé sur formulaire modal, et pre remplir info de date et allDays", info)
    });

    showRoomCalendar.render();
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
    const text = `Vous allez déplacer l'évènement sur la période du ${dateStart?.toLocaleString()} au ${dateEnd?.toLocaleString()}`;

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