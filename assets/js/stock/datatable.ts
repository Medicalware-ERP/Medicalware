import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";

const table = $("#table-stock")


if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
