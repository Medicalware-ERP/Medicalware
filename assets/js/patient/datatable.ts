import {$} from '../utils'
import generateDatable from "../datatable/datatableGeneric";

const table = $("#table-patients")


if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
