import Routing from "../Routing";
import {ModalOption, openAjaxModal} from "../util/modal";
import {$} from "../utils";
import {swaleDangerAndRedirect} from "../util/swal";

const initEventType = () => {
    // Mise en place du binding pour l'ajout d'un type
    const addButton = document.querySelector("#btn-add-event-type");
    addButton?.addEventListener("click", () => {
        const url = Routing.generate("add_enum",{
            class : "App\\Entity\\Planning\\EventType"
        });

        const modalOption: ModalOption = {
            title: "Ajouter un type",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    })

    // Mise en place du binding pour l'édition d'un des types
    const editTypeCallback = (e: Event) => {
        const button = <HTMLInputElement>e.currentTarget;

        const url = Routing.generate("edit_enum",{
            class : "App\\Entity\\Planning\\EventType",
            id : button.dataset.type
        });

        const modalOption: ModalOption = {
            title: "Modifier un type",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    }

    $(".btn-edit-event-type", (elem: Node) => {
        elem.addEventListener('click', editTypeCallback)
    });

    // Mise en place du binding pour l'archivage d'un des types
    const callback = (e: Event) => {
        e.stopPropagation();

        const buttons = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer un type de salle."
        const url = Routing.generate('archive_event_type', {
            id: buttons.dataset.type
        });

        swaleDangerAndRedirect(text, url);
    };

    $(".btn-delete-event-type", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
};
document.addEventListener('DOMContentLoaded', () => {
    initEventType();
});