import {$} from '../utils'
import Routing from "../Routing";
import generateDatable from "../datatable/datatableGeneric";
import {swaleWarning} from "../utilis/swal";

const table = $("#table-users")

const toggleActive = () => {

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

document.addEventListener('datatable.loaded', toggleActive);

if (table instanceof HTMLTableElement) {
    generateDatable(table);
}
