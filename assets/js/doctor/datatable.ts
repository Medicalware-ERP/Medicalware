import {$, findInDataset} from '../utils'
import Routing from "../Routing";
import generateDatable from "../datatable/datatableGeneric";
import {swaleDanger, swaleWarning} from "../util/swal";
import { toggleActive} from "../human_resources/datatable";
import axios from "axios";

const table = $("#table-doctors")

document.addEventListener('datatable.loaded', () => {
    toggleActive();
    if (table instanceof HTMLTableElement) {
        $('[data-delete]', (btn: HTMLButtonElement) => {
            const url = findInDataset(btn, 'delete');

            btn.addEventListener('click', () => {
                swaleDanger('Vous êtes sûr le point de supprimer cet utilisateur').then(r => {
                    if (r.isConfirmed) {
                        axios.get(url).then(r => {

                            generateDatable(table);
                        })
                    }
                })
            })
        });
    }
});

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
