import Swal from 'sweetalert2'
import { $, colors } from '../utils'
import Routing from "../Routing";


const callback = e => {
    const url = Routing.generate('app_toggle_active_user', {
        id: e.target.dataset.user
    });

    if (e.checked) {
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
    } else {
        Swal.fire({
            title: 'Êtes vous sûr de continuer ?',
            text: "Vous êtes sur le point de d'activer l'accés au compte pour l'utilisateur",
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

$(".switch [type=checkbox]", (elem) => {
   elem.addEventListener('click', callback)
})
