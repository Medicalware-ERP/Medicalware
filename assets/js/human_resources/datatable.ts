import {$, findInDataset} from '../utils'
import Routing from "../Routing";
import generateDatable from "../datatable/datatableGeneric";
import {swaleDanger, swaleDangerAndRedirect, swaleWarning} from "../util/swal";
import axios from "axios";

const table = $("#table-users")

export const toggleActive = () => {

    const callback = (e: Event) => {
        const checkbox = <HTMLInputElement>e.target;
        const url = Routing.generate('app_toggle_active_user', {
            id: checkbox.dataset.user
        });

        let text = checkbox.checked ?
            "Vous êtes sur le point d'activer l'accés au compte pour l'utilisateur" :
            "Vous êtes sur le point de désactiver l'accés au compte pour l'utilisateur"
        ;

        swaleWarning(text).then(r => {
            if (r.isConfirmed) {
                fetch(url).then(() => {
                    if (table instanceof HTMLTableElement) {
                        generateDatable(table);
                    }
                });
            } else {
                checkbox.checked = !checkbox.checked;
            }
        })
    };

    $(".switch [type=checkbox]", (elem: Node) => {
        elem.addEventListener('click', callback)
    });
}

document.addEventListener('datatable.loaded', () => {
    toggleActive();
    if (table instanceof HTMLTableElement) {
        $('[data-delete]', (btn: HTMLButtonElement) => {
            const url = findInDataset(btn, 'delete');

            btn.addEventListener('click', () => {
                swaleDanger('Vous êtes sûr le point de supprimer cet utilisateur').then(r => {
                    if (r.isConfirmed) {
                        axios.get(url).then(r => {

                            generateDatable(table);
                        })
                    }
                })
            })
        });
    }
});

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
