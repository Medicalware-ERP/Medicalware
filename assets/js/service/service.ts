import {openAjaxModal, openModal} from "../util/modal";
import Routing from "../Routing";

const button = document.querySelector("#add_service");
button?.addEventListener("click", () => {
    const url = Routing.generate("add_enum",{
        class : "App\\Entity\\Service"
    });
    openAjaxModal(url);
})
