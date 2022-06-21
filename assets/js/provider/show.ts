import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";


const initCommandTab = () => {
    const table = $("#table-order")
    if (table instanceof HTMLTableElement) {
        generateDatable(table);
    }
};

const initEquipmentTab = () => {
    const table = $("#table-stock")

    if (table instanceof HTMLTableElement) {
        generateDatable(table);
    }
};

document.addEventListener('layout.command.loaded', function () {
    initCommandTab();
});

document.addEventListener('layout.equipment.loaded', function () {
    initEquipmentTab();
});

document.addEventListener('DOMContentLoaded', function () {
    initCommandTab();
    initEquipmentTab();
});