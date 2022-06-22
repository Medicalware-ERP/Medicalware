import {initFormCollection} from "../util/form_collection";
import {$, findInDataset} from "../utils";

initFormCollection();

let provider = document.querySelector('#order_provider') as HTMLSelectElement;

const initEquipmentSelect = (removeSelected = true, customElement : HTMLElement|null = null) => {
    const providerId = provider.value;
    const callback = (option: HTMLOptionElement) => {
        const id = findInDataset(option, 'providerId');

        if (removeSelected) {
            // @ts-ignore
            option.parentElement?.selectedIndex = -1;
        }

        if (providerId !== id) {
            option.style.display = 'none'
        } else {
            option.style.display = 'block'
        }
    };

    if (customElement instanceof HTMLElement) {
        customElement.querySelectorAll('[data-provider-id]').forEach(e => {
            callback(e as HTMLOptionElement)
        })
        return;
    }

    $('[data-provider-id]', callback)
};

document.addEventListener('collection.element.added', (e: Event) => {
    const tr = (e as CustomEvent).detail.element as HTMLTableRowElement;
    initEquipmentSelect(true, tr);
});

document.addEventListener('DOMContentLoaded', (e: Event) => {
    initEquipmentSelect(false);
});


provider?.addEventListener('change', () => initEquipmentSelect());