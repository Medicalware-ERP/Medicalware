import {initFormCollection} from "../util/form_collection";
import {$} from "../utils";

initFormCollection();

document.addEventListener('collection.element.added', (e: Event) => {
    const tr = (e as CustomEvent).detail.element as HTMLTableRowElement;
    console.log(tr.querySelector('.qty_element'))

    tr.querySelector('.qty_element')?.addEventListener('change', function () {
        initTotalInvoice();
    });

    tr.querySelector('.price_element')?.addEventListener('change', function () {
        initTotalInvoice();
    });
});


const initTotalInvoice = () => {
    const orderTotalSpan = document.querySelector('[data-total-lines]') as HTMLElement;
    const spanTotalLines = document.querySelectorAll('span[data-total]');

    let orderTotal = 0;

    spanTotalLines.forEach(span => {
        orderTotal += parseFloat(span.innerHTML);
    });

    orderTotalSpan.innerHTML = orderTotal.toFixed(2).toString();
};

const initInputListener = () => {
    $('.qty_element', function (elem: HTMLElement) {
        elem.addEventListener('change', function () {
            initTotalInvoice();
        });
    });

    $('.price_element', function (elem: HTMLElement) {
        elem.addEventListener('change', function () {
            initTotalInvoice();
        });
    });
};

document.addEventListener('DOMContentLoaded', function () {
    initTotalInvoice();
    initInputListener();
});