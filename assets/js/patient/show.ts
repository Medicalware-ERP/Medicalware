import {$, findInDataset} from '../utils'
import Routing from "../Routing";
import axios from "axios";
import {loadCurrentTab} from "../layout/layout_show";
import {initFormCollection} from "../util/form_collection";
import generateDatable from "../datatable/datatableGeneric";
import {declareCalendar} from "../util/planning";

const initMedicalFileTab = () => {
    initFormCollection();
    $("[data-element-remove]", (deleteBtn: HTMLElement) => {
        deleteBtn.addEventListener('click', () => {
            const id = parseInt(findInDataset(deleteBtn, "elementId"));
            console.log(id);
            if(id === 0){
                return;
            }
            const url = Routing.generate("medical_file_line_delete", {
                id
            });
            axios.get(url).then(() => {
                deleteBtn.parentElement?.parentElement?.remove();
            });
        });
    });
};

const initInvoiceTab = () => {
    const table = $("#table-invoice")
    if (table instanceof HTMLTableElement) {
        generateDatable(table);
    }
};

const initPatientCalendar = () => {
    const patientIdEl = $("#patient-show-planning");
    if(patientIdEl == null){
        return;
    }
    const patientId: number = parseInt(findInDataset(patientIdEl as HTMLElement, "patientId"));

    declareCalendar("patient-show-planning", patientId, "App\\Entity\\Patient");
};

document.addEventListener( 'layout.medical_file.loaded', () => {
    initMedicalFileTab();
});

document.addEventListener( 'layout.invoice.loaded', () => {
    initInvoiceTab();
});

document.addEventListener( 'layout.patient_show_planning.loaded', () => {
    console.log("dam patient show claendar")
    initPatientCalendar();
});

document.addEventListener( 'DOMContentLoaded', () => {
    initMedicalFileTab();
    initInvoiceTab();
    initPatientCalendar();
});