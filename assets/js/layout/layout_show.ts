import {simpleLoader, isText} from "../utils";
import axios from "axios";

const links = document.querySelectorAll('nav button');
const container = document.querySelector('#content_container');

if (!(container instanceof HTMLElement)) {
    throw new Error('container not found for id #content_container');
}

if (links.length === 0) {
    throw new Error('No links found');
}

const initLinkButton = (link: Element) => {
    link.classList.add("active");
    if (!(link.parentNode instanceof HTMLElement)) {
        throw new Error('link not found');
    }

    const nodesLink = Array.from(link.parentNode.children);
    const nodesLinkFiltered = nodesLink.filter((nodeLink) => link !== nodeLink);
    nodesLinkFiltered.forEach((nodeLinkFiltered) => {
        nodeLinkFiltered.classList.remove("active");
    });
}

const loadTab = (url: string) =>  {
    const link = document.querySelector(`nav button[data-url='${url}']`)
    if(!(link instanceof HTMLElement)) {
        throw new Error('No link found');
    }
    const name = link.dataset.name;
    initLinkButton(link);

    container.innerHTML = `
            <div class="d-flex justify-content-center align-items-center h-100">
                <i class="fas fa-circle-notch fa-spin text-primary fz-32"></i>
            </div>
        `;

    return axios.request(
        {
            method: 'GET',
            params: {
                isAjax: true
            },
            url,
        }
    ).then(res => {
        container.innerHTML = res.data
    }).then(r => {
        if (!!name)
        {
            const event = new CustomEvent(`layout.${name}.loaded`);
            document.dispatchEvent(event);
        }
        const event = new CustomEvent('layout.loaded');
        document.dispatchEvent(event);
    });
}

links.forEach((link, key) => {
    link.addEventListener('click', (e) => {
        e.stopPropagation()
        const a = e.currentTarget as HTMLAnchorElement
        const url = a.dataset.url;

        if (!isText(url)) {
            return;
        }

        history.pushState({
            key,
            url
        }, '', url);

        loadTab(url);
    })
});

window.onpopstate = function() {
    loadCurrentTab();
};

// A l'arrivé sur une tab, on la sélectionne
document.addEventListener('DOMContentLoaded', () => {
    const link: HTMLElement | null = document.querySelector(`nav button[data-url="${location.pathname}"]`);

    if (link instanceof HTMLElement)
        initLinkButton(link);
});

export const loadCurrentTab = () => {
    loadTab(location.pathname).then(
        () => window.history.replaceState(null,"",location.pathname)
    );
}

// @ts-ignore
window.loadTab = loadCurrentTab;