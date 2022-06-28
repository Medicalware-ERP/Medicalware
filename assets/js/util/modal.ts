import {simpleLoaderModal} from "../utils";
import axios from "axios";
import $ from "jquery";

export const openModal = (id : string = "modal", ajaxSubmit = false) => {
    const modal:HTMLElement|null = document.querySelector(`#${id}`);
    if(modal == null){
        return;
    }

    // @ts-ignore
    modal?.showModal();
    const addButton = modal.querySelector("#submit__form");
    addButton?.addEventListener("click", () => {
        const form = modal.querySelector(".modal-content form") as HTMLFormElement;

        if (ajaxSubmit) {
            const modalBody = modal.querySelector(".modal-body") as HTMLFormElement;
            const formData = new FormData(form);
            axios.post(form.action, formData).then(res => {
                closeAjaxModal(id)
            }).catch(res => {
                modalBody.innerHTML = res.response.data
            })
            ;
        } else {
            form?.submit();
        }
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
    ajaxSubmit?: boolean
}

export const openAjaxModal = (url: string, modalOption: ModalOption | null = null, id : string = "modal") => {
    const modal = openModal(id, modalOption?.ajaxSubmit ?? false);
    const modalTitle = modal?.querySelector(".modal-title");
    const modalBody = modal?.querySelector(".modal-body");
    const modalFooter = modal?.querySelector(".modal-footer");
    const title: string = modalOption?.title ?? "Modal title";

    if(modalBody == null || modalTitle == null || modalFooter == null){
        return;
    }

    modalTitle.innerHTML = title.toUpperCase();
    modalBody.innerHTML = simpleLoaderModal();
    if (!!modalOption?.removeAction) modalFooter.classList.add("d-none");
    else modalFooter.classList.remove("d-none");

    axios.get(url)
        .then(res => {
        $(modalBody).html(res.data)
        })
        .then(res => {
            const event = new CustomEvent('modal.loaded');
            document.dispatchEvent(event);
        });
}

export const closeAjaxModal = (id : string = "modal") => {
    const modal:HTMLElement|null = document.querySelector(`#${id}`);
    if(modal == null){
        return;
    }

    // @ts-ignore
    modal?.close();
}