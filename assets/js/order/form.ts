import {initFormCollection} from "../util/form_collection";
import {$, findInDataset} from "../utils";
import Routing from "../Routing";
import axios from "axios";

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

    $('[data-element-remove-id]', (btn: HTMLElement) => {
        btn.addEventListener('click', e => {
            e.stopPropagation();

            const addToCollectionBtn = document.querySelector('[data-collection-id]');
            if (!(addToCollectionBtn instanceof HTMLElement)) {
                return;
            }

            const collectionId = findInDataset(addToCollectionBtn, 'collectionId');

            const collection = document.getElementById(collectionId);
            if (!(collection instanceof HTMLElement)) {
                throw new Error('Collection not found for id ' + collectionId);
            }

            let btn = e.currentTarget as HTMLElement;
            const elementId = findInDataset(btn, 'elementRemove');
            document.getElementById(elementId)?.remove();
            let couter = parseInt(findInDataset(collection, 'widgetCounter'));
            couter--;
            collection.dataset.widgetCounter = couter.toString();

            const  id =  findInDataset(btn, 'elementRemoveId');
            const url = Routing.generate('order_delete_line', {id})
            axios.get(url).then(r => r);
        });
    })
});


provider?.addEventListener('change', () => initEquipmentSelect());