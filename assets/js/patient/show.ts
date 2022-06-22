import {$, findInDataset} from '../utils'
import Routing from "../Routing";
import axios from "axios";
import {loadCurrentTab} from "../layout/layout_show";
import {initFormCollection} from "../util/form_collection";

const initMedicalFileTab = () => {
    initFormCollection();
    $("[data-element-remove]", (deleteBtn: HTMLElement) => {
        console.log(deleteBtn);
        deleteBtn.addEventListener('click', () => {
            const id = parseInt(findInDataset(deleteBtn, "elementId"));
            console.log(id);
            if(id === 0){
                return;
            }
            const url = Routing.generate("medical_file_line_delete", {
                id
            });
            axios.get(url).then(() => {

                loadCurrentTab()
            })
        })
    });
}

document.addEventListener( 'layout.medical_file.loaded', (e) => {
    initMedicalFileTab();
})
document.addEventListener( 'DOMContentLoaded', (e) => {
    initMedicalFileTab();
})