import {$, simpleLoader, isText, findInDataset} from '../utils'

type JSONResponse = {
    data: JSONDataCollection,
    filteredCount: number,
    totalCount: number,
};

type JSONData = {
    [key: string]: string
};

type JSONDataCollection = Array<JSONData>;

type Filter = {
   [key: string]: {
       field: string
       condition: string
       value: string
   }
};
export default function generateDatable(table: HTMLTableElement) {
    let query: string = '';
    let page: number = 1;
    let limitSelect = $('#datatable-limit') as HTMLSelectElement;
    let limit: number = parseInt(limitSelect?.value ?? 10);
    const inputSearch = $('#input-search');
    let dataFilter: Filter = {};

    const filtersId = table.dataset.filters;
    if (isText(filtersId)) {
        const filters = document.querySelector(filtersId);
        if (filters instanceof HTMLFormElement) {
            const formFilters = new FormData(filters);

            formFilters.forEach((value, key) => {
                const input = filters.querySelector(`[name="${key}"]`);

                if (!(input instanceof HTMLElement)) {
                    throw new Error('form field (input, select, ...) not found')
                }
                dataFilter[key] = {
                    field: findInDataset(input, 'field'),
                    condition: findInDataset(input, 'condition'),
                    value: value as string
                };
            });
        }
    }


    const fetchData = async (): Promise<JSONResponse> => {
        const dataUrl = table.dataset.url;

        if (!isText(dataUrl)) {
            throw new Error("Veuillez mettre un data-url sur votre table");
        }
        const url = new URL(dataUrl, document.baseURI);

        url.searchParams.set('query', query);
        url.searchParams.set('page', page.toString());
        url.searchParams.set('limit', limit.toString());
        url.searchParams.set('filters', JSON.stringify(dataFilter));

        return await fetch(url.href).then(res => res.json())
    };

    const insertCenterCellData = (text: string) => {
        table.tBodies.item(0)?.remove()
        const nbCol = table.tHead?.children.item(0)?.children.length;
        let tbody = table.createTBody();
        let row = tbody.insertRow();
        let cell =row.insertCell();
        cell.setAttribute('colspan', String(nbCol))
        cell.style.textAlign = 'center';
        cell.innerHTML = text;
    }

    const getData = async () => {
        insertCenterCellData(simpleLoader());

        const datas: JSONResponse = await fetchData();
        table.tBodies.item(0)?.remove();

        if (datas.data.length > 0) {
            let tbody = table.createTBody();
            for (let data of datas.data) {
                let row = tbody.insertRow();

                Object.keys(data).forEach(key => {
                    let cell = row.insertCell();
                    cell.innerHTML = data[key];
                });
            }
        } else {
            insertCenterCellData('Aucunes données');
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
        let debounceGeneralSearch: number | undefined = undefined;

        inputSearch.addEventListener('keyup', function () {
            query = this.value;

            window.clearTimeout(debounceGeneralSearch);
            debounceGeneralSearch = window.setTimeout(
                async () => {
                    await getData()
                },
                300
            );
        });
    }

    if (limitSelect instanceof HTMLSelectElement) {
        limitSelect.addEventListener('change', async function () {
            limit = parseInt(this.value);
            await getData();
        });
    }
}