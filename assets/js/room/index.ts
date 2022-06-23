import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";
import {swaleDangerAndRedirect, swaleDanger} from "../util/swal";
import Routing from "../Routing";
import {openAjaxModal} from "../util/modal";

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

function initTypes(){
    // Mise en place du binding pour l'ajout d'un type
    const addButton = document.querySelector("#btn-add-room-type");
    addButton?.addEventListener("click", () => {
        const url = Routing.generate("add_enum",{
            class : "App\\Entity\\Room\\RoomType"
        });
        openAjaxModal(url, "Ajouter un type");
    })

    // Mise en place du binding pour l'édition d'un des types
    const editTypeCallback = (e: Event) => {
        const button = <HTMLInputElement>e.currentTarget;

        const url = Routing.generate("edit_enum",{
            class : "App\\Entity\\Room\\RoomType",
            id : button.dataset.type
        });
        openAjaxModal(url, "Modifier un type");
    }

    $(".btn-edit-room-type", (elem: Node) => {
        elem.addEventListener('click', editTypeCallback)
    });

    // Mise en place du binding pour l'archivage d'un des types
    const callback = (e: Event) => {
        e.stopPropagation();

        const buttons = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer un type de salle."
        const url = Routing.generate('app_archive_room_type', {
            id: buttons.dataset.type
        });

        swaleDangerAndRedirect(text, url);
    };

    $(".btn-delete-room-type", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('layout.types.loaded', () => {
    initTypes();
});

function initOptions(){
    // Mise en place du binding pour l'ajout d'une option
    const addButton = document.querySelector("#btn-add-room-option");
    addButton?.addEventListener("click", () => {
        const url = Routing.generate("add_enum",{
            class : "App\\Entity\\Room\\RoomOption"
        });
        openAjaxModal(url, "Ajouter une option");
    })

    // Mise en place du binding pour l'édition d'une des options
    const editOptionCallback = (e: Event) => {
        const button = <HTMLInputElement>e.currentTarget;

        const url = Routing.generate("edit_enum",{
            class : "App\\Entity\\Room\\RoomOption",
            id : button.dataset.option
        });
        openAjaxModal(url, "Modifier une option");
    }

    $(".btn-edit-room-option", (elem: Node) => {
        elem.addEventListener('click', editOptionCallback)
    });

    // Mise en place du binding pour l'archivage d'une option
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer une option de salle."
        const url = Routing.generate('app_archive_room_option', {
            id: button.dataset.option
        });

        swaleDangerAndRedirect(text, url);
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
