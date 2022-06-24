import {$, findInDataset} from "../utils";
import {swaleDangerAndRedirect} from "../util/swal";
import Routing from "../Routing";
import {declareCalendar} from "../util/planning";

const initShow = () => {
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer une salle."
        const url = Routing.generate('app_archive_room', {
            id: button.dataset.room
        });

        swaleDangerAndRedirect(text, url).then();
    };

    $(".btn-delete-room")?.addEventListener('click', callback);
}

document.addEventListener('layout.room-information.loaded', () => {
    initShow();
});

const initRoomCalendar = () => {
    const roomId: number = parseInt(findInDataset($("#room-show-planning") as HTMLElement, "roomId"));

    declareCalendar("room-show-planning", roomId, "App\\Entity\\Room\\Room");
}

document.addEventListener('layout.room-planning.loaded', () => {
    initRoomCalendar();
});

document.addEventListener('DOMContentLoaded', () => {
    initShow();
    initRoomCalendar();
});