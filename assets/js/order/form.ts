import {initFormCollection} from "../util/form_collection";
import {$, findInDataset} from "../utils";
import Routing from "../Routing";
import axios from "axios";

initFormCollection();

let provider = document.querySelector('#order_provider') as HTMLSelectElement;

const initTr = (tr : HTMLTableRowElement) => {
    const select = tr.querySelector('.equipment_select') as HTMLSelectElement;
    const callback = () => {
        const option = select.querySelector(`option[value="${select.selectedIndex}"]`);
        if (!(option instanceof HTMLOptionElement)){
            return;
        }
        const price = option.dataset.price as string;
        const spanPrice = tr.querySelector('span[data-price]') as HTMLElement;
        const spanTotal = tr.querySelector('span[data-total]') as HTMLElement;
        const qtyElement = tr.querySelector('.qty_select') as HTMLInputElement;
        qtyElement.addEventListener('change', callback)
        const qty = isNaN(parseInt(qtyElement.value)) ? 0: parseInt(qtyElement.value);

        const total = qty * parseInt(price);
        spanPrice.innerHTML = price;
        spanTotal.innerHTML = total.toString()

        const orderTotalSpan = document.querySelector('[data-total-lines]') as HTMLElement;
        const spanTotalLines = document.querySelectorAll('span[data-total]');

        let orderTotal = 0;

        spanTotalLines.forEach(span => {
            orderTotal += parseInt(span.innerHTML);
        });

        orderTotalSpan.innerHTML = orderTotal.toString();
    };

    select.addEventListener('change', callback);

    callback();
}

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
    initTr(tr);
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
    });

    provider?.addEventListener('change', () => initEquipmentSelect());

    $('.order_line', (tr: HTMLTableRowElement) => {
        initTr(tr);
    });
});