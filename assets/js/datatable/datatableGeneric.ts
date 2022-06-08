import {$} from '../utils'
import {isText} from "../utilis/stringUtilis";

let query: string = '';
let page: number = 1;
let limitSelect = $('#datatable-limit') as HTMLSelectElement;
let limit: number = parseInt(limitSelect?.value ?? 2);
const inputSearch = $('#input-search');

type JSONResponse = {
    data: JSONDataCollection,
    filteredCount: number,
    totalCount: number,
};

type JSONData = {
    [key: string]: string
};

type JSONDataCollection = Array<JSONData>;


export default function generateDatable(table: HTMLTableElement) {
    const fetchData = async (): Promise<JSONResponse> => {
        const dataUrl = table.dataset.url;

        if (!isText(dataUrl)) {
            throw new Error("Veuillez mettre un data-url sur votre table");
        }
        const url = new URL(dataUrl, document.baseURI);

        url.searchParams.set('query', query);
        url.searchParams.set('page', page.toString());
        url.searchParams.set('limit', limit.toString());

        return await fetch(url.href).then(res => res.json())
    };

    const getData = async () => {
        const datas: JSONResponse = await fetchData();
        table.tBodies.item(0)?.remove()
        let tbody = table.createTBody();

        for (let data of datas.data) {
            let row = tbody.insertRow();

            Object.keys(data).forEach(key => {
                let cell = row.insertCell();
                cell.innerHTML = data[key];
            });
        }

        const event = new CustomEvent('datatable.loaded', {
            detail: {
                datas,
                table
            }
        });
        document.dispatchEvent(event);

        $('.pagination')?.remove()

        let ul = document.createElement('ul')
        ul.classList.add('pagination')
        for (let i = 0; i < datas.filteredCount / limit; i++) {
            let a = document.createElement('a');
            let li = document.createElement('li');
            li.appendChild(document.createTextNode((i + 1).toString()))
            a.dataset.id = (i + 1).toString()
            a.classList.add('link')
            if (page === i + 1) {
                a.classList.add('active')
            }
            a.appendChild(li);
            a.addEventListener('click', async function () {
                a.classList.add("active");
                let id = this.dataset.id;
                if (isText(id)) {
                    page = parseInt(id);
                }
                if (a.parentNode === null) {
                    return;
                }
                const nodesLink = Array.from(a.parentNode.children);
                const nodesLinkFiltered = nodesLink.filter((nodeLink) => a !== nodeLink);
                nodesLinkFiltered.forEach((nodeLinkFiltered) => {
                    nodeLinkFiltered.classList.remove("active");
                });
                await getData();
            })
            ul.appendChild(a)
        }

        table.insertAdjacentElement('afterend', ul)

    }

    getData().then(r => r);

    if (inputSearch instanceof HTMLInputElement) {
        inputSearch.addEventListener('keyup', async function () {
            query = this.value;
            await getData();
        });
    }

    if (limitSelect instanceof HTMLSelectElement) {
        limitSelect.addEventListener('change', async function () {
            limit = parseInt(this.value);
            await getData();
        });
    }
}