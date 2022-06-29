import {$, findInDataset} from '../utils'
import generateDatable from "../datatable/datatableGeneric";
import {declareCalendar} from "../util/planning";


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

const initEquipmentPlanning = () => {
    const providerPlanning = $("#provider-show-planning") as HTMLElement;

    if (!!providerPlanning) {
        const providerId: number = parseInt(findInDataset(providerPlanning, "providerId"));
        declareCalendar("provider-show-planning", providerId, "App\\Entity\\Provider");
    }
};

document.addEventListener('layout.command.loaded', function () {
    initCommandTab();
});

document.addEventListener('layout.equipment.loaded', function () {
    initEquipmentTab();
});

document.addEventListener('layout.provider.planning.loaded', function () {
    initEquipmentPlanning();
});

document.addEventListener('DOMContentLoaded', function () {
    initCommandTab();
    initEquipmentTab();
    initEquipmentPlanning();
});