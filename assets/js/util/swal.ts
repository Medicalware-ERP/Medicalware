import Swal from "sweetalert2";
import {colors} from "../utils";

export const swaleWarning = (text: string, target: string = "") => Swal.fire({
    title: 'Êtes vous sûr de continuer ?',
    text: text,
    icon: 'warning',
    confirmButtonText: 'Oui',
    confirmButtonColor: colors.primary,
    showCancelButton: true,
    cancelButtonText: 'Annuler',
    cancelButtonColor: colors.secondary,
    target: target
})

export const swaleDanger = (text: string, target: string = "") => Swal.fire({
    title: 'Êtes vous sûr de continuer ?',
    text: text,
    icon: 'error',
    confirmButtonText: 'Oui',
    confirmButtonColor: colors.danger,
    showCancelButton: true,
    cancelButtonText: 'Annuler',
    cancelButtonColor: colors.secondary,
    target: target
})

export const swaleWarningAndRedirect = (text: string, url: string, target: string = "") => Swal.fire({
    title: 'Êtes vous sûr de continuer ?',
    text: text,
    icon: 'warning',
    confirmButtonText: 'Oui',
    confirmButtonColor: colors.primary,
    showCancelButton: true,
    cancelButtonText: 'Annuler',
    cancelButtonColor: colors.secondary,
    target: target
}).then(r => {
    if (r.isConfirmed) {
        location.href = url
    }
});

export const swaleDangerAndRedirect = (text: string, url: string, target: string = "") => Swal.fire({
    title: 'Êtes vous sûr de continuer ?',
    text: text,
    icon: 'error',
    confirmButtonText: 'Oui',
    confirmButtonColor: colors.danger,
    showCancelButton: true,
    cancelButtonText: 'Annuler',
    cancelButtonColor: colors.secondary,
    target: target
}).then(r => {
    if (r.isConfirmed) {
        location.href = url
    }
})