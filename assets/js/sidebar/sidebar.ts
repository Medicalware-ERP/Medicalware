import {$} from '../utils'

const drawerLinks: HTMLElement[] = Array.from<HTMLElement>(document.querySelectorAll(".drawer-links"));
const sidebar = $(".sidebar");
const toggleButton = $(".fa-bars");
const topbar = $(".topbar");

if (toggleButton instanceof HTMLElement &&
    sidebar instanceof HTMLElement &&
    topbar instanceof HTMLElement
) {
    //Toggle sideBar, add/remove width topbar
    toggleButton.addEventListener("click", () => {
        sidebar.classList.toggle("close");
        topbar.classList.toggle("sidebar-close");
        toggleButton.classList.toggle("reverse");
        drawerLinks.forEach( element => element.classList.remove("show-sub-menu"));
    });

    // Toggle submenus
    drawerLinks.forEach(element => {
        element.addEventListener("click", (e) => {
            element.classList.toggle("show-sub-menu");
        });
    })
}