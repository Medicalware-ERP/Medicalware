import {$, findInDataset} from '../utils'
import Routing from "../Routing";
import {swaleWarningAndRedirect} from "../util/swal";
import axios from "axios";
import {ModalOption, openAjaxModal} from "../util/modal";
import {declareCalendar} from "../util/planning";

function initShowUser() {
    let userIdElement = $('#user-id') as HTMLInputElement;

    if (userIdElement == null) return;

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

    const fileInput = $('#file_form')?.querySelector('[type="file"]');
    const removeImageBtn = $("#removeImage");
    const nameAvatar    = $("#name_avatar");

    if (!(fileInput instanceof HTMLInputElement)) {
        throw new Error('file input not found');
    }

    if (!(removeImageBtn instanceof HTMLButtonElement)) {
        throw new Error('remove btn not found');
    }

    if (!(nameAvatar instanceof HTMLElement)) {
        throw new Error('file input not found');
    }

    $("#uploadImage")?.addEventListener('click', e => {
        fileInput.click();
    });


    fileInput.addEventListener('change', () => {
        const file = (fileInput.files as FileList)[0];
        const form =  $('#file_form')?.querySelector('form') as HTMLFormElement;
        const formData = new FormData(form);
        const url      = form.action;

        const img = document.getElementById("img_avatar") as HTMLImageElement;
        const error = document.getElementById("error-avatar") as HTMLParagraphElement;
        error.classList.add('d-none');
        img.classList.remove('border-danger')

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then((e) => {
            img.classList.remove('d-none')
            removeImageBtn.classList.remove('d-none');
            nameAvatar.classList.add('d-none');
            const fr = new FileReader();
            fr.onload = function () {
                img.src = fr.result as string;
            }
            fr.readAsDataURL(file);
        }).catch(() => {
            error.classList.remove('d-none');
            error.textContent = "Le fichier est trop volumineux (max: 1Mb)";
            img.classList.add('border-danger');
            nameAvatar.classList.add('border-danger');
        })
    });



    removeImageBtn.addEventListener('click', () => {
        const url = findInDataset(removeImageBtn, 'url');
        location.href = url;
    });

    const changePasswordBtn = $("#change_password") as HTMLAnchorElement;

    changePasswordBtn?.addEventListener('click', () => {
        const url = findInDataset(changePasswordBtn, 'url');
        const option: ModalOption = {
            title: 'Changer votre mot de passe',
            removeAction: false,
            ajaxSubmit: true,
        };
        openAjaxModal(url, option);
    });
}

document.addEventListener('layout.user-show.loaded', () => {
    initShowUser();
});

const initUserCalendar = () => {
    const userPlanning = $("#user-show-planning") as HTMLElement;

    if (!!userPlanning) {
        const userId: number = parseInt(findInDataset(userPlanning, "userId"));
        declareCalendar("user-show-planning", userId, "App\\Entity\\User");
    }
};

document.addEventListener('layout.user-planning.loaded', () => {
    initUserCalendar();
});

const initDoctorCalendar = () => {
    const doctorPlanning = $("#doctor-show-planning") as HTMLElement;

    if (!!doctorPlanning) {
        const doctorId: number = parseInt(findInDataset(doctorPlanning, "doctorId"));
        declareCalendar("doctor-show-planning", doctorId, "App\\Entity\\Doctor");
    }
};

document.addEventListener('layout.doctor-planning.loaded', () => {
    initDoctorCalendar();
});

document.addEventListener('DOMContentLoaded', () => {
    initShowUser();
    initUserCalendar();
    initDoctorCalendar();
});