document.addEventListener('DOMContentLoaded', () => {
    const events = document.querySelectorAll('.event');
    const detalleCitaContainer = document.getElementById('detalle-cita');

    events.forEach(event => {
        event.addEventListener('click', () => {
            const citaId = event.getAttribute('data-cita-id');

            // Fetch para obtener los detalles de la cita
            fetch(`detalleCita.php?citaId=${citaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        detalleCitaContainer.innerHTML = `<div class="error">${data.error}</div>`;
                    } else {
                        // Renderizar la tarjeta de detalle
                        detalleCitaContainer.innerHTML = `
                            <div class="detalle-cita">
                                <h2>Detalles de la Cita</h2>
                                <p><strong>Fecha:</strong> ${data.Fecha}</p>
                                <p><strong>Hora:</strong> ${data.Hora}</p>
                                <p><strong>Notas:</strong> ${data.Notas}</p>
                                <h3>Información del Paciente</h3>
                                <p><strong>Nombre:</strong> ${data.PacienteNombre} ${data.PacienteApellidoPaterno} ${data.PacienteApellidoMaterno}</p>
                                <p><strong>DNI:</strong> ${data.PacienteDNI}</p>
                                <p><strong>Fecha de Nacimiento:</strong> ${data.PacienteFechaNacimiento}</p>
                                <p><strong>Teléfono:</strong> ${data.PacienteTelefono}</p>
                                <p><strong>Dirección:</strong> ${data.PacienteDireccion}</p>
                                <p><strong>Sexo:</strong> ${data.PacienteSexo}</p>
                                <button onclick="cerrarDetalle()">Cerrar</button>
                            </div>
                        `;
                        // Mostrar el contenedor
                        detalleCitaContainer.classList.add('active');
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los detalles:', error);
                    detalleCitaContainer.innerHTML = `<div class="error">Ocurrió un error al cargar los detalles.</div>`;
                });
        });
    });
});

function cerrarDetalle() {
    const detalleCitaContainer = document.getElementById('detalle-cita');
    detalleCitaContainer.classList.remove('active');
    detalleCitaContainer.innerHTML = '';
}
