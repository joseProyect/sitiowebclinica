// presentacion/js/dashboard.js

// Función para obtener los datos desde el servidor
function loadDashboardData() {
  fetch('../controladores/DashboardController.php')
    .then(response => response.json())
    .then(data => {
      // Actualizar las tarjetas con los datos recibidos
      document.getElementById('usuarios').textContent = data.usuarios;
      document.getElementById('doctores').textContent = data.doctores;
      document.getElementById('asistentes').textContent = data.asistentes;
      document.getElementById('administradores').textContent = data.administradores;
      document.getElementById('pacientes').textContent = data.pacientes;
      document.getElementById('citas').textContent = data.citas;
    })
    .catch(error => console.error('Error al cargar los datos del dashboard:', error));
}

// Cargar los datos cuando la página se haya cargado completamente
window.onload = loadDashboardData;
