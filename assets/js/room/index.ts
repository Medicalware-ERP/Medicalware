import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";
import {swaleDangerAndRedirect, swaleDanger} from "../util/swal";
import Routing from "../Routing";
import {openAjaxModal, ModalOption} from "../util/modal";
import {initTypes} from "./index_types";
import {initOptions} from "./index_options";

const table = $("#table-rooms");

const archiveRoom = () => {
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver une salle."
        const url = Routing.generate('app_archive_room', {
            id: button.dataset.room
        });

        swaleDanger(text).then(r => {
            if (r.isConfirmed) {
                fetch(url).then(() => {
                    if (table instanceof HTMLTableElement) {
                        generateDatable(table);
                    }
                });
            }
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

document.addEventListener('layout.types.loaded', () => {
    initTypes();
});

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
