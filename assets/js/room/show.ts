import {$, findInDataset} from "../utils";
import {swaleDangerAndRedirect} from "../util/swal";
import Routing from "../Routing";
import {declareCalendar} from "../util/planning";
import {ModalOption, openAjaxModal} from "../util/modal";

const initShow = () => {
    const onClickDeleteButton = (e: Event) => {
        e.stopPropagation();

        const currentButton: HTMLInputElement = <HTMLInputElement>e.currentTarget;
        const id: string =  findInDataset(currentButton, 'roomId');
        const text: string = "Vous êtes sur le point de supprimer une salle."
        const url = Routing.generate('app_archive_room', {id});

        swaleDangerAndRedirect(text, url).then();
    };

    const onClickEditButton = (e: Event) => {
        const currentButton: HTMLInputElement = <HTMLInputElement>e.currentTarget;
        const id: string =  findInDataset(currentButton, 'roomId');
        const url = Routing.generate('app_edit_room', {id})

        const modalOption: ModalOption = {
            title: "Modifier une salle",
            removeAction: false
        }

        openAjaxModal(url, modalOption);
    }

    $("#btn-delete-room")?.addEventListener('click', onClickDeleteButton)
    $("#btn-edit-room")?.addEventListener('click', onClickEditButton);
};

document.addEventListener('layout.room-information.loaded', () => {
    initShow();
});

const initRoomCalendar = () => {
    const roomPlanning = $("#room-show-planning") as HTMLElement;

    if (!!roomPlanning) {
        const roomId: number = parseInt(findInDataset(roomPlanning, "roomId"));
        declareCalendar("room-show-planning", roomId, "App\\Entity\\Room\\Room");
    }
};

document.addEventListener('layout.room-planning.loaded', () => {
    initRoomCalendar();
});

document.addEventListener('DOMContentLoaded', () => {
    initShow();
    initRoomCalendar();
});