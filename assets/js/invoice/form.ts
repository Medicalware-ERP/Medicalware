import {initFormCollection} from "../util/form_collection";

initFormCollection();

document.addEventListener('collection.element.added', (e: Event) => {
    console.log((e as CustomEvent).detail.element)
});