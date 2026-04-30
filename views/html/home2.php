<?php
// ============================================================
// inicio.php — Página principal del usuario autenticado
// ============================================================

// Protección: si no hay sesión activa, redirigir al login


// Datos del usuario desde la sesión (cargados al hacer login desde BD)

$nombre = htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Huésped');
$apellido = htmlspecialchars($_SESSION['usuario']['apellido'] ?? '');
$email    = htmlspecialchars($_SESSION['usuario']['email']    ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inicio · Hotel Viña del Mar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/home2.css"/>
    <link rel="icon" href="img/recurso.png" type="image/png">
</head>
<body>

<!-- ============================================================
     BARRA DE NAVEGACIÓN
     ============================================================ -->
<nav class="navbar">
  <div class="navbar-inner">

    <!-- Logo (izquierda) -->
    <a href="inicio.php" class="navbar-logo">
      <img src="img/logo.png" alt="Logo Hotel Viña del Mar" class="logo-img"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"/>
      <!-- Fallback si no existe la imagen -->
      <div class="logo-fallback" style="display:none;">
        <span class="logo-icon">✦</span>
        <span class="logo-text">VIÑA DEL MAR</span>
      </div>
    </a>

    <!-- Menú central -->
    <ul class="navbar-menu">
      <li><a href="#inicio"      class="nav-link nav-link--active">Inicio</a></li>
      <li><a href="#servicios"   class="nav-link">Servicios</a></li>
      <li><a href="#habitaciones" class="nav-link">Habitaciones</a></li>
        <li><a href="index.php?action=reservas" class="nav-link">Mis reservas</a></li>
    </ul>

    <!-- Acciones (derecha) -->
    <div class="navbar-actions">
      <span class="nav-username">
        <?= $nombre ?>
      </span>
      <a href="index.php?action=cerrarSesion" class="btn-logout">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
        </svg>
        Cerrar Sesión
      </a>
    </div>

    <!-- Botón hamburguesa (móvil) -->
    <button class="hamburger" id="hamburger" aria-label="Menú">
      <span></span><span></span><span></span>
    </button>
  </div>

  <!-- Menú móvil desplegable -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="#inicio"       class="mobile-link">Inicio</a>
    <a href="#servicios"    class="mobile-link">Servicios</a>
    <a href="#habitaciones" class="mobile-link">Habitaciones</a>
      <a href="index.php?action=reservas" class="mobile-link">Mis reservas</a>
    <a href="logout.php"    class="mobile-link mobile-link--logout">Cerrar Sesión</a>
  </div>
</nav>

<!-- ============================================================
     SECCIÓN INICIO — Bienvenida personalizada
     ============================================================ -->
<section id="inicio" class="hero-section">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <p class="hero-tag">Área exclusiva de huéspedes</p>
    <h1 class="hero-title">
      Bienvenido,<br/>
      <em class="hero-name"><?= $nombre ?></em>
    </h1>
    <p class="hero-subtitle">
      Nos alegra tenerte aquí. Explora todo lo que el Hotel Viña del Mar<br/>
      ha preparado especialmente para ti.
    </p>
    <div class="hero-btns">
      <a href="#habitaciones" class="btn-primary">Ver Habitaciones</a>
      <a href="#servicios"    class="btn-secondary">Nuestros Servicios</a>
    </div>
  </div>
</section>

<!-- ============================================================
     SECCIÓN SERVICIOS
     ============================================================ -->
<section id="servicios" class="section-servicios">
  <div class="section-inner">
    <div class="section-header">
      <p class="section-tag">Lo que ofrecemos</p>
      <h2 class="section-title">Nuestros Servicios</h2>
    </div>

    <div class="servicios-grid">
      <?php
      $servicios = [
        [
          'img'   => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80',
          'icon'  => '🍽️',
          'name'  => 'Restaurante Gourmet',
          'desc'  => 'Gastronomía de autor con sabores del Mediterráneo y el Pacífico, abierto los 7 días.',
        ],
        [
          'img'   => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80',
          'icon'  => '🧖',
          'name'  => 'Spa & Bienestar',
          'desc'  => 'Masajes, tratamientos faciales, sauna y rituales de relajación de clase mundial.',
        ],
        [
          'img'   => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=80',
          'icon'  => '🏊',
          'name'  => 'Piscina Infinity',
          'desc'  => 'Vista panorámica al mar, climatizada todo el año. Área exclusiva para huéspedes.',
        ],
        [
          'img'   => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',
          'icon'  => '🏋️',
          'name'  => 'Gimnasio 24h',
          'desc'  => 'Equipos de última generación y entrenadores personales disponibles a toda hora.',
        ],
        [
          'img'   => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=80',
          'icon'  => '🍾',
          'name'  => 'Bar & Lounge',
          'desc'  => 'Cócteles artesanales, vinos selectos y música en vivo los fines de semana.',
        ],
        [
          'img'   => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
          'icon'  => '🛎️',
          'name'  => 'Concierge 24h',
          'desc'  => 'Asistencia personalizada para reservas, traslados y cualquier necesidad que tengas.',
        ],
      ];
      foreach ($servicios as $srv): ?>
      <div class="servicio-card">
        <div class="servicio-img-wrap">
          <img src="<?= $srv['img'] ?>" alt="<?= $srv['name'] ?>" class="servicio-img"/>
        </div>
        <div class="servicio-body">
          <span class="servicio-icon"><?= $srv['icon'] ?></span>
          <h3 class="servicio-name"><?= $srv['name'] ?></h3>
          <p class="servicio-desc"><?= $srv['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     SECCIÓN HABITACIONES
     ============================================================ -->
<section id="habitaciones" class="section-habitaciones">
  <div class="section-inner">
    <div class="section-header">
      <p class="section-tag">Descansa en grande</p>
      <h2 class="section-title">Habitaciones & Suites</h2>
    </div>

    <div class="habitaciones-grid">
      <?php
      require_once "controller/HabitacionesController.php";
      $habitacionesDb = HabitacionesController::obtenerHabitaciones();
      
      foreach ($habitacionesDb as $hab): 
        $esDisponible = ($hab['estado']);
      ?>
      <div class="hab-card">
        <div class="hab-img-wrap">
          <img src="<?= $hab['img'] ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80' ?>" alt="<?= htmlspecialchars($hab['categoria_nombre']) ?>" class="hab-img"/>
          <span class="hab-tipo"><?= htmlspecialchars($hab['categoria_nombre']) ?></span>
        </div>
        <div class="hab-body">
          <div class="hab-header">
            <span class="hab-badge <?= $esDisponible ? 'hab-badge--ok' : 'hab-badge--no' ?>">
              <?= $esDisponible ? 'Disponible' : 'No disponible' ?>
            </span>
            <div class="hab-precio-wrap">
              <span class="hab-desde">desde</span>
              <span class="hab-precio">$<?= number_format($hab['precio'], 0) ?></span>
              <span class="hab-noche">/noche</span>
            </div>
          </div>
          <p class="hab-detalle">
            <?= $hab['descripcion'] ?>
          </p>
          <div class="hab-footer">
            <?php if ($esDisponible): ?>
              <button class="btn-reservar"
                data-id="<?= $hab['id'] ?>"
                data-nombre="<?= htmlspecialchars($hab['categoria_nombre']) ?>"
                data-tipo="<?= htmlspecialchars($hab['categoria_nombre']) ?>"
                data-precio="$<?= $hab['precio'] ?>"
                data-img="<?= $hab['img'] ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80' ?>"
                data-max-personas="<?= $hab['max_personas'] ?? 4 ?>"
                onclick="abrirReserva(this)">Reservar</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>





<!-- ============================================================
  MODAL DE RESERVA 
============================================================ -->
<div id="modalReserva" class="modal-backdrop" onclick="cerrarReserva(event)">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-title">

    
    <!-- Encabezado con imagen y datos de la hab -->
    <div class="modal-header">
      
      <div class="modal-header-img-wrap">
        <!-- Botón cerrar -->
      <button class="modal-close" onclick="cerrarReserva(null, true)" aria-label="Cerrar">✕</button>
        <img id="modal-img" src="" alt="" class="modal-header-img"/>
        <div class="modal-header-overlay"></div>
      </div>
      <div class="modal-header-info">
        <span id="modal-tipo" class="modal-hab-tipo"></span>
        <h2 id="modal-title" class="modal-hab-nombre"></h2>
        <div class="modal-precio-display">
          <span class="modal-precio-label">Precio por noche</span>
          <span id="modal-precio-noche" class="modal-precio-valor"></span>
        </div>
      </div>
    </div>
    <!-- Cuerpo del formulario -->
    <div class="modal-body">
      <!-- Resumen de precio dinámico -->
      <div class="modal-resumen" id="modal-resumen">
        <div class="resumen-row">
          <span class="resumen-label">🏨 Habitación</span>
          <span id="res-nombre" class="resumen-valor"></span>
        </div>
        <div class="resumen-row">
          <span class="resumen-label">📅 Noches</span>
          <span id="res-noches" class="resumen-valor">—</span>
        </div>
        <div class="resumen-row resumen-total">
          <span class="resumen-label">💰 Total estimado</span>
          <span id="res-total" class="resumen-valor resumen-total-val">—</span>
        </div>
      </div>
      <form id="formReserva" class="modal-form" action="index.php?action=reservarHabitacion" method="POST">
        <input type="hidden" name="id_habitacion" id="input-hab-id">
        <input type="hidden" name="precio" id="input-precio-hidden">
        <!-- Calendario: fechas -->
        <div class="form-section">
          <p class="form-section-title">📅 Fechas de estadía</p>
          <div class="calendar-wrap">
            <div class="cal-month-nav">
              <button type="button" class="cal-nav-btn" id="cal-prev" onclick="cambiarMes(-1)">‹</button>
              <span class="cal-month-label" id="cal-month-label"></span>
              <button type="button" class="cal-nav-btn" id="cal-next" onclick="cambiarMes(1)">›</button>
            </div>
            <div class="cal-grid-head">
              <span>Do</span><span>Lu</span><span>Ma</span>
              <span>Mi</span><span>Ju</span><span>Vi</span><span>Sá</span>
            </div>
            <div class="cal-grid" id="cal-grid"></div>
            <div class="cal-legend">
              <span class="leg-item"><span class="leg-dot leg-start"></span> Entrada</span>
              <span class="leg-item"><span class="leg-dot leg-range"></span> Estadía</span>
              <span class="leg-item"><span class="leg-dot leg-end"></span> Salida</span>
            </div>
          </div>
          <!-- Inputs ocultos con las fechas elegidas -->
          <input type="hidden" name="fecha_inicio" id="fecha_inicio"/>
          <input type="hidden" name="fecha_final"  id="fecha_fin"/>
          <div class="fechas-seleccionadas" id="fechas-texto">
            Selecciona tu fecha de entrada en el calendario
          </div>
        </div>
        <!-- Personas -->
        <div class="form-section">
          <p class="form-section-title">👥 Número de personas</p>
          <div class="personas-selector">
            <button type="button" class="personas-btn" onclick="cambiarPersonas(-1)">−</button>
            <span class="personas-display">
              <span id="personas-num" class="personas-num">1</span>
              <span class="personas-label">persona<span id="personas-plural" style="display:none;">s</span></span>
              <span id="personas-hint" class="personas-hint">Selecciona fechas para continuar</span>
            </span>
            <button type="button" class="personas-btn" onclick="cambiarPersonas(1)">+</button>
          </div>
          <input type="hidden" name="num_personas" id="personas-input" value="1">
        </div>

        <!-- Método de pago -->
        <div class="form-section">
          <p class="form-section-title">💳 Método de pago</p>
          <div class="pago-options">
            <label class="pago-option">
              <input type="radio" name="id_metodo_pago" value="1" id="pago-nequi" checked>
              <span class="pago-icon">💜</span>
              <span class="pago-name">Nequi</span>
            </label>
            <label class="pago-option">
              <input type="radio" name="id_metodo_pago" value="2" id="pago-daviplata">
              <span class="pago-icon">❤️</span>
              <span class="pago-name">Daviplata</span>
            </label>
            <label class="pago-option">
              <input type="radio" name="id_metodo_pago" value="3" id="pago-bancolombia">
              <span class="pago-icon">🏦</span>
              <span class="pago-name">Bancolombia</span>
            </label>
          </div>
        </div>
        <!-- Botón confirmar -->
        <button type="submit" class="btn-confirmar" id="btn-confirmar">
          Confirmar Reserva
        </button>
      </form>
    </div><!-- /modal-body -->
  </div><!-- /modal-box -->
</div>

<!-- Toast de confirmación -->
<div class="toast" id="toast">
  <span class="toast-icon">✅</span>
  <div>
    <p class="toast-title">¡Reserva confirmada!</p>
    <p class="toast-sub" id="toast-sub"></p>
  </div>
</div>
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-logo">
      <span class="logo-icon">✦</span>
      <span class="logo-text">VIÑA DEL MAR</span>
    </div>
    <p class="footer-copy">© <?= date('Y') ?> Hotel Viña del Mar · Todos los derechos reservados</p>
  </div>
</footer>

<!-- ============================================================
    JAVASCRIPT — Menú hamburguesa móvil + navbar activo al scroll
============================================================ -->
<script src="js/ScriptHome2.js"></script>

</body>
</html>