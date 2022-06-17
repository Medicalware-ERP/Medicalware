import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";
import Routing from "../Routing";
import {swaleWarning} from "../util/swal";

const table = $("#table-patients")

const toArchived = () => {

    const callback = (e: Event) => {
        const link = <HTMLInputElement>e.currentTarget;
        const text: string = "Vous êtes sur le point d'archiver un patient."
        const url = Routing.generate('app_to_archive_patient', {
            id: link.dataset.patient
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

    $(".btn-xs[data-patient]", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('datatable.loaded', toArchived);

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
