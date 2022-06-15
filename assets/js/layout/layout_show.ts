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

const loadTab = (url: string) =>   axios.request(
    {
        method: 'GET',
        params: {
            isAjax: true
        },
        url,
    }
).then(res => {
    container.innerHTML = res.data
});

links.forEach((link, key) => {
    link.addEventListener('click', (e) => {
        const a = e.target as HTMLAnchorElement
        const url = a.dataset.url;
        const name = a.dataset.name;

        if (!isText(url)) {
            return;
        }

        container.innerHTML = `
            <div class="d-flex justify-content-center align-items-center h-100">
                <i class="fas fa-circle-notch fa-spin text-primary fz-32"></i>
            </div>
        `;

        history.pushState({
            key,
            url,
        }, '', url);

        loadTab(url).then(r => {
            console.log("loadTab w/ name", name);
            if (!!name)
            {
                console.log("chaien géné:", `layout.${name}.loaded`);
                const event = new CustomEvent(`layout.${name}.loaded`);
                document.dispatchEvent(event);
            }
            const event = new CustomEvent('layout.loaded');
            document.dispatchEvent(event);
        });
    })
});

window.onpopstate = function(event) {
    loadTab(event.state?.url).then(r => r);
};

