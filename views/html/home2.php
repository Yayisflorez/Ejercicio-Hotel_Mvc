<?php
// ============================================================
// inicio.php — Página principal del usuario autenticado
// ============================================================


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
      $habitaciones = [
        [
          'img'      => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80',
          'tipo'     => 'Estándar',
          'nombre'   => 'Habitación Clásica',
          'precio'   => '$180',
          'detalle'  => 'Cama doble, vista al jardín, baño privado, WiFi premium.',
          'badge'    => 'Disponible',
          'badge_ok' => true,
        ],
        [
          'img'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80',
          'tipo'     => 'Superior',
          'nombre'   => 'Suite Mar',
          'precio'   => '$320',
          'detalle'  => 'Cama king, balcón con vista al mar, bañera de lujo, minibar.',
          'badge'    => 'Más popular',
          'badge_ok' => true,
        ],
        [
          'img'      => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=900&q=80',
          'tipo'     => 'Deluxe',
          'nombre'   => 'Suite Presidencial',
          'precio'   => '$650',
          'detalle'  => 'Sala privada, jacuzzi, terraza panorámica, servicio butler 24h.',
          'badge'    => 'Premium',
          'badge_ok' => true,
        ],
        [
          'img'      => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=900&q=80',
          'tipo'     => 'Familiar',
          'nombre'   => 'Suite Familia',
          'precio'   => '$420',
          'detalle'  => 'Dos habitaciones conectadas, zona de juegos, camas adicionales.',
          'badge'    => 'Disponible',
          'badge_ok' => true,
        ],
      ];
      foreach ($habitaciones as $hab): ?>
      <div class="hab-card">
        <div class="hab-img-wrap">
          <img src="<?= $hab['img'] ?>" alt="<?= $hab['nombre'] ?>" class="hab-img"/>
          <span class="hab-tipo"><?= $hab['tipo'] ?></span>
        </div>
        <div class="hab-body">
          <div class="hab-header">
            <h3 class="hab-nombre"><?= $hab['nombre'] ?></h3>
            <div class="hab-precio-wrap">
              <span class="hab-desde">desde</span>
              <span class="hab-precio"><?= $hab['precio'] ?></span>
              <span class="hab-noche">/noche</span>
            </div>
          </div>
          <p class="hab-detalle"><?= $hab['detalle'] ?></p>
          <div class="hab-footer">
            <span class="hab-badge <?= $hab['badge_ok'] ? 'hab-badge--ok' : 'hab-badge--no' ?>">
              <?= $hab['badge'] ?>
            </span>
            <button class="btn-reservar"
              data-nombre="<?= $hab['nombre'] ?>"
              data-tipo="<?= $hab['tipo'] ?>"
              data-precio="<?= $hab['precio'] ?>"
              data-img="<?= $hab['img'] ?>"
              onclick="abrirReserva(this)">Reservar</button>
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

    <!-- Botón cerrar -->
    <button class="modal-close" onclick="cerrarReserva(null, true)" aria-label="Cerrar">✕</button>

    <!-- Encabezado con imagen y datos de la hab -->
    <div class="modal-header">
      <div class="modal-header-img-wrap">
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

      <form id="formReserva" class="modal-form" onsubmit="confirmarReserva(event)">

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
          <input type="hidden" name="fecha_fin"    id="fecha_fin"/>
          <div class="fechas-seleccionadas" id="fechas-texto">
            Selecciona tu fecha de entrada en el calendario
          </div>
        </div>

        <!-- Cantidad de personas -->
        <div class="form-section">
          <p class="form-section-title">👥 Cantidad de personas</p>
          <div class="personas-selector">
            <button type="button" class="personas-btn" onclick="cambiarPersonas(-1)">−</button>
            <div class="personas-display">
              <span id="personas-num" class="personas-num">1</span>
              <span class="personas-label">persona(s)</span>
            </div>
            <button type="button" class="personas-btn" onclick="cambiarPersonas(1)">+</button>
          </div>
          <input type="hidden" name="personas" id="personas-input" value="1"/>
          <p class="personas-hint">Máximo 4 personas por habitación</p>
        </div>

        <!-- Método de pago -->
        <div class="form-section">
          <p class="form-section-title">💳 Método de pago</p>
          <div class="pagos-grid">

            <label class="pago-card" for="pago-nequi">
              <input type="radio" name="pago" id="pago-nequi" value="nequi" required/>
              <div class="pago-card-inner">
                <div class="pago-logo pago-nequi">
                  <span class="pago-logo-letter">N</span>
                </div>
                <span class="pago-nombre">Nequi</span>
                <span class="pago-desc">Pago digital</span>
              </div>
            </label>

            <label class="pago-card" for="pago-daviplata">
              <input type="radio" name="pago" id="pago-daviplata" value="daviplata" required/>
              <div class="pago-card-inner">
                <div class="pago-logo pago-daviplata">
                  <span class="pago-logo-letter">D</span>
                </div>
                <span class="pago-nombre">Daviplata</span>
                <span class="pago-desc">Banco Davivienda</span>
              </div>
            </label>

            <label class="pago-card" for="pago-bancolombia">
              <input type="radio" name="pago" id="pago-bancolombia" value="bancolombia" required/>
              <div class="pago-card-inner">
                <div class="pago-logo pago-bancolombia">
                  <span class="pago-logo-letter">B</span>
                </div>
                <span class="pago-nombre">Bancolombia</span>
                <span class="pago-desc">Transferencia</span>
              </div>
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

