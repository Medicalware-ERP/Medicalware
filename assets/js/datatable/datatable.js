import {$} from '../utils.js'
import Routing from "../Routing";
import {toggleActive} from "../human_resources/datatable";

let query = '';
let page = 1;
let users;
const inputSearch = $('#input-search');

const fetchUsers = async () => {
    const url = Routing.generate('users_json', { query, page });
    const completeUrl = "http://127.0.0.1:8000" + url;
    users = await fetch(completeUrl).then(res => res.json())
};


// Ajax get users from controller
const getUsers = async () => {
    await fetchUsers();
    const tbody = $("#users-data");
    tbody.innerHTML = '';
    for(let user of users){
        const regexFrenchPhoneNumberFormat = /(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/
        const phoneNumber = user.phone_number.replace(regexFrenchPhoneNumberFormat, "$1.$2.$3.$4.$5");
        const urlDetailsUser = Routing.generate('app_show_user', { id: user.id });
        const avatar = user.avatar ?? `<div class="avatar-default"><span>${user.first_name.substr(0,1).toUpperCase()}${user.last_name.substr(0,1).toUpperCase()}</span></div>`
        const active = user.is_active === true ?
            `<label class="switch">
                        <input type="checkbox" checked data-user=${user.id}>
                        <span class="slider round"></span>
              </label>`
            : ` <label class="switch">
                        <input type="checkbox" data-user=${user.id}>
                        <span class="slider round"></span>
                 </label>`
        tbody.innerHTML += `<tr>
                                <td> ${avatar}</td>
                                <td class="user-last-name">${user.last_name}</td>
                                <td class="user-first-name">${user.first_name}</td>
                                <td class="user-phone-number">${phoneNumber}</td>
                                <td class="user-email">${user.email}</td>
                                <td class="user-role">${user.profession}</td>
                                <td>${active}</td>
                                <td>
                                    <a class="btn-xs btn-primary" href=${urlDetailsUser}><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>`
    }
    toggleActive();
}

getUsers();

const allLink = document.querySelectorAll(".pagination .link");
allLink.forEach((link) => {
    if(parseInt(link.getAttribute('data-id')) === 1){
        link.classList.add("active");
    }
    link.addEventListener("click", async (e) => {
        link.classList.add("active");
        page = e.currentTarget.dataset.id;
        window.page = page;
        const nodesLink = [...link.parentNode.children];
        const nodesLinkFiltered = nodesLink.filter((nodeLink) => link !== nodeLink);
        nodesLinkFiltered.forEach((nodeLinkFiltered) => {
            nodeLinkFiltered.classList.remove("active");
        })
        await getUsers()
    })
})

inputSearch.addEventListener('keyup',async (e) => {
    query = e.target.value;
    await getUsers();
});


