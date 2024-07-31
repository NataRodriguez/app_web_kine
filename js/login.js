document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('form-login');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto

        // Redirigir a la página de destino
        window.location.href = 'pagina.html';
    });
});
