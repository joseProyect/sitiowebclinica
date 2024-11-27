document.addEventListener('DOMContentLoaded', () => {
    const citaCards = document.querySelectorAll('.cita-card');
    const btnEditar = document.getElementById('btnEditar');
    const btnCambiarEstado = document.getElementById('btnCambiarEstado');
    let selectedCard = null;

    // Inicia con los botones "Editar" y "Cambiar Estado" deshabilitados
    btnEditar.disabled = true;
    btnCambiarEstado.disabled = true;

    // Manejo del evento de clic en cada tarjeta de cita
    citaCards.forEach(card => {
        card.addEventListener('click', () => {
            // Si ya hay una cita seleccionada, la deseleccionamos
            if (selectedCard) {
                selectedCard.classList.remove('selected');
            }

            // Seleccionamos la nueva cita y la marcamos
            selectedCard = card;
            card.classList.add('selected');

            // Habilitar los botones
            btnEditar.disabled = false;
            btnCambiarEstado.disabled = false;
        });
    });

    // Redirección para el botón "Agregar"
    document.getElementById('btnAgregar').addEventListener('click', () => {
        window.location.href = `agregarCita.php`; // Redirige a la página de agregar cita
    });

    // Redirección para el botón "Editar"
    btnEditar.addEventListener('click', () => {
        if (selectedCard) {
            const citaId = selectedCard.dataset.id;
            window.location.href = `editarCita.php?citaId=${citaId}`; // Redirigir a la página de editar con el ID de la cita
        } else {
            alert("Por favor, seleccione una cita para editar.");
        }
    });

    // Redirección para el botón "Cambiar Estado"
    btnCambiarEstado.addEventListener('click', () => {
        if (selectedCard) {
            const citaId = selectedCard.dataset.id;
            window.location.href = `cambiarEstadoCita.php?citaId=${citaId}`; // Redirige a la página de cambiar estado
        } else {
            alert("Por favor, seleccione una cita para cambiar el estado.");
        }
    });
});
