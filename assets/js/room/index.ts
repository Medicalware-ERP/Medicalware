import generateDatable from "../datatable/datatableGeneric";
import {$} from "../utils";

const table = $("#table-rooms");

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}