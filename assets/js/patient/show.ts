import {$, findInDataset} from '../utils'
import Routing from "../Routing";
import axios from "axios";
import {initFormCollection} from "../util/form_collection";
import generateDatable from "../datatable/datatableGeneric";
import {declareCalendar} from "../util/planning";

const initMedicalFileTab = () => {
    initFormCollection();
    $("[data-element-remove]", (deleteBtn: HTMLElement) => {
        deleteBtn.addEventListener('click', () => {
            const id = parseInt(findInDataset(deleteBtn, "elementId"));

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
    initPatientCalendar();
});

document.addEventListener( 'DOMContentLoaded', () => {
    initMedicalFileTab();
    initInvoiceTab();
    initPatientCalendar();

    const fileInput = $('#file_form')?.querySelector('[type="file"]');
    const removeImageBtn = $("#removeImage");
    const nameAvatar    = $("#name_avatar");
    const errorContainer    = $("#error-container") as HTMLElement;

    if (!(fileInput instanceof HTMLInputElement)) {
        throw new Error('file input not found');
    }

    if (!(removeImageBtn instanceof HTMLButtonElement)) {
        throw new Error('remove btn not found');
    }

    if (!(nameAvatar instanceof HTMLElement)) {
        throw new Error('file input not found');
    }

    $("#uploadImage")?.addEventListener('click', e => {
        fileInput.click();
    });


    fileInput.addEventListener('change', () => {
        const file = (fileInput.files as FileList)[0];
        const form =  $('#file_form')?.querySelector('form') as HTMLFormElement;
        const formData = new FormData(form);
        const url      = form.action;

        const img = document.getElementById("img_avatar") as HTMLImageElement;
        const error = document.getElementById("error-avatar") as HTMLParagraphElement;
        errorContainer.classList.add('d-none');
        img.classList.remove('border-danger')

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then((e) => {
            img.classList.remove('d-none')
            removeImageBtn.classList.remove('d-none');
            nameAvatar.classList.add('d-none');
            const fr = new FileReader();
            fr.onload = function () {
                img.src = fr.result as string;
            }
            fr.readAsDataURL(file);
        }).catch(() => {
            errorContainer.classList.remove('d-none');
            img.classList.add('border-danger');
            nameAvatar.classList.add('border-danger');
        })
    });



    removeImageBtn.addEventListener('click', () => {
        const url = findInDataset(removeImageBtn, 'url');
        location.href = url;
    });
});