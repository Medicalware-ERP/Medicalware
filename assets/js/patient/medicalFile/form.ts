import {$, findInDataset} from "../../utils";

const initServiceSelect = (tr: HTMLTableRowElement) => {
    const select = tr.querySelector('.doctor__service') as HTMLSelectElement;
    console.log(tr, select)
    const callback = () => {
        const span = tr.querySelector('.doctor_service');
        const optionSelected = select.options[select.selectedIndex];
        if(span == null){
            return;
        }
        span.innerHTML = findInDataset(optionSelected, 'serviceName');
    }
    callback();
    select.addEventListener("change", () => {
        callback();
    })
}

document.addEventListener('collection.element.added', (e: Event) => {
    const tr = (e as CustomEvent).detail.element as HTMLTableRowElement;
    initServiceSelect(tr);
});

document.addEventListener('DOMContentLoaded', (e: Event) => {
    $(".table_medical_file_line tbody tr", (tr: HTMLTableRowElement) => {
        initServiceSelect(tr);
    });
});


