import Swal from "sweetalert2";
import {colors} from "../utils";

export const swaleWarning = (text: string) => Swal.fire({
    title: 'Êtes vous sûr de continuer ?',
    text: text,
    icon: 'warning',
    confirmButtonText: 'Oui',
    confirmButtonColor: colors.primary,
    showCancelButton: true,
    cancelButtonText: 'Annuler',
    cancelButtonColor: colors.secondary
})

export const swaleWarningAndRedirect = (text: string, url: string) => Swal.fire({
    title: 'Êtes vous sûr de continuer ?',
    text: text,
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