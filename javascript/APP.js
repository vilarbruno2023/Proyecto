function inicioSesion (){

    const formulario = document.querySelector('#ini');

    formulario.addEventListener("submit", async (event) => {

    event.preventDefault();

    const usuario = document.querySelector("#nombreUsuario").value;
    const contra = document.querySelector("contresañe").value;

    const respuesta = await fetch("http://localhost:3000/inicio", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            usuario,
            contra
        })
    });

    const datos = await respuesta.json();

    if (respuesta.ok) {

        // Guardamos solamente la sesión/token
        localStorage.setItem("token", datos.token);

        // Podemos guardar datos no sensibles
        localStorage.setItem("nombre", datos.usuario.nombre);

        window.location.href = "inicio.html";

    } else {

        document.getElementById("mensaje").textContent =
            datos.mensaje;
    }
    }); 
}

inicioSesion();