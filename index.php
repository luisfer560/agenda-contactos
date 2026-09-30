<?php
require_once "Database.php";
require_once "Contact.php";

$database = new Database();
$db = $database->getConnection();
$contact = new Contact($db);

// Mensaje de estado para feedback al usuario
$message = "";
$messageType = "";

// 1. Procesar creación de contacto (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add") {
    if (!empty($_POST["name"]) && !empty($_POST["phone"])) {
        $contact->name = $_POST["name"];
        $contact->phone = $_POST["phone"];
        $contact->email = $_POST["email"] ?? "";
        $contact->category = $_POST["category"] ?? "Otros";

        if ($contact->create()) {
            $message = "¡Contacto guardado exitosamente!";
            $messageType = "success";
        } else {
            $message = "Ocurrió un error al guardar el contacto.";
            $messageType = "error";
        }
    }
}

// 2. Procesar eliminación (GET)
if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $contact->id = $_GET["id"];
    $contact->delete();
    header("Location: index.php");
    exit();
}

// 3. Capturar término de búsqueda si existe
$search_query = $_GET["search"] ?? "";
$stmt = $contact->read($search_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agenda de Contactos POO</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Fuente Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Navegación -->
  <header class="navbar">
    <div class="logo">
      <span class="logo-icon">📇</span> ContactApp <span class="badge-mini">POO</span>
    </div>
    <div class="nav-info">
      <span class="status-dot"></span> MySQL Conectado
    </div>
  </header>

  <!-- Hero Section -->
  <main class="container">
    <section class="hero">
      <span class="badge">✨ Proyecto Didáctico PHP & MySQL</span>
      <h1>Gestión de Contactos <span class="gradient-text">Moderna</span></h1>
      <p class="hero-subtitle">
        Administra tu agenda telefónica aplicando Programación Orientada a Objetos y consultas seguras con PDO.
      </p>
    </section>

    <!-- Alertas de feedback -->
    <?php if(!empty($message)): ?>
      <div class="alert alert-<?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Cuadrícula Principal (Formulario + Lista) -->
    <div class="app-grid">
      
      <!-- Columna Izquierda: Formulario -->
      <div class="card card-form">
        <div class="card-header">
          <h2>👤 Nuevo Contacto</h2>
          <p>Llena los datos para guardar en MySQL</p>
        </div>

        <form action="index.php" method="POST" class="contact-form">
          <input type="hidden" name="action" value="add">
          
          <div class="form-group">
            <label for="name">Nombre Completo *</label>
            <input type="text" id="name" name="name" required placeholder="Ej. Ana Gómez" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="phone">Teléfono *</label>
            <input type="tel" id="phone" name="phone" required placeholder="Ej. +58 412 1234567" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="category">Categoría</label>
            <select id="category" name="category">
              <option value="Familia">Familia</option>
              <option value="Trabajo">Trabajo</option>
              <option value="Amigos">Amigos</option>
              <option value="Otros" selected>Otros</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Guardar Contacto</button>
        </form>
      </div>

      <!-- Columna Derecha: Búsqueda y Lista -->
      <div class="card card-list">
        <div class="card-header flex-header">
          <div>
            <h2>📋 Mis Contactos</h2>
            <p>Lista almacenada en la base de datos</p>
          </div>
        </div>

        <!-- Buscador -->
        <form action="index.php" method="GET" class="search-bar">
          <input type="text" name="search" placeholder="Buscar por nombre, teléfono o email..." value="<?= htmlspecialchars($search_query) ?>" autocomplete="off">
          <button type="submit" class="btn btn-secondary">Buscar</button>
          <?php if(!empty($search_query)): ?>
            <a href="index.php" class="btn btn-icon" title="Limpiar búsqueda">✕</a>
          <?php endif; ?>
        </form>

        <!-- Lista de Contactos -->
        <ul class="contact-list">
          <?php 
          $hasContacts = false;
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
            $hasContacts = true;
          ?>
            <li class="contact-item">
              <div class="contact-avatar">
                <?= strtoupper(substr($row['name'], 0, 1)) ?>
              </div>
              <div class="contact-info">
                <div class="contact-title">
                  <h3><?= htmlspecialchars($row['name']) ?></h3>
                  <span class="badge-cat badge-<?= strtolower($row['category']) ?>">
                    <?= htmlspecialchars($row['category']) ?>
                  </span>
                </div>
                <div class="contact-details">
                  <span>📞 <?= htmlspecialchars($row['phone']) ?></span>
                  <?php if(!empty($row['email'])): ?>
                    <span>✉️ <?= htmlspecialchars($row['email']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <a href="index.php?action=delete&id=<?= $row['id'] ?>" 
                 class="btn-delete" 
                 onclick="return confirm('¿Seguro que deseas eliminar este contacto?')"
                 title="Eliminar contacto">
                🗑️
              </a>
            </li>
          <?php endwhile; ?>

          <?php if (!$hasContacts): ?>
            <div class="empty-state">
              <span class="empty-icon">🔍</span>
              <p>No se encontraron contactos en la agenda.</p>
            </div>
          <?php endif; ?>
        </ul>
      </div>

    </div>
  </main>

</body>
</html>