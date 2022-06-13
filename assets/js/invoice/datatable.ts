import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";

const table = $("#table-invoice")


if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
