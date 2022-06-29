import {initFormCollection} from "../util/form_collection";
import {$, findInDataset} from "../utils";
import Routing from "../Routing";
import axios from "axios";

initFormCollection();

document.addEventListener('collection.element.added', (e: Event) => {
    const tr = (e as CustomEvent).detail.element as HTMLTableRowElement;

    tr.querySelector('.qty_element')?.addEventListener('change', function () {
        initTotalInvoice();
    });

    tr.querySelector('.price_element')?.addEventListener('change', function () {
        initTotalInvoice();
    });
});

document.addEventListener('collection.element.removed', (e: Event) => {
    initTotalInvoice();
});


const initTotalInvoice = () => {
    const orderTotalSpan = document.querySelector('[data-total-lines]') as HTMLElement;
    const spanTotalLines = document.querySelectorAll('span[data-total]');
    const tvaElement     = document.querySelector('#invoice_tva') as HTMLSelectElement;
    const tva            =  parseFloat(tvaElement.options[tvaElement.selectedIndex].dataset.tva as string);
    const tvaDisplay     =  tvaElement.options[tvaElement.selectedIndex].dataset.tvaDisplay as string;
    const tvaResult      = document.querySelector('[data-total-tva]') as HTMLElement;
    const ttcResult      = document.querySelector('[data-total-ttc]')  as HTMLElement;

    let orderTotal = 0;

    spanTotalLines.forEach(span => {
        orderTotal += parseFloat(span.innerHTML);
    });

    orderTotalSpan.innerHTML = orderTotal.toFixed(2).toString();
    tvaResult.innerHTML = tvaDisplay;
    ttcResult.innerHTML = (orderTotal * tva).toFixed(2).toString();
};

const initInputListener = () => {
    $('.qty_element', function (elem: HTMLElement) {
        elem.addEventListener('change', function () {
            initTotalInvoice();
        });
    });

    $('#invoice_tva')?.addEventListener('change', initTotalInvoice);

    $('.price_element', function (elem: HTMLElement) {
        elem.addEventListener('change', function () {
            initTotalInvoice();
        });
    });
};

document.addEventListener('DOMContentLoaded', function () {
    initTotalInvoice();
    initInputListener();

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
            const url = Routing.generate('invoice_delete_line', {id})
            axios.get(url).then(() => initTotalInvoice());
        });
    });
});