document.addEventListener('DOMContentLoaded', function() {
    const buscadorInput = document.getElementById('buscadorPaciente');
    const pacienteSelect = document.getElementById('pacienteId');

    // Función para cargar pacientes filtrados
    function buscarPacientes(query) {
        if (query.length > 2) { // Hacer la búsqueda solo si se escriben al menos 3 caracteres
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `buscarPacientes.php?query=${query}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const pacientes = JSON.parse(xhr.responseText);
                    actualizarListaPacientes(pacientes);
                }
            };
            xhr.send();
        } else {
            // Si no hay suficiente texto, limpiamos la lista
            actualizarListaPacientes([]);
        }
    }

    // Función para actualizar la lista de pacientes en el select
    function actualizarListaPacientes(pacientes) {
        // Limpiar las opciones actuales del select
        pacienteSelect.innerHTML = '<option value="">Seleccione un paciente</option>';

        // Si hay pacientes, añadirlos al select
        pacientes.forEach(paciente => {
            const option = document.createElement('option');
            option.value = paciente.PacienteId;
            option.textContent = paciente.NombreCompleto;
            pacienteSelect.appendChild(option);
        });
    }

    // Evento que se activa al escribir en el buscador
    buscadorInput.addEventListener('input', function() {
        const query = this.value.trim();
        buscarPacientes(query);
    });
});
