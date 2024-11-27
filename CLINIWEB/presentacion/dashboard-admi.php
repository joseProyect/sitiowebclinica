<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DASHBOARD</title>
  <link rel="stylesheet" href="./css/dashboard-admi.css">
  <!-- Agregar Font Awesome para iconos -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <!-- Título animado -->
  <div class="title-container">
    <div class="title">¡Bienvenido al sistema!</div>
  </div>
  
  <div class="container" id="dashboard-container">
    <!-- Tarjeta de Usuarios -->
    <div class="card">
      <div class="number">
        <span id="usuarios">0</span>
      </div>
      <h3>USUARIOS</h3>
      <button class="card-btn">
        <a href="adminUsuarios.php" class="btn-link">
          <i class="fas fa-users"></i> Ver más
        </a>
      </button>
    </div>

    <!-- Tarjeta de Doctores -->
    <div class="card">
      <div class="number">
        <span id="doctores">0</span>
      </div>
      <h3>DOCTORES</h3>
      <button class="card-btn">
        <a href="adminDoctor.php" class="btn-link">
          <i class="fas fa-user-md"></i> Ver más
        </a>
      </button>
    </div>

    <!-- Tarjeta de Asistentes -->
    <div class="card">
      <div class="number">
        <span id="asistentes">0</span>
      </div>
      <h3>ASISTENTES</h3>
      <button class="card-btn">
        <a href="adminUsuarios.php" class="btn-link">
          <i class="fas fa-user-tie"></i> Ver más
        </a>
      </button>
    </div>

    <!-- Tarjeta de Administradores -->
    <div class="card">
      <div class="number">
        <span id="administradores">0</span>
      </div>
      <h3>ADMINISTRADORES</h3>
      <button class="card-btn">
        <a href="adminUsuarios.php" class="btn-link">
          <i class="fas fa-cogs"></i> Ver más
        </a>
      </button>
    </div>

    <!-- Tarjeta de Pacientes -->
    <div class="card">
      <div class="number">
        <span id="pacientes">0</span>
      </div>
      <h3>PACIENTES</h3>
      <button class="card-btn">
        <a href="adminPacientes.php" class="btn-link">
          <i class="fas fa-bed"></i> Ver más
        </a>
      </button>
    </div>

    <!-- Tarjeta de Citas -->
    <div class="card">
      <div class="number">
        <span id="citas">0</span>
      </div>
      <h3>CITAS</h3>
      <button class="card-btn">
        <a href="admincitas.php" class="btn-link">
          <i class="fas fa-calendar-check"></i> Ver más
        </a>
      </button>
    </div>
  </div>

  <script src="js/dashboard-admi.js"></script>
</body>
</html>
