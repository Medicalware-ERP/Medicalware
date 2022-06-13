import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";

const table = $("#table-provider")


if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
