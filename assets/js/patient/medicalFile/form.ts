import {findInDataset} from "../../utils";


const initServiceSelect = (tr: HTMLTableRowElement) => {
    const select = tr.querySelector('.doctor__service') as HTMLSelectElement;
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
    console.log("add")
});

document.addEventListener( 'layout.medical_file.loaded', (e: Event) => {
    const tr = (e as CustomEvent).detail.element as HTMLTableRowElement;
    initServiceSelect(tr);
    console.log("loaded")
});

document.addEventListener( 'DOMContentLoaded', (e: Event) => {
    const tr = (e as CustomEvent).detail.element as HTMLTableRowElement;
    initServiceSelect(tr);
    console.log("loadedinit")
});