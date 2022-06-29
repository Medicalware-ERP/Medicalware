import {$} from '../utils'
import Routing from "../Routing";
import generateDatable from "../datatable/datatableGeneric";
import {swaleWarning} from "../util/swal";
import { toggleActive} from "../human_resources/datatable";

const table = $("#table-doctors")

document.addEventListener('datatable.loaded', toggleActive);

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
