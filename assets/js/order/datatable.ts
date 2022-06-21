import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";

const table = $("#table-order")


if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
