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

export const openAjaxModal = (url: string, title: string = "Header modal", id : string = "modal") => {
    const modal = openModal(id);
    const modalTitle = modal?.querySelector(".modal-title");
    const modalBody = modal?.querySelector(".modal-body");
    if(modalBody == null || modalTitle == null){
        return;
    }
    modalTitle.innerHTML = title.toUpperCase();
    modalBody.innerHTML = simpleLoaderModal();
    axios.get(url)
        .then(res => {
        $(modalBody).html(res.data)
        })
        .then(res => {
            const event = new CustomEvent('modal.loaded');
            document.dispatchEvent(event);
        });
}