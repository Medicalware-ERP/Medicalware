import Swal from 'sweetalert2'
import { $, colors } from '../utils'
import Routing from "../Routing";
import generateDatable from "../datatable/datatableGeneric";

const toggleActive = ()  => {
    const callback = (e: Event) => {
        let checkbox = <HTMLInputElement> e.target;
        const url = Routing.generate('app_toggle_active_user', {
            id: checkbox.dataset.user
        });

        if (checkbox.checked) {
            Swal.fire({
                title: 'Êtes vous sûr de continuer ?',
                text: "Vous êtes sur le point d'activer l'accés au compte pour l'utilisateur",
                icon: 'warning',
                confirmButtonText: 'Oui',
                confirmButtonColor: colors.primary,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                cancelButtonColor: colors.secondary
            }).then(r => {
                if (r.isConfirmed) {
                    location.href = url
                }
            })
        } else {
            Swal.fire({
                title: 'Êtes vous sûr de continuer ?',
                text: "Vous êtes sur le point de désactiver l'accés au compte pour l'utilisateur",
                icon: 'warning',
                confirmButtonText: 'Oui',
                confirmButtonColor: colors.primary,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                cancelButtonColor: colors.secondary
            }).then(r => {
                if (r.isConfirmed) {
                    location.href = url
                }
            })
        }
    };

    $(".switch [type=checkbox]", (elem: Node) => {
        elem.addEventListener('click', callback)
    })
}

const table = $("#table-users")

if (table instanceof HTMLTableElement) {
    generateDatable(table, toggleActive);
}
