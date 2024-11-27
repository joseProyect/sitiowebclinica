// Selección de elementos
const sidebar = document.querySelector('.sidebar');
const toggle = document.querySelector('.toggle');
const logoutLink = document.getElementById('logout-link');
const contentArea = document.getElementById('content-area');

// Evento de toggle para abrir/cerrar el sidebar
toggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
});

// Agregar animación 3D al iframe al cargar contenido
document.querySelectorAll('.menu-links a').forEach(link => {
    link.addEventListener('click', (event) => {
        event.preventDefault();

        // Cambiar la URL del iframe después de un pequeño retraso para permitir que la animación se reproduzca
        const newUrl = link.getAttribute('href');
        if (contentArea.src !== newUrl) {
            contentArea.classList.add('animate-3d');

            // Cambiar la URL del iframe
            setTimeout(() => {
                contentArea.src = newUrl;
            }, 300); // Coincide con la duración de la animación
        }

        // Eliminar la clase de animación después de que termine
        contentArea.addEventListener('load', () => {
            setTimeout(() => {
                contentArea.classList.remove('animate-3d');
            }, 300); // Coincide con la duración de la animación
        });
    });
});
