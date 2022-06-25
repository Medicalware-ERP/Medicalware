import Routing from "../Routing";
import {ModalOption, openAjaxModal} from "../util/modal";
import {$} from "../utils";
import {swaleDangerAndRedirect} from "../util/swal";

export const initTypes = () => {
    // Mise en place du binding pour l'ajout d'un type
    const addButton = document.querySelector("#btn-add-room-type");
    addButton?.addEventListener("click", () => {
        const url = Routing.generate("add_enum",{
            class : "App\\Entity\\Room\\RoomType"
        });

        const modalOption: ModalOption = {
            title: "Ajouter un type",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    });

    // Mise en place du binding pour l'édition d'un des types
    const editTypeCallback = (e: Event) => {
        const button = <HTMLInputElement>e.currentTarget;

        const url = Routing.generate("edit_enum",{
            class : "App\\Entity\\Room\\RoomType",
            id : button.dataset.type
        });

        const modalOption: ModalOption = {
            title: "Modifier un type",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    };

    $(".btn-edit-room-type", (elem: Node) => {
        elem.addEventListener('click', editTypeCallback)
    });

    // Mise en place du binding pour l'archivage d'un des types
    const callback = (e: Event) => {
        e.stopPropagation();

        const buttons = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer un type d'évènement."
        const url = Routing.generate('app_archive_room_type', {
            id: buttons.dataset.type
        });

        swaleDangerAndRedirect(text, url);
    };

    $(".btn-delete-room-type", (elem: Node) => {
        elem.addEventListener('click', callback);
    });
};