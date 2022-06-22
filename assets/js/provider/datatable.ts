import {$, findInDataset} from '../utils'
import generateDatable from "../datatable/datatableGeneric";
import {swaleDangerAndRedirect} from "../util/swal";

const table = $("#table-provider")


if (table instanceof HTMLTableElement) {
    generateDatable(table);
}

document.addEventListener('datatable.loaded', () => {
    $('[data-remove-url]', (a : HTMLAnchorElement) => {
        a.addEventListener('click', () => {
            const url = findInDataset(a, 'removeUrl');

            swaleDangerAndRedirect('Vous êtes sur le point de supprimer ce fournisseur !', url).then(r => r);
        })
    })
})