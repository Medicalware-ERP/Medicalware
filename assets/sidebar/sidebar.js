import { $ } from '../utils.js'

const arrow = $("#accountent .link a");
const subMenu = $("#accountent .sub-menu");

const sidebar = $(".sidebar");
const sidebarBtn = $(".fa-bars");

//Toggle sideBar
sidebarBtn.addEventListener("click", (e) => {
    sidebar.classList.toggle("close");
});

// Toggle submenu
arrow.addEventListener('click', (e) => {
    let arrow = e.target.parentElement.parentElement.parentElement;
    arrow.classList.toggle("show-sub-menu");
})