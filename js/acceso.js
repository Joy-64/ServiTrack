/*BOTON CONTRASEÑA */

const inputContrasena = document.getElementById('contrasena');
const mostrarContrasena = document.getElementById('btnContrasena');

mostrarContrasena.addEventListener('click', function() {
     if(inputContrasena.type === 'password'){

        inputContrasena.type = 'text';

    } else {

        inputContrasena.type = 'password';

    }
});
