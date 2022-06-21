import {$} from "../utils";
import { swaleWarningAndRedirect } from "../util/swal";
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

    if (!(!!calendarElement))
        return;

    let calendar = new Calendar(calendarElement, {
        plugins: [ dayGridPlugin, timeGridPlugin, listPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        locale: frLocale
    });

    calendar.render();
}

document.addEventListener('layout.room-planning.loaded', () => {
    initRoomPlanning();
});

document.addEventListener('DOMContentLoaded', () => {
    initShow();
    initRoomPlanning();
});