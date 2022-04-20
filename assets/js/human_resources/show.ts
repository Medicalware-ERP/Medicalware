import Swal from 'sweetalert2'
import { $, colors } from '../utils'
import Routing from "../Routing";

let userIdElement = $('#user-id') as HTMLInputElement;

const url = Routing.generate('app_toggle_active_user', {
    id: userIdElement.dataset.user
});

const userDisable = $("#user-disable");
const userEnable  = $("#user-enable");

if (userDisable instanceof HTMLInputElement) {
    userDisable.addEventListener('click', function () {
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
    });
}

if (userEnable  instanceof HTMLInputElement) {
    userEnable.addEventListener('click', function () {
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
    });
}