<?php


$mensaje = $_SESSION["mensaje"] ?? "";
$tipo = $_SESSION["tipo"] ?? "";
$errores = $_SESSION["errors"] ?? [];
$datos = $_SESSION["datos"] ?? [];
$documents = $_SESSION['documentTypes'] ?? [];
$usuario = $_SESSION["usuario"] ?? null;


// LIMPIAR SESIÓN (IMPORTANTE)
unset($_SESSION["mensaje"], $_SESSION["tipo"], $_SESSION["errors"], $_SESSION["datos"]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrarse · Hotel Viña del Mar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="icon" href="img/recurso.png" type="image/png">
</head>
<body>

<div class="auth-layout">

  <!-- Panel izquierdo: imagen decorativa -->
  <div class="auth-panel-img auth-panel-img--register">
    <div class="auth-panel-overlay"></div>
    <div class="auth-panel-content">
      <span class="auth-logo-icon">✦</span>
      <h2 class="auth-logo-name">VIÑA DEL MAR</h2>
      <p class="auth-panel-quote">
        "Únete a nuestra familia y vive<br/>una experiencia sin igual."
      </p>
    </div>
  </div>

  <!-- Panel derecho: formulario -->
  <div class="auth-form-side">

    <!-- Volver al inicio -->
    <a href="index.php" class="auth-back-link">
      ← Volver al inicio
    </a>

    <div class="auth-form-box">

      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="success-message-form">
          <span class="success-icon-form">✔</span>
          Usuario creado correctamente
        </div>
      <?php endif; ?>

      <?php if (!empty($errores['db'])): ?>
        <div class="alert-error">
          <?= htmlspecialchars($errores['db']) ?>
        </div>
      <?php endif; ?>

      <br>
      <p class="auth-tag">Únete a nosotros</p>
      <h1 class="auth-title">Crear Cuenta</h1>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">
          <?= htmlspecialchars($_GET['msg'] ?? 'Error al registrar. Verifica los datos.') ?>
        </div>
      <?php endif; ?>

      <form action="index.php?action=registerUser" method="POST" class="auth-form" id="regForm">

        <!-- Tipo y número de documento -->
        <div class="field-row">
          <div class="field-group">
            <label class="field-label" for="tipo_documento_id">Tipo de Documento</label>
            <select class="field-input field-select" name="tipo_documento_id" id="tipo_documento_id">
              <option value="" disabled <?= empty($datos['tipo_documento_id'] ?? '') ? 'selected' : '' ?>>Selecciona un tipo</option>
              <?php foreach ($documents as $document): ?>
                <option value="<?php echo htmlspecialchars($document['id']); ?>" <?php echo (isset($datos['tipo_documento_id']) && $datos['tipo_documento_id'] == $document['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($document['tipo']); ?></option>
              <?php endforeach; ?>
            </select>

            <p id="tipoDocError" class="field-error hidden"></p>
            <?php if (!empty($errores['tipo_documento_id'])): ?>
              <p class="field-error"><?= htmlspecialchars($errores['tipo_documento_id']) ?></p>
            <?php endif; ?>
          </div>
          <div class="field-group">
            <label class="field-label" for="documento">Número de Doc.</label>
            <input
              id="documento"
              type="text"
              name="documento"
              placeholder="12345678"
              class="field-input"
              value="<?= htmlspecialchars($datos['documento'] ?? '') ?>"
            />
            <p id="documentoError" class="field-error hidden"></p>
            <?php if (!empty($errores['documento'])): ?>
              <p class="field-error"><?= htmlspecialchars($errores['documento']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Nombre y Apellido -->
        <div class="field-row">
          <div class="field-group">
            <label class="field-label" for="nombre">Nombre</label>
            <input
              id="nombre"
              type="text"
              name="nombre"
              placeholder="Juan"
              class="field-input"
              value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>"
            />
            <p id="nombreError" class="field-error hidden"></p>
            <?php if (!empty($errores['nombre'])): ?>
              <p class="field-error"><?= htmlspecialchars($errores['nombre']) ?></p>
            <?php endif; ?>
          </div>
          <div class="field-group">
            <label class="field-label" for="apellido">Apellido</label>
            <input
              id="apellido"
              type="text"
              name="apellido"
              placeholder="Pérez"
              class="field-input"
              value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>"
            />
            <p id="apellidoError" class="field-error hidden"></p>
            <?php if (!empty($errores['apellido'])): ?>
              <p class="field-error"><?= htmlspecialchars($errores['apellido']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Teléfono -->
        <div class="field-group">
          <label class="field-label" for="telefono">Teléfono</label>
          <input
            id="telefono"
            type="tel"
            name="telefono"
            placeholder="+57 300 000 0000"
            class="field-input"
            value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>"
          />
          <p id="telefonoError" class="field-error hidden"></p>
          <?php if (!empty($errores['telefono'])): ?>
            <p class="field-error"><?= htmlspecialchars($errores['telefono']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="field-group">
          <label class="field-label" for="email">Email</label>
          <input
            id="email"
            type="email"
            name="email"
            placeholder="correo@ejemplo.com"
            class="field-input"
            value="<?= htmlspecialchars($datos['email'] ?? '') ?>"
          />
          <p id="emailError" class="field-error hidden"></p>
          <?php if (!empty($errores['email'])): ?>
            <p class="field-error"><?= htmlspecialchars($errores['email']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Contraseña -->
        <div class="field-group">
          <label class="field-label" for="pwd">Contraseña</label>
          <input
            id="pwd"
            type="password"
            name="password"
            placeholder="Mín. 8 caracteres"
            class="field-input"
          />
          <p class="field-hint">Debe tener al menos 8 caracteres, 1 mayúscula y 1 número.</p>
          <p id="pwdStrengthError" class="field-error hidden"></p>
          <?php if (!empty($errores['password'])): ?>
            <p class="field-error"><?= htmlspecialchars($errores['password']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Confirmar contraseña -->
        <div class="field-group">
          <label class="field-label" for="pwd2">Confirmar Contraseña</label>
          <input
            id="pwd2"
            type="password"
            name="confirm_password"
            placeholder="Repite tu contraseña"
            class="field-input"
          />
          <p id="confirmPasswordError" class="field-error hidden"></p>
          <?php if (!empty($errores['confirm_password'])): ?>
            <p class="field-error"><?= htmlspecialchars($errores['confirm_password']) ?></p>
          <?php endif; ?>
          <p id="pwdError" class="field-error hidden">Las contraseñas no coinciden.</p>
        </div>

        <button type="submit" class="btn-submit">
          Crear Cuenta
        </button>

      </form>

      <p class="auth-switch">
        ¿Ya tienes cuenta?
        <a href="index.php?action=getFormLoginUser" class="auth-switch-link">Inicia sesión aquí</a>
      </p>

    </div>
  </div>

</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-text">Creando cuenta...</div>
  <div class="loading-bar-container">
    <div class="loading-bar-fill" id="loadingBarFill"></div>
  </div>
</div>

<!-- Validación frontend -->
<script src="js/ScriptRegister.js"></script>

</body>
</html>