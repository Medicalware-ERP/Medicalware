import {$, findInDataset} from "../utils";
import { swaleDangerAndRedirect } from "../util/swal";
import Routing from "../Routing";
import {Calendar} from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from '@fullcalendar/core/locales/fr';

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
        plugins: [ dayGridPlugin, timeGridPlugin, listPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        locale: frLocale,
        timeZone: "UTC",
        eventDataTransform: (data) => {
            // On transforme nos datas en objet que le calendrier peux traiter
            return {
                allDay: false,
                start: data.startAt,
                end: data.endAt,
                title: data.title,
                color: data.color,
                backgroundColor: data.color
            };
        },
        events: { url: url }
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