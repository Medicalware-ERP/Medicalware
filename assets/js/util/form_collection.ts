import {findInDataset, htmlToElement} from "../utils";

export const initFormCollection = () => {
    const addToCollectionBtn = document.querySelector('[data-collection-id]');

    if (!(addToCollectionBtn instanceof HTMLElement)) {
        return;
    }

    addToCollectionBtn.addEventListener('click', e => {
        const collectionId = findInDataset(addToCollectionBtn, 'collectionId');

        const collection = document.getElementById(collectionId);
        if (!(collection instanceof HTMLElement)) {
            throw new Error('Collection not found for id ' + collectionId);
        }

        let couter = parseInt(findInDataset(collection, 'widgetCounter'));

        const prototype = findInDataset(collection, 'prototype');

        let element = prototype.replace(/__name__/g, couter.toString())

        couter++;

        collection.dataset.widgetCounter = couter.toString();

        const html = htmlToElement(element);

        html.querySelector('[data-element-remove]')?.addEventListener('click', e => {
            e.stopPropagation();
            let btn = e.currentTarget as HTMLElement;
            const elementId = findInDataset(btn, 'elementRemove');
            document.getElementById(elementId)?.remove();
            const event = new CustomEvent('collection.element.removed');
            document.dispatchEvent(event);
            let couter = parseInt(findInDataset(collection, 'widgetCounter'));
            couter--;
            collection.dataset.widgetCounter = couter.toString();
        });

        const event = new CustomEvent('collection.element.added', {
            detail: {element: html, collection}
        });
        document.dispatchEvent(event);

        collection.appendChild(html);

    })
}