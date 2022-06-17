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

export const openAjaxModal = (url: string, id : string = "modal") => {
    const modal = openModal(id);
    const modalBody = modal?.querySelector(".modal-body");
    if(modalBody == null){
        return;
    }
    modalBody.innerHTML = simpleLoaderModal();
    axios.get(url).then(res => {
        let myDiv = htmlToElement(res.data);
        let arr = myDiv.getElementsByTagName('script')

        for (let n = 0; n < arr.length; n++){
            const scriptEl = document.createRange().createContextualFragment(arr[n].textContent as string);
            document.body.append(scriptEl);
        }

        modalBody.appendChild(myDiv);

    });
}