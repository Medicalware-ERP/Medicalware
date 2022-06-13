
document.addEventListener('collection.element.added', (e: Event) => {
    console.log((e as CustomEvent).detail.element)
});