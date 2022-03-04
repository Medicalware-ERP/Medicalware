import {$} from '../utils.js'

// SORT ROWS DATATABLE
const sortDataTableByColumn = (table, column, asc = true) => {
    const order = asc ? 1 : -1;
    const tBody = table.tBodies[0];
    const rows = Array.from(tBody.querySelectorAll("tr"));
    console.log(rows);

    // Trier les lignes
    const sortedRows = rows.sort((a, b) => {
        // Column + 1 car les enfants commencent à l'index 1
        const aColumnText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
        const bColumnText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim();

        console.log(aColumnText, bColumnText)
    });
}

sortDataTableByColumn(document.querySelector(".table-users"),1,true);

// SEARCH DATATABLE
const inputSearch = $("#input-search");
const rows = document.querySelectorAll(".table-users tbody tr");
const selectSearch = $("#show-search-options");
const selectedValueSearchDefault = selectSearch.options[selectSearch.selectedIndex].value;

const searchOnKeyUp = (selectedValue) => {
    inputSearch.addEventListener("keyup", (e) => {
        const query = e.target.value.toLowerCase();
        rows.forEach((row) => {
            row.querySelector(`.${selectedValue}`).textContent.toLowerCase().includes(query) ? (row.style.display = "table-row")
                : (row.style.display = "none");
        })
    })
}

// Cherche avec la valeur par défaut
searchOnKeyUp(selectedValueSearchDefault);

// Si on choisit une option manuellement on attache un event onchange
selectSearch.addEventListener("change", (e) => {
    const selectedValue = e.currentTarget.value;
    searchOnKeyUp(selectedValue);
})