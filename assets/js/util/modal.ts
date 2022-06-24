import {htmlToElement, simpleLoader, simpleLoaderModal} from "../utils";
import axios from "axios";
import $ from "jquery";

export const openModal = (id : string = "modal") => {
    const modal:HTMLElement|null = document.querySelector(`#${id}`);
    if(modal == null){
        return
    }
    // @ts-ignore
    modal?.showModal();
    const addButton = modal.querySelector("#submit__form");
    addButton?.addEventListener("click", () => {
        const form : HTMLFormElement|null = modal.querySelector(".modal-content form");
        form?.submit();
    })

    const closeButtons = modal.querySelectorAll(".close-button");
    closeButtons.forEach((closeBtn) => {
        closeBtn?.addEventListener("click", () => {
            // @ts-ignore
            modal?.close()
        })
    })
    return modal;
}

export type ModalOption = {
    title: string,
    removeAction: boolean
}

export const openAjaxModal = (url: string, modalOption: ModalOption | null = null, id : string = "modal") => {
    const modal = openModal(id);
    const modalTitle = modal?.querySelector(".modal-title");
    const modalBody = modal?.querySelector(".modal-body");
    const modalFooter = modal?.querySelector(".modal-footer");
    const title: string = modalOption?.title ?? "Modal title";

    if(modalBody == null || modalTitle == null || modalFooter == null){
        return;
    }

    modalTitle.innerHTML = title.toUpperCase();
    modalBody.innerHTML = simpleLoaderModal();
    if (!!modalOption?.removeAction) modalFooter.remove();

    axios.get(url)
        .then(res => {
        $(modalBody).html(res.data)
        })
        .then(res => {
            const event = new CustomEvent('modal.loaded');
            document.dispatchEvent(event);
        });
}