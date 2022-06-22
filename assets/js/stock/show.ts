import {$, findInDataset, simpleLoader} from '../utils'
import generateDatable from "../datatable/datatableGeneric";
import {swaleWarning, swaleWarningAndRedirect} from "../util/swal";
import axios from "axios";
import {loadCurrentTab} from "../layout/layout_show";


const initCommandTab = () => {
    const table = $("#table-order")
    if (table instanceof HTMLTableElement) {
        generateDatable(table);
    }

};

const initInformationTab = () => {
    const btn = $('#askOrder') as HTMLElement;
    btn.addEventListener('click', () => {
        if (btn.getAttribute('disabled') !== null) {
            return
        }
        const url = findInDataset(btn, 'url');
        swaleWarning('Voulez-vous vraiment envoyer une demande de commande ?').then(r => {
            if (r.isConfirmed) {
                btn.innerHTML += '<i style="color: white" class="fas fa-circle-notch fa-spin fz-16"></i>';
                btn.setAttribute('disabled', '')
                axios.get(url).then(() => loadCurrentTab());
            }
        })
    })
};

document.addEventListener('layout.command.loaded', function () {
    initCommandTab();
});


document.addEventListener('layout.information.loaded', function () {
    initInformationTab()
});


document.addEventListener('DOMContentLoaded', function () {
    initCommandTab();
    initInformationTab();
});