
/*BOTON MENU HAMBURGUESA*/

const boton = document.getElementById("menu-btn");

const menu = document.getElementById("menu");

boton.addEventListener("click", ()=>{
    menu.classList.toggle("active");
});


