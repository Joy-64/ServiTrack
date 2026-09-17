
        const slides = document.querySelectorAll(".carrusel-slide");

let slideActual = 0;

function mostrarSlide(indice) {
    slides[slideActual].classList.remove("activo");

    slideActual = (indice + slides.length) % slides.length;

    slides[slideActual].classList.add("activo");
}

const anterior = document.querySelector("#slideAnterior");
const siguiente = document.querySelector("#slideSiguiente");

anterior.addEventListener("click", function () {
    mostrarSlide(slideActual - 1);
});

siguiente.addEventListener("click", function () {
    mostrarSlide(slideActual + 1);
});