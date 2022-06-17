import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";
import {swaleWarning, swaleWarningAndRedirect} from "../util/swal";
import Routing from "../Routing";

const table = $("#table-rooms");

const archiveRoom = () => {
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver une salle."
        const url = Routing.generate('app_archive_room', {
            id: button.dataset.room
        });

        swaleWarning(text).then(r => {
            fetch(url).then(() => {
                if (table instanceof HTMLTableElement) {
                    generateDatable(table);
                }
            });
        });
    };

    $(".btn-delete-room", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('datatable.loaded', archiveRoom);

document.addEventListener('layout.rooms.loaded', () => {
    const roomTable = $("#table-rooms");
    if (!!roomTable) generateDatable(roomTable as HTMLTableElement);
});

function initTypes(){
    const callback = (e: Event) => {
        e.stopPropagation();

        const buttons = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver un type de salle."
        const url = Routing.generate('app_archive_room_type', {
            id: buttons.dataset.type
        });

        swaleWarningAndRedirect(text, url);
    };

    $(".btn-delete-room-type", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('layout.types.loaded', () => {
    initTypes();
});

function initOptions(){
    const callback = (e: Event) => {
        e.stopPropagation();

        const buttons = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver une option de salle."
        const url = Routing.generate('app_archive_room_option', {
            id: buttons.dataset.option
        });

        swaleWarningAndRedirect(text, url);
    };

    $(".btn-delete-room-option", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('layout.options.loaded', () => {
    initOptions();
});

document.addEventListener('DOMContentLoaded', () => {
    initTypes();
    initOptions();
});

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}