import { $ } from '../utils.js'

const li = $(".nav-links #accountent");
const sidebar = $(".sidebar");
const toggleButton = $(".fa-bars");
const topbar = $(".topbar");

//Toggle sideBar, add/remove width topbar
toggleButton.addEventListener("click", ()=>{
    sidebar.classList.toggle("close");
    topbar.classList.toggle("sidebar-close");
    toggleButton.classList.toggle("reverse");
    li.classList.remove("show-sub-menu");
});

// Toggle submenu
li.addEventListener("click", (e)=>{
    li.classList.toggle("show-sub-menu");
});

