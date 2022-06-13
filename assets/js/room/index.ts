import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";
import Routing from "../Routing";
import {swaleWarning} from "../util/swal";

const table = $("#table-rooms");

const deleteRoom = () => {

    const callback = (e: Event) => {
        e.stopPropagation();

        const button = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point de supprimer une salle."
        const url = Routing.generate('app_delete_room', {
            id: button.dataset.room
        });

        swaleWarning(text).then(r => {
            fetch(url).then(() => {
                if (table instanceof HTMLTableElement) {
                    generateDatable(table);
                }
            });
        });
    };

    $(".btn-delete-room", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('datatable.loaded', deleteRoom);

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}