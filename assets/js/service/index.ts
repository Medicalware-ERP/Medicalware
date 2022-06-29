import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";
import {ModalOption, openAjaxModal} from "../util/modal";
import Routing from "../Routing";
import {swaleDangerAndRedirect, swaleWarning} from "../util/swal";

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

const toArchived = () => {
    const callback = (e: Event) => {
        const link = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver un service."
        const url = Routing.generate('app_to_archive_service', {
            id: link.dataset.service
        });

        swaleWarning(text).then(r => {
            if (r.isConfirmed) {
                fetch(url).then(() => {
                    if (table instanceof HTMLTableElement) {
                        generateDatable(table);
                    }
                });
            }
        })
    };

    $(".btn-danger[data-service]", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener("datatable.loaded", callback);
document.addEventListener("datatable.loaded", toArchived);


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