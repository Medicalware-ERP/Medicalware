import {$} from "../utils";
import {swaleWarning, swaleWarningAndRedirect} from "../util/swal";
import Routing from "../Routing";

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