<!-- ============================================================
     FOOTER
     ============================================================ -->
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
<script>
  // Menú hamburguesa
  const hamburger   = document.getElementById('hamburger');
  const mobileMenu  = document.getElementById('mobileMenu');

  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    mobileMenu.classList.toggle('open');
  });

  // Cerrar menú móvil al hacer clic en un enlace
  document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('open');
      mobileMenu.classList.remove('open');
    });
  });

  // Marcar enlace activo según sección visible
  const sections  = document.querySelectorAll('section[id]');
  const navLinks  = document.querySelectorAll('.nav-link');

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(l => l.classList.remove('nav-link--active'));
        const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
        if (active) active.classList.add('nav-link--active');
      }
    });
  }, { threshold: 0.4 });

  sections.forEach(s => observer.observe(s));
</script>

<!-- ============================================================
     JAVASCRIPT — Modal de Reservas
     ============================================================ -->
<script>
// ── Estado del modal ──────────────────────────────────────────
let precioNoche   = 0;
let personasCount = 1;
let calYear, calMonth;
let fechaInicio   = null; // Date
let fechaFin      = null; // Date

// ── Abrir modal ───────────────────────────────────────────────
function abrirReserva(btn) {
  const nombre = btn.dataset.nombre;
  const tipo   = btn.dataset.tipo;
  const precio = btn.dataset.precio;   // e.g. "$180"
  const img    = btn.dataset.img;

  // Parsear precio a número
  precioNoche = parseInt(precio.replace(/[^0-9]/g, ''), 10);

  // Llenar encabezado
  document.getElementById('modal-img').src            = img;
  document.getElementById('modal-img').alt            = nombre;
  document.getElementById('modal-tipo').textContent   = tipo;
  document.getElementById('modal-title').textContent  = nombre;
  document.getElementById('modal-precio-noche').textContent = precio + ' / noche';
  document.getElementById('res-nombre').textContent   = nombre;

  // Reiniciar estado
  personasCount = 1;
  fechaInicio   = null;
  fechaFin      = null;
  document.getElementById('personas-num').textContent  = '1';
  document.getElementById('personas-input').value      = '1';
  document.getElementById('fecha_inicio').value        = '';
  document.getElementById('fecha_fin').value           = '';
  document.getElementById('fechas-texto').textContent  = 'Selecciona tu fecha de entrada en el calendario';
  document.getElementById('res-noches').textContent    = '—';
  document.getElementById('res-total').textContent     = '—';

  // Desmarcar métodos de pago
  document.querySelectorAll('.pago-card input').forEach(r => r.checked = false);
  document.querySelectorAll('.pago-card').forEach(c => c.classList.remove('selected'));

  // Inicializar calendario en mes actual
  const hoy  = new Date();
  calYear    = hoy.getFullYear();
  calMonth   = hoy.getMonth();
  renderCalendario();

  // Mostrar modal
  document.getElementById('modalReserva').classList.add('open');
  document.body.style.overflow = 'hidden';
}

// ── Cerrar modal ──────────────────────────────────────────────
function cerrarReserva(event, forzar) {
  if (forzar || (event && event.target === document.getElementById('modalReserva'))) {
    document.getElementById('modalReserva').classList.remove('open');
    document.body.style.overflow = '';
  }
}

// ── Calendario ────────────────────────────────────────────────
const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

function renderCalendario() {
  document.getElementById('cal-month-label').textContent = MESES[calMonth] + ' ' + calYear;

  const grid  = document.getElementById('cal-grid');
  grid.innerHTML = '';

  const hoy       = new Date(); hoy.setHours(0,0,0,0);
  const primerDia = new Date(calYear, calMonth, 1).getDay();
  const diasMes   = new Date(calYear, calMonth + 1, 0).getDate();

  // Celdas vacías al inicio
  for (let i = 0; i < primerDia; i++) {
    const blank = document.createElement('div');
    blank.className = 'cal-cell cal-blank';
    grid.appendChild(blank);
  }

  for (let d = 1; d <= diasMes; d++) {
    const fecha = new Date(calYear, calMonth, d);
    const cell  = document.createElement('div');
    cell.className  = 'cal-cell';
    cell.textContent = d;

    if (fecha < hoy) {
      cell.classList.add('cal-past');
    } else {
      // Marcar inicio, rango y fin
      if (fechaInicio && fechaFin) {
        if (esMismaFecha(fecha, fechaInicio))   cell.classList.add('cal-start');
        else if (esMismaFecha(fecha, fechaFin)) cell.classList.add('cal-end');
        else if (fecha > fechaInicio && fecha < fechaFin) cell.classList.add('cal-range');
      } else if (fechaInicio && esMismaFecha(fecha, fechaInicio)) {
        cell.classList.add('cal-start');
      }

      cell.addEventListener('click', () => seleccionarFecha(fecha));
    }

    grid.appendChild(cell);
  }

  // Botón prev: deshabilitar si estamos en el mes actual
  const hoyMes = new Date(); hoyMes.setDate(1); hoyMes.setHours(0,0,0,0);
  const esteMs = new Date(calYear, calMonth, 1);
  document.getElementById('cal-prev').disabled = esteMs <= hoyMes;
}

