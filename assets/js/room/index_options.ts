import Routing from "../Routing";
import {ModalOption, openAjaxModal} from "../util/modal";
import {$} from "../utils";
import {swaleDangerAndRedirect} from "../util/swal";

export const initOptions = () => {
    // Mise en place du binding pour l'ajout d'une option
    const addButton = document.querySelector("#btn-add-room-option");
    addButton?.addEventListener("click", () => {
        const url = Routing.generate("add_enum",{
            class : "App\\Entity\\Room\\RoomOption"
        });

        const modalOption: ModalOption = {
            title: "Ajouter une option",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    })

    // Mise en place du binding pour l'édition d'une des options
    const editOptionCallback = (e: Event) => {
        const button = <HTMLInputElement>e.currentTarget;

        const url = Routing.generate("edit_enum",{
            class : "App\\Entity\\Room\\RoomOption",
            id : button.dataset.option
        });

        const modalOption: ModalOption = {
            title: "Modifier une option",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
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
};