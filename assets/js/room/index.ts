import generateDatable from "../datatable/datatableGeneric";
import {$, findInDataset} from "../utils";
import {swaleDangerAndRedirect, swaleDanger} from "../util/swal";
import Routing from "../Routing";
import {openAjaxModal, ModalOption} from "../util/modal";
import {initTypes} from "./index_types";
import {initOptions} from "./index_options";
import {importSelect2} from "../app";

const table = $("#table-rooms");

const onClickArchiveButtons = () => {
    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver une salle."
        const url = Routing.generate('app_archive_room', {
            id: button.dataset.roomId
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

const onClickEditButtons = () => {
    // Mise en place du binding pour l'édition d'une salle
    $(".btn-edit-room", (elem: Node) => {
        elem.addEventListener('click', (e)=> {
            const currentButton = <HTMLInputElement>e.currentTarget;
            const id =  findInDataset(currentButton, 'roomId');
            const url = Routing.generate('app_edit_room', {id})

            const modalOption: ModalOption = {
                title: "Modifier une salle",
                removeAction: false
            }

            openAjaxModal(url, modalOption);
        });
    });
}

const initIndex = () => {
    // Mise en place du binding pour l'ajout d'une salle
    const addButton = document.querySelector("#btn-add-room");
    addButton?.addEventListener("click", () => {
        const url = Routing.generate("app_add_room");

        const modalOption: ModalOption = {
            title: "Ajouter une salle",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    });
}

document.addEventListener('datatable.loaded', () => {
    onClickArchiveButtons();
    onClickEditButtons();
    
    document.addEventListener("modal.loaded", (e) => {
        importSelect2(true);
    });
});

document.addEventListener('layout.rooms.loaded', () => {
    initIndex();
    generateDatable($("#table-rooms") as HTMLTableElement);
});

document.addEventListener('layout.types.loaded', () => {
    initTypes();
});

document.addEventListener('layout.options.loaded', () => {
    initOptions();
});

document.addEventListener('DOMContentLoaded', () => {
    initIndex();
    initTypes();
    initOptions();
});

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
