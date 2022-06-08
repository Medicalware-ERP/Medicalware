import {$} from '../utils'
import Routing from "../Routing";
import {swaleWarningAndRedirect} from "../utilis/swal";

let userIdElement = $('#user-id') as HTMLInputElement;

const url = Routing.generate('app_toggle_active_user', {
    id: userIdElement.dataset.user
});

const userDisable = $("#user-disable");
const userEnable = $("#user-enable");

if (userDisable instanceof HTMLElement) {

    userDisable.addEventListener('click', function () {
        swaleWarningAndRedirect("Vous êtes sur le point de désactiver l'accés au compte pour l'utilisateur", url)
    });
}

if (userEnable instanceof HTMLElement) {

    userEnable.addEventListener('click', function () {

        swaleWarningAndRedirect("Vous êtes sur le point de d'activer l'accés au compte pour l'utilisateur", url)
    });
}