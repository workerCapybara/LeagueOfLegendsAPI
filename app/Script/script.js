"use strict"

const URL = "api/champs/";

let champs = [];

let form = document.querySelector('#champion-form');
form.addEventListener('submit', insertChampion)



//Gets all the champions from the API REST
 
async function getAll() {
    try {
        let response = await fetch(URL);
        if (!response.ok) {
            throw new Error('Resource does not exist');
        }
        champs = await response.json();

        showChampions();
    } catch(e) {
        console.log(e);
    }
}

//Inserts the champion using the API REST
async function insertChampion(e) {
    e.preventDefault();
    
    let data = new FormData(form);
    let champion = {
        name: data.get('name'),
        role: data.get('role'),
        price: data.get('price'),
    };

    try {
        let response = await fetch(URL, {
            method: "POST",
            headers: { 'Content-Type': 'application/json'},
            body: JSON.stringify(champion)
        });
        if (!response.ok) {
            throw new Error('Server error');
        }

        let newChampion = await response.json();

        //Inserts the new champion
        champion.push(newChampion);
        showChampions();

        form.reset();
    } catch(e) {
        console.log(e);
    }
} 

async function deleteChampion(e) {
    e.preventDefault();
    try {
        let id = e.target.dataset.Champion;
        let response = await fetch(URL + id, {method: 'DELETE'});
        if (!response.ok) {
            throw new Error('Resource does not exist');
        }

        // eliminar el champion  del arreglo global
        Champion = Champion.filter(Champion => Champion.Champion_id != Champion_id);
        showChampions();
    } catch(e) {
        console.log(e);
    }
}



function showChampions() {
    let ul = document.querySelector("#champion-list");
    ul.innerHTML = "";
    for (const champion of champs) {

        let html = `
            <li class='
                    list-group-item d-flex justify-content-between align-items-center
                 
                '>
                <span> <b>${champion.name}</b> - ${champion.role} (prioridad ${champion.price}) </span>
                <div class="ml-auto">
                   
                    <a href='#' data-task="${task.id}" type='button' class='btn btn-danger btn-delete'>Borrar</a>
                </div>
            </li>
        `;

        ul.innerHTML += html;
    }

    //Assigns event listeners to the buttons
    const btnsDelete = document.querySelectorAll('a.btn-delete');
    for (const btnDelete of btnsDelete) {
        btnDelete.addEventListener('click', deleteChampion);
    }

    
}

getAll();