function esMismaFecha(a, b) {
  return a.getFullYear() === b.getFullYear() &&
         a.getMonth()    === b.getMonth()    &&
         a.getDate()     === b.getDate();
}

function cambiarMes(delta) {
  calMonth += delta;
  if (calMonth > 11) { calMonth = 0;  calYear++; }
  if (calMonth < 0)  { calMonth = 11; calYear--; }
  renderCalendario();
}

function seleccionarFecha(fecha) {
  if (!fechaInicio || (fechaInicio && fechaFin)) {
    // Primera selección o reinicio
    fechaInicio = fecha;
    fechaFin    = null;
  } else {
    if (fecha <= fechaInicio) {
      fechaInicio = fecha;
      fechaFin    = null;
    } else {
      fechaFin = fecha;
    }
  }
  actualizarFechasUI();
  renderCalendario();
}

function actualizarFechasUI() {
  const fmt = d => d.toLocaleDateString('es-CO', { day:'2-digit', month:'short', year:'numeric' });

  if (fechaInicio && !fechaFin) {
    document.getElementById('fechas-texto').innerHTML =
      `<span class="fecha-chip entrada">✈️ Entrada: ${fmt(fechaInicio)}</span>
       <span class="fecha-chip-hint">Ahora selecciona la fecha de salida</span>`;
    document.getElementById('fecha_inicio').value = fechaInicio.toISOString().slice(0,10);
    document.getElementById('fecha_fin').value    = '';
    document.getElementById('res-noches').textContent = '—';
    document.getElementById('res-total').textContent  = '—';
  } else if (fechaInicio && fechaFin) {
    const noches = Math.round((fechaFin - fechaInicio) / 86400000);
    const total  = noches * precioNoche;
    document.getElementById('fechas-texto').innerHTML =
      `<span class="fecha-chip entrada">✈️ Entrada: ${fmt(fechaInicio)}</span>
       <span class="fecha-chip salida">🏁 Salida: ${fmt(fechaFin)}</span>`;
    document.getElementById('fecha_inicio').value = fechaInicio.toISOString().slice(0,10);
    document.getElementById('fecha_fin').value    = fechaFin.toISOString().slice(0,10);
    document.getElementById('res-noches').textContent = noches + (noches === 1 ? ' noche' : ' noches');
    document.getElementById('res-total').textContent  = '$' + total.toLocaleString('es-CO');
  }
}

// ── Personas ──────────────────────────────────────────────────
function cambiarPersonas(delta) {
  personasCount = Math.max(1, Math.min(4, personasCount + delta));
  document.getElementById('personas-num').textContent  = personasCount;
  document.getElementById('personas-input').value      = personasCount;

  // Efecto visual en los botones
  document.querySelector('.personas-btn:first-child').disabled = personasCount === 1;
  document.querySelector('.personas-btn:last-child').disabled  = personasCount === 4;
}

// ── Resaltar tarjeta de pago al seleccionar ───────────────────
document.querySelectorAll('.pago-card input').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.pago-card').forEach(c => c.classList.remove('selected'));
    radio.closest('.pago-card').classList.add('selected');
  });
});

// ── Confirmar reserva ─────────────────────────────────────────
function confirmarReserva(e) {
  e.preventDefault();

  if (!fechaInicio || !fechaFin) {
    shakeEl('fechas-texto'); return;
  }

  const metodo = document.querySelector('input[name="pago"]:checked');
  if (!metodo) { shakeEl('pagos-grid'); return; }

  const fmt    = d => d.toLocaleDateString('es-CO', { day:'2-digit', month:'short', year:'numeric' });
  const noches = Math.round((fechaFin - fechaInicio) / 86400000);
  const total  = noches * precioNoche;

  document.getElementById('toast-sub').textContent =
    `${document.getElementById('modal-title').textContent} · ${fmt(fechaInicio)} → ${fmt(fechaFin)} · $${total.toLocaleString('es-CO')} · ${metodo.value}`;

  cerrarReserva(null, true);

  const toast = document.getElementById('toast');
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 4500);
}

function shakeEl(id) {
  const el = document.getElementById(id) || document.querySelector('.' + id);
  if (!el) return;
  el.classList.add('shake');
  setTimeout(() => el.classList.remove('shake'), 500);
}

// Cerrar con Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') cerrarReserva(null, true);
});
</script>

</body>
</html>