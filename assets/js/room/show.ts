import {$, findInDataset} from "../utils";
import { swaleWarningAndRedirect } from "../util/swal";
import Routing from "../Routing";
import {Calendar} from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from '@fullcalendar/core/locales/fr';

let showRoomCalendar: Calendar;

const initShow = () => {
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver une salle."
        const url = Routing.generate('app_archive_room', {
            id: button.dataset.room
        });

        swaleWarningAndRedirect(text, url);
    };

    $(".btn-delete-room")?.addEventListener('click', callback);
}


document.addEventListener('layout.room-information.loaded', () => {
    initShow();
});

const initRoomPlanning = () => {
    const calendarElement: HTMLElement | null = document.getElementById("room-show-planning");

    const url = Routing.generate("event_resource_id",{
        class : "App\\Entity\\Room\\RoomType",
        id: findInDataset($("#room-show-planning") as HTMLElement, "roomId")
    });

    if (!(!!calendarElement))
        return;

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