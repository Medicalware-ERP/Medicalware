import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";
import {ModalOption, openAjaxModal} from "../util/modal";
import Routing from "../Routing";
import {swaleDangerAndRedirect} from "../util/swal";

const addButton = document.querySelector("#add_service");

const callback = () => {
    $(".edit_service", (btn : HTMLElement) => {
        btn.addEventListener("click", (e:Event) => {
            const button = <HTMLInputElement>e.currentTarget
            const url = Routing.generate("edit_enum",{
                class : "App\\Entity\\Service",
                id: button?.dataset.service
            });

            const modalOption: ModalOption = {
                title: "Editer un service",
                removeAction: false
            }

            openAjaxModal(url,modalOption);
        })
    });
}

const callBackDeleteService = () => {
    $(".delete_service", (btn : HTMLElement) => {
        btn.addEventListener("click", (e:Event) => {
            const button = <HTMLInputElement>e.currentTarget
            const text: string = "Vous êtes sur le point de supprimer un service."
            const url = Routing.generate("service_delete",{
                class : "App\\Entity\\Service",
                id: button?.dataset.service
            });
            swaleDangerAndRedirect(text, url);
        })
    });
}

document.addEventListener("datatable.loaded", callback);
document.addEventListener("datatable.loaded", callBackDeleteService);


addButton?.addEventListener("click", () => {
    const url = Routing.generate("add_enum",{
        class : "App\\Entity\\Service"
    });

    const modalOption: ModalOption = {
        title: "Ajouter un service",
        removeAction: false
    }

    openAjaxModal(url, modalOption);
})

const table = $("#table-services")

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}