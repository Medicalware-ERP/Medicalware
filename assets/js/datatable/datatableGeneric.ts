import {$} from '../utils'
import {isText} from "../utilis/stringUtilis";

let query: string = '';
let page: number = 1;
const inputSearch = $('#input-search');

type JSONData = {
    [key: string]: string
};

type JSONDataCollection = Array<JSONData>;


export default function generateDatable(table: HTMLTableElement, callback: any = null) {
    const fetchData = async (): Promise<JSONDataCollection> => {
        const dataUrl = table.dataset.url;

        if (!isText(dataUrl)) {
            throw new Error("Veuillez mettre un data-url sur votre table");
        }
        const url = new URL(dataUrl, document.baseURI);

        url.searchParams.set('query', query);
        url.searchParams.set('page', page.toString());

        return await fetch(url.href).then(res => res.json())
    };

    const getData = async () => {
        const datas: JSONDataCollection = await fetchData();

        table.tBodies.item(0)?.remove()
        let tbody = table.createTBody();

        for (let data of datas) {
            let row = tbody.insertRow();

            Object.keys(data).forEach(key => {
                let cell = row.insertCell();
                cell.innerHTML = data[key];
            });
        }

        if (callback != null) {
            callback();
        }
    }

    getData().then(r => r);

    const allLink = (link: HTMLElement) => {
        const id = link.dataset.id;

        if (isText(id) && parseInt(id) === 1) {
            link.classList.add("active");
        }

        link.addEventListener("click", async function () {
            link.classList.add("active");
            let id = this.dataset.id;
            if (isText(id)) {
                page = parseInt(id);
            }
            if (link.parentNode === null) {
                return;
            }
            const nodesLink = Array.from(link.parentNode.children);
            const nodesLinkFiltered = nodesLink.filter((nodeLink) => link !== nodeLink);
            nodesLinkFiltered.forEach((nodeLinkFiltered) => {
                nodeLinkFiltered.classList.remove("active");
            });
            await getData();
        })
    };

    $(".pagination .link", allLink)

    if (inputSearch instanceof HTMLInputElement) {
        inputSearch.addEventListener('keyup', async function () {
            query = this.value;
            await getData();
        });
    }
}