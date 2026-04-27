<?php
// ============================================================
// reservas.php — Gestión de reservas del usuario autenticado
// ============================================================

require_once "controller/HabitacionesController.php";
$id_user = $_SESSION['usuario']['id'] ?? null;
$habitaciones_lista = HabitacionesController::obtenerHabitaciones();
$reservas = $id_user ? ReservasController::obtenerReservasPorUsuario($id_user) : [];
$hay_reservas = count($reservas) > 0;
$nombre = htmlspecialchars($_SESSION['usuario']['nombre'] ?? $_SESSION['usuario_nombre'] ?? 'Huésped');

// Obtener categorías únicas
$categorias = [];
foreach ($habitaciones_lista as $hab) {
    if (!in_array($hab['categoria_nombre'], $categorias)) {
        $categorias[] = $hab['categoria_nombre'];
    }
}

// Helpers
$fmt_fecha = fn($f) => date('d M Y', strtotime($f));

$estado_cfg = [
    1 => ['label' => 'Activa', 'class' => 'estado-confirmada'],
    2 => ['label' => 'Pendiente', 'class' => 'estado-pendiente'],
    3 => ['label' => 'Completada', 'class' => 'estado-completada'],
    4 => ['label' => 'Cancelada', 'class' => 'estado-cancelada'],
];

$pago_icon = ['Bancolombia' => '🏦', 'Nequi' => '💜', 'Daviplata' => '❤️'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mis Reservas · Hotel Viña del Mar</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/homes2.css"/>
  <link rel="stylesheet" href="css/reservas.css"/>
  <link rel="icon" href="img/recurso.png" type="image/png"/>
</head>
<body>

<!-- ============================================================
  NAVBAR
============================================================ -->
<nav class="navbar">
  <div class="navbar-inner">
    <a href="home2.php" class="navbar-logo">
      <img src="img/logo.png" alt="Logo" class="logo-img"
          onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
      <div class="logo-fallback" style="display:none;">
        <span class="logo-icon">✦</span>
        <span class="logo-text">VIÑA DEL MAR</span>
      </div>
    </a>

    <ul class="navbar-menu">
      <li><a href="index.php?action=getFormInicioExitoso"class="nav-link">Inicio</a></li>
      <li><a href="index.php?action=getFormInicioExitosoServicios"class="nav-link">Servicios</a></li>
      <li><a href="index.php?action=getFormInicioExitosoHabitaciones"  class="nav-link">Habitaciones</a></li>
      <li><a href="index.php?action=getFormInicioExitosoReservas"  class="nav-link nav-link--active">Mis Reservas</a></li>
    </ul>

    <div class="navbar-actions">
      <span class="nav-username"><?= $nombre ?></span>
      <a href="index.php?action=cerrarSesion" class="btn-logout">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
        </svg>
        Cerrar Sesión
      </a>
    </div>

    <button class="hamburger" id="hamburger">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <a href="home2.php#inicio"       class="mobile-link">Inicio</a>
    <a href="home2.php#servicios"    class="mobile-link">Servicios</a>
    <a href="home2.php#habitaciones" class="mobile-link">Habitaciones</a>
    <a href="reservas.php"           class="mobile-link">Mis Reservas</a>
    <a href="index.php?action=cerrarSesion" class="mobile-link mobile-link--logout">Cerrar Sesión</a>
  </div>
</nav>

<!-- ============================================================
    HERO BANNER
============================================================ -->
<header class="page-hero">
  <div class="page-hero-bg"></div>
  <div class="page-hero-overlay"></div>
  <div class="page-hero-content">
    <p class="page-hero-tag">Área personal</p>
    <h1 class="page-hero-title">Mis Reservas</h1>
    <p class="page-hero-sub">
      Aquí encuentras el historial y estado de todas tus reservas.
    </p>
  </div>
</header>

<!-- ============================================================
    CONTENIDO PRINCIPAL
============================================================ -->
<main class="main-content">
  <div class="content-inner">

    <!-- ── Estadísticas rápidas ── -->
    <?php if ($hay_reservas): ?>
    <div class="stats-row">
      <?php
      $total_reservas  = count($reservas);
      $confirmadas     = count(array_filter($reservas, fn($r) => (int)$r['estado'] === 1));
      $pendientes      = count(array_filter($reservas, fn($r) => (int)$r['estado'] === 2));
      $completadas     = count(array_filter($reservas, fn($r) => (int)$r['estado'] === 3));
      ?>
      <div class="stat-card">
        <span class="stat-icon">🗓️</span>
        <div>
          <p class="stat-num"><?= $total_reservas ?></p>
          <p class="stat-label">Total reservas</p>
        </div>
      </div>
      <div class="stat-card stat-card--green">
        <span class="stat-icon">✅</span>
        <div>
          <p class="stat-num"><?= $confirmadas ?></p>
          <p class="stat-label">Confirmadas</p>
        </div>
      </div>
      <div class="stat-card stat-card--yellow">
        <span class="stat-icon">⏳</span>
        <div>
          <p class="stat-num"><?= $pendientes ?></p>
          <p class="stat-label">Pendientes</p>
        </div>
      </div>
      <div class="stat-card stat-card--gray">
        <span class="stat-icon">🏁</span>
        <div>
          <p class="stat-num"><?= $completadas ?></p>
          <p class="stat-label">Completadas</p>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ── Barra de acciones ── -->
    <div class="toolbar">
      <h2 class="toolbar-title">
        <?= $hay_reservas ? 'Historial de reservas' : 'Sin reservas' ?>
      </h2>
      <div class="toolbar-actions">
        <button type="button" class="btn-nueva-reserva" onclick="abrirModalNuevaReserva()">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          Nueva Reserva
        </button>
        <button class="btn-reporte" onclick="generarReporte()">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
          </svg>
          Reporte General
        </button>
      </div>
    </div>

    <!-- ── Estado vacío ── -->
    <?php if (!$hay_reservas): ?>
    <div class="empty-state">
      <div class="empty-icon">🛏️</div>
      <h3 class="empty-title">Aún no tienes reservas</h3>
      <p class="empty-msg">Cuando realices una reserva aparecerá aquí con todos sus detalles.</p>
      <button type="button" class="btn-nueva-reserva" onclick="abrirModalNuevaReserva()">Explorar Habitaciones</button>
    </div>

    <!-- ── Tabla de reservas ── -->
    <?php else: ?>

    <!-- Vista desktop: tabla -->
    <div class="table-wrap">
      <table class="reservas-table">
        <thead>
          <tr>
            <th>Habitación</th>
            <th>Fechas</th>
            <th>Personas</th>
            <th>Pago</th>
            <th>Total</th>
            <th>Estado</th>
            <th class="th-acciones">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservas as $r):
            $ec = $estado_cfg[$r['estado']] ?? ['label'=>$r['estado'],'class'=>''];
          ?>
          <tr class="res-row" data-id="<?= $r['id'] ?>">

            <!-- Habitación -->
            <td class="td-hab">
              <div class="hab-cell">
                <img src="<?= $r['img'] ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80' ?>" alt="Habitación <?= htmlspecialchars($r['habitacion']) ?>" class="hab-thumb"/>
                <div>
                  <p class="hab-nombre">Habitación <?= htmlspecialchars($r['habitacion']) ?></p>
                  <span class="hab-tipo"><?= htmlspecialchars($r['tipo']) ?></span>
                  <span class="res-id">#<?= $r['id'] ?></span>
                </div>
              </div>
            </td>

            <!-- Fechas -->
            <td class="td-fechas">
              <div class="fechas-cell">
                <div class="fecha-item">
                  <span class="fecha-lbl">✈️ Entrada</span>
                  <span class="fecha-val"><?= $fmt_fecha($r['entrada']) ?></span>
                </div>
                <div class="fecha-sep">→</div>
                <div class="fecha-item">
                  <span class="fecha-lbl">🏁 Salida</span>
                  <span class="fecha-val"><?= $fmt_fecha($r['salida']) ?></span>
                </div>
                <span class="noches-badge"><?= $r['noches'] ?> noches</span>
              </div>
            </td>

            <!-- Personas -->
            <td class="td-center">
              <span class="personas-cell">
                <?= str_repeat('👤', $r['personas']) ?>
                <span class="personas-num"><?= $r['personas'] ?></span>
              </span>
            </td>

            <!-- Pago -->
            <td class="td-center">
              <span class="pago-cell">
                <?= $pago_icon[$r['pago']] ?? '💳' ?> <?= $r['pago'] ?>
              </span>
            </td>

            <!-- Total -->
            <td class="td-total">
              $<?= number_format($r['total'], 0, ',', '.') ?>
            </td>

            <!-- Estado -->
            <td class="td-center">
              <span class="estado-badge <?= $ec['class'] ?>">
                <?= $ec['label'] ?>
              </span>
            </td>

            <!-- Acciones -->
            <td class="td-acciones">
              <div class="acciones-wrap">
                <button class="btn-accion btn-editar"
                        title="Editar reserva"
                        onclick="editarReserva('<?= $r['id'] ?>')">
                  <!-- Ícono lápiz -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-1.414.586H8v-2a2 2 0 01.586-1.414z"/>
                  </svg>
                </button>

                <button class="btn-accion btn-descargar"
                        title="Descargar reserva"
                        onclick="descargarReserva('<?= $r['id'] ?>', '<?= addslashes($r['habitacion']) ?>', '<?= $r['entrada'] ?>', '<?= $r['salida'] ?>', '<?= $r['total'] ?>', '<?= $r['pago'] ?>')">
                  <!-- Ícono descarga -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5 5 5-5M12 3v12"/>
                  </svg>
                </button>

                <button class="btn-accion btn-borrar"
                        title="Cancelar reserva"
                        onclick="confirmarBorrar('<?= $r['id'] ?>')">
                  <!-- Ícono caneca -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>

          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Vista móvil: cards -->
    <div class="mobile-cards">
      <?php foreach ($reservas as $r):
        $ec = $estado_cfg[$r['estado']] ?? ['label'=>$r['estado'],'class'=>''];
      ?>
      <div class="mob-card">
        <div class="mob-card-header">
          <img src="<?= $r['img'] ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80' ?>" alt="" class="mob-thumb"/>
          <div class="mob-card-info">
            <p class="mob-hab-nombre"><?= $r['habitacion'] ?></p>
            <span class="hab-tipo"><?= $r['tipo'] ?></span>
            <span class="res-id">#<?= $r['id'] ?></span>
          </div>
          <span class="estado-badge <?= $ec['class'] ?>"><?= $ec['label'] ?></span>
        </div>
        <div class="mob-card-body">
          <div class="mob-row">
            <span class="mob-lbl">📅 Fechas</span>
            <span class="mob-val"><?= $fmt_fecha($r['entrada']) ?> → <?= $fmt_fecha($r['salida']) ?> (<?= $r['noches'] ?> noches)</span>
          </div>
          <div class="mob-row">
            <span class="mob-lbl">👥 Personas</span>
            <span class="mob-val"><?= $r['personas'] ?></span>
          </div>
          <div class="mob-row">
            <span class="mob-lbl">💳 Pago</span>
            <span class="mob-val"><?= $pago_icon[$r['pago']] ?? '' ?> <?= $r['pago'] ?></span>
          </div>
          <div class="mob-row mob-row--total">
            <span class="mob-lbl">💰 Total</span>
            <span class="mob-total">$<?= number_format($r['total'], 0, ',', '.') ?></span>
          </div>
        </div>
        <div class="mob-card-footer">
          <button class="btn-accion btn-editar"    title="Editar"    onclick="editarReserva('<?= $r['id'] ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-1.414.586H8v-2a2 2 0 01.586-1.414z"/></svg>
            Editar
          </button>
          <button class="btn-accion btn-descargar" title="Descargar" onclick="descargarReserva('<?= $r['id'] ?>', '<?= addslashes($r['habitacion']) ?>', '<?= $r['entrada'] ?>', '<?= $r['salida'] ?>', '<?= $r['total'] ?>', '<?= $r['pago'] ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5 5 5-5M12 3v12"/></svg>
            Descargar
          </button>
          <button class="btn-accion btn-borrar"    title="Cancelar"  onclick="confirmarBorrar('<?= $r['id'] ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
            Cancelar
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>
  </div>
</main>

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
            <div class="cal-grid" id="cal-grid"></div>
          </div>
          <div class="fechas-display" id="fechas-texto">
            <span class="fecha-chip-hint">Selecciona entrada y salida</span>
          </div>
          <input type="hidden" name="fecha_inicio" id="fecha_inicio">
          <input type="hidden" name="fecha_final" id="fecha_fin">
        </div>

        <!-- Personas -->
        <div class="form-section">
          <p class="form-section-title">👥 Número de personas</p>
          <div class="personas-selector">
            <button type="button" class="personas-btn" onclick="cambiarPersonas(-1)">−</button>
            <span class="personas-display">
              <span id="personas-num" class="personas-num">1</span>
              <span class="personas-label">persona<span id="personas-plural" class="personas-plural" style="display:none;">s</span></span>
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

        <!-- Botón enviar -->
        <button type="submit" class="btn-reservar" id="btn-reservar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          Reservar Ahora
        </button>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================
  MODAL DE NUEVA RESERVA GENERAL
============================================================ -->
<div id="modalNuevaReserva" class="modal-backdrop" onclick="cerrarModalNuevaReserva(event)">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-nueva-title">
     <!-- Botón cerrar -->
        <button class="modal-close" onclick="cerrarModalNuevaReserva(null, true)" aria-label="Cerrar">✕</button>

        <!-- Encabezado -->
        <div class="modal-header">
      <div class="modal-header-img-wrap">
        <img id="modal-nueva-img" src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80" alt="" class="modal-header-img"/>
        <div class="modal-header-overlay"></div>
      </div>
      <div class="modal-header-info">
        <span class="modal-hab-tipo">Nueva Reserva</span>
        <h2 id="modal-nueva-title" class="modal-hab-nombre">Selecciona tu habitación</h2>
        <div class="modal-precio-display">
          <span class="modal-precio-label">Precio por noche</span>
          <span id="modal-nueva-precio-noche" class="modal-precio-valor">—</span>
        </div>
      </div>
    </div>

    <!-- Cuerpo del formulario -->
    <div class="modal-body">

      <!-- Resumen de precio dinámico -->
      <div class="modal-resumen" id="modal-nueva-resumen">
        <div class="resumen-row">
          <span class="resumen-label">🏨 Habitación</span>
          <span id="res-nueva-nombre" class="resumen-valor">—</span>
        </div>
        <div class="resumen-row">
          <span class="resumen-label">📅 Noches</span>
          <span id="res-nueva-noches" class="resumen-valor">—</span>
        </div>
        <div class="resumen-row resumen-total">
          <span class="resumen-label">💰 Total estimado</span>
          <span id="res-nueva-total" class="resumen-valor resumen-total-val">—</span>
        </div>
      </div>

      <form id="formNuevaReserva" class="modal-form" action="index.php?action=reservarHabitacion" method="POST">
        <input type="hidden" name="id_habitacion" id="input-nueva-hab-id">
        <input type="hidden" name="precio" id="input-nueva-precio-hidden">

        <!-- Seleccionar categoría -->
        <div class="form-section">
          <p class="form-section-title">🏷️ Categoría de habitación</p>
          <select id="select-categoria" class="form-select" onchange="cambiarCategoria()">
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Seleccionar habitación -->
        <div class="form-section">
          <p class="form-section-title">🏨 Habitación específica</p>
          <select id="select-habitacion" class="form-select" onchange="cambiarHabitacion()" disabled>
            <option value="">Primero selecciona una categoría</option>
          </select>
        </div>

        <!-- Calendario: fechas -->
        <div class="form-section">
          <p class="form-section-title">📅 Fechas de estadía</p>
          <div class="calendar-wrap">
            <div class="cal-month-nav">
              <button type="button" class="cal-nav-btn" id="cal-nueva-prev" onclick="cambiarMesNueva(-1)">‹</button>
              <span class="cal-month-label" id="cal-nueva-month-label"></span>
              <button type="button" class="cal-nav-btn" id="cal-nueva-next" onclick="cambiarMesNueva(1)">›</button>
            </div>
            <div class="cal-grid" id="cal-nueva-grid"></div>
          </div>
          <div class="fechas-display" id="fechas-nueva-texto">
            <span class="fecha-chip-hint">Selecciona entrada y salida</span>
          </div>
          <input type="hidden" name="fecha_inicio" id="fecha-nueva-inicio">
          <input type="hidden" name="fecha_final" id="fecha-nueva-fin">
        </div>

        <!-- Personas -->
        <div class="form-section">
          <p class="form-section-title">👥 Número de personas</p>
          <div class="personas-selector">
            <button type="button" class="personas-btn" id="personas-nueva-decr" onclick="cambiarPersonasNueva(-1)" disabled>−</button>
            <span class="personas-display">
              <span id="personas-nueva-num" class="personas-num">1</span>
              <span class="personas-label">persona<span id="personas-nueva-plural" class="personas-plural" style="display:none;">s</span></span>
              <span id="personas-nueva-hint" class="personas-hint">Selecciona categoría y habitación para activar</span>
            </span>
            <button type="button" class="personas-btn" id="personas-nueva-incr" onclick="cambiarPersonasNueva(1)" disabled>+</button>
          </div>
          <input type="hidden" name="num_personas" id="personas-nueva-input" value="1">
        </div>

        <!-- Método de pago -->
        <div class="form-section">
          <p class="form-section-title">💳 Método de pago</p>
          <div class="pago-options">
            <label class="pago-option">
              <input type="radio" name="id_metodo_pago" value="1" id="pago-nueva-nequi" checked>
              <span class="pago-icon">💜</span>
              <span class="pago-name">Nequi</span>
            </label>
            <label class="pago-option">
              <input type="radio" name="id_metodo_pago" value="2" id="pago-nueva-daviplata">
              <span class="pago-icon">❤️</span>
              <span class="pago-name">Daviplata</span>
            </label>
            <label class="pago-option">
              <input type="radio" name="id_metodo_pago" value="3" id="pago-nueva-bancolombia">
              <span class="pago-icon">🏦</span>
              <span class="pago-name">Bancolombia</span>
            </label>
          </div>
        </div>

        <!-- Botón enviar -->
        <button type="submit" class="btn-reservar" id="btn-nueva-reservar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          Reservar Ahora
        </button>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================
  MODAL DE EDITAR RESERVA
============================================================ -->
<div id="modalEditar" class="modal-backdrop" onclick="cerrarEditar(event)">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="edit-title">
    <button class="modal-close" onclick="cerrarEditar(null, true)" aria-label="Cerrar">✕</button>

    <div class="modal-header">
      <div class="modal-header-img-wrap">
        <img id="edit-img" src="" alt="" class="modal-header-img"/>
        <div class="modal-header-overlay"></div>
      </div>
      <div class="modal-header-info">
        <span id="edit-tipo" class="modal-hab-tipo"></span>
        <h2 id="edit-title" class="modal-hab-nombre">Editar Reserva</h2>
      </div>
    </div>

    <div class="modal-body">
      <form id="formEditar" class="modal-form">
        <input type="hidden" id="edit-reserva-id">

        <!-- Fechas -->
        <div class="form-section">
          <p class="form-section-title">📅 Fechas de estadía</p>
          <div class="edit-fechas">
            <label>Entrada:
              <input type="date" id="edit-fecha-inicio" required>
            </label>
            <label>Salida:
              <input type="date" id="edit-fecha-fin" required>
            </label>
          </div>
        </div>

        <!-- Personas -->
        <div class="form-section">
          <p class="form-section-title">👥 Número de personas</p>
          <div class="personas-selector">
            <button type="button" class="personas-btn" onclick="cambiarPersonasEdit(-1)">−</button>
            <span class="personas-display">
              <span id="edit-personas-num" class="personas-num">1</span>
              <span class="personas-label">persona<span id="edit-personas-plural" style="display:none;">s</span></span>
            </span>
            <button type="button" class="personas-btn" onclick="cambiarPersonasEdit(1)">+</button>
          </div>
          <input type="hidden" id="edit-personas-input" value="1">
        </div>

        <!-- Método de pago -->
        <div class="form-section">
          <p class="form-section-title">💳 Método de pago</p>
          <div class="pago-options">
            <label class="pago-option">
              <input type="radio" name="pago_edit" value="nequi" id="edit-pago-nequi" checked>
              <span class="pago-icon">💜</span>
              <span class="pago-name">Nequi</span>
            </label>
            <label class="pago-option">
              <input type="radio" name="pago_edit" value="daviplata" id="edit-pago-daviplata">
              <span class="pago-icon">❤️</span>
              <span class="pago-name">Daviplata</span>
            </label>
            <label class="pago-option">
              <input type="radio" name="pago_edit" value="bancolombia" id="edit-pago-bancolombia">
              <span class="pago-icon">🏦</span>
              <span class="pago-name">Bancolombia</span>
            </label>
          </div>
        </div>

        <button type="submit" class="btn-confirmar">Actualizar Reserva</button>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================
  MODAL DE BORRAR
============================================================ -->
<div id="modalDel" class="modal-backdrop" onclick="cerrarModalDel()">
  <div class="modal-box modal-del" role="dialog" aria-modal="true">
    <div class="modal-del-icon">⚠️</div>
    <h3 class="modal-del-title">¿Cancelar reserva?</h3>
    <p class="modal-del-msg">
      Estás a punto de cancelar la reserva <strong id="del-id"></strong>.<br>
      Esta acción no se puede deshacer.
    </p>
    <div class="modal-del-actions">
      <button class="btn-del-cancel" onclick="cerrarModalDel()">Mantener</button>
      <button class="btn-del-confirm" onclick="ejecutarBorrar()">Cancelar Reserva</button>
    </div>
  </div>
</div>

<!-- ============================================================
  TOAST
============================================================ -->
<div id="toast" class="toast">
  <span id="toast-icon"></span>
  <div class="toast-content">
    <div id="toast-title" class="toast-title"></div>
    <div id="toast-sub" class="toast-sub"></div>
  </div>
</div>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
// ── Hamburguesa ───────────────────────────────────────────────
const hamburger  = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu.classList.toggle('open');
});

// ── Modal de Reserva General ──────────────────────────────────
let precioActual = 0;
let currentViewDate = new Date();
let fechaInicio = null;
let fechaFin = null;

function abrirModalGeneral() {
    document.getElementById('modalReserva').classList.add('open');
    document.body.style.overflow = 'hidden';
    renderizarCalendario();
}

function cambiarMes(delta) {
    currentViewDate.setMonth(currentViewDate.getMonth() + delta);
    renderizarCalendario();
}

function renderizarCalendario() {
    const grid = document.getElementById('cal-grid');
    const label = document.getElementById('cal-month-label');
    if (!grid || !label) return;

    grid.innerHTML = '';
    const year = currentViewDate.getFullYear();
    const month = currentViewDate.getMonth();

    const monthName = new Intl.DateTimeFormat('es-ES', { month: 'long', year: 'numeric' }).format(currentViewDate);
    label.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0,0,0,0);

    for (let i = 0; i < firstDay; i++) {
        const div = document.createElement('div');
        div.className = 'cal-cell cal-blank';
        grid.appendChild(div);
    }

    for (let d = 1; d <= totalDays; d++) {
        const dateObj = new Date(year, month, d);
        const dateStr = dateObj.toISOString().split('T')[0];
        const div = document.createElement('div');
        div.className = 'cal-cell';
        div.textContent = d;

        if (dateObj < today) {
            div.classList.add('cal-past');
        } else {
            div.onclick = () => seleccionarFecha(dateStr);
            if (dateStr === fechaInicio) div.classList.add('cal-start');
            if (dateStr === fechaFin) div.classList.add('cal-end');
            if (fechaInicio && fechaFin && dateStr > fechaInicio && dateStr < fechaFin) {
                div.classList.add('cal-range');
            }
        }
        grid.appendChild(div);
    }
}

function seleccionarFecha(fecha) {
    if (!fechaInicio || (fechaInicio && fechaFin)) {
        fechaInicio = fecha;
        fechaFin = null;
        document.getElementById('fechas-texto').innerHTML = `<span class="fecha-chip entrada">Entrada: ${fecha}</span> <span class="fecha-chip-hint">Selecciona salida</span>`;
    } else if (fecha > fechaInicio) {
        fechaFin = fecha;
        document.getElementById('fechas-texto').innerHTML = `<span class="fecha-chip entrada">Entrada: ${fechaInicio}</span> <span class="fecha-chip salida">Salida: ${fechaFin}</span>`;
    } else {
        fechaInicio = fecha;
        fechaFin = null;
    }
    document.getElementById('fecha_inicio').value = fechaInicio || '';
    document.getElementById('fecha_fin').value = fechaFin || '';
    renderizarCalendario();
    calcularTotalGeneral();
}

function cerrarReserva(event, forzar) {
    if (forzar || (event && event.target === document.getElementById('modalReserva'))) {
        document.getElementById('modalReserva').classList.remove('open');
        document.body.style.overflow = '';
    }
}

function calcularTotalGeneral() {
    if (fechaInicio && fechaFin && precioActual > 0) {
        const d1 = new Date(fechaInicio);
        const d2 = new Date(fechaFin);
        const noches = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        const resNoches = document.getElementById('res-noches');
        const resTotal = document.getElementById('res-total');
        if(resNoches) resNoches.textContent = noches > 0 ? noches : '—';
        if(resTotal) resTotal.textContent = (noches > 0) ? '$' + (noches * precioActual).toLocaleString() : '—';
    }
}

// ── Personas ──────────────────────────────────────────────────
function cambiarPersonas(delta) {
    const input = document.getElementById('personas-input');
    const display = document.getElementById('personas-num');
    const plural = document.getElementById('personas-plural');
    let num = parseInt(input.value) + delta;
    num = Math.max(1, Math.min(10, num)); // Min 1, max 10
    input.value = num;
    display.textContent = num;
    plural.style.display = num > 1 ? 'inline' : 'none';
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(icon, title, sub, duration = 4000) {
  document.getElementById('toast-icon').textContent  = icon;
  document.getElementById('toast-title').textContent = title;
  document.getElementById('toast-sub').textContent   = sub;
  const t = document.getElementById('toast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), duration);
}

// ── Editar ────────────────────────────────────────────────────
function editarReserva(id) {
  // Buscar la fila de la reserva por id
  let fila = document.querySelector(`.res-row[data-id='${id}']`);
  if (!fila) {
    // Si es móvil, buscar la card
    fila = document.querySelector(`.mob-card[data-id='${id}']`);
    if (!fila) {
      showToast('⚠️', 'No encontrado', `No se encontró la reserva ${id}`);
      return;
    }
  }

  // Obtener datos de la fila
  let habNombre, habTipo, habImg, entrada, salida, personas, pago;

  if (fila.classList.contains('res-row')) {
    const celdas = fila.querySelectorAll('td');
    habNombre = celdas[0].querySelector('.hab-nombre').textContent;
    habTipo = celdas[0].querySelector('.hab-tipo').textContent;
    habImg = celdas[0].querySelector('img').src;
    const fechas = celdas[1].querySelectorAll('.fecha-val');
    entrada = fechas[0].textContent;
    salida = fechas[1].textContent;
    personas = celdas[2].querySelector('.personas-num').textContent;
    pago = celdas[3].textContent.trim();
  } else {
    // Móvil
    habNombre = fila.querySelector('.hab-nombre').textContent;
    habTipo = fila.querySelector('.hab-tipo').textContent;
    habImg = fila.querySelector('img').src;
    entrada = fila.querySelector('.fecha-entrada').textContent;
    salida = fila.querySelector('.fecha-salida').textContent;
    personas = fila.querySelector('.personas-num').textContent;
    pago = fila.querySelector('.pago-val').textContent.trim();
  }

  // Llenar el modal de edición
  document.getElementById('edit-img').src = habImg;
  document.getElementById('edit-tipo').textContent = habTipo;
  document.getElementById('edit-title').textContent = habNombre;
  document.getElementById('edit-reserva-id').value = id;
  document.getElementById('edit-fecha-inicio').value = entrada;
  document.getElementById('edit-fecha-fin').value = salida;
  document.getElementById('edit-personas-num').textContent = personas;
  document.getElementById('edit-personas-input').value = personas;
  document.getElementById('edit-personas-plural').style.display = personas > 1 ? 'inline' : 'none';

  // Método de pago
  const pagoLower = pago.toLowerCase();
  if (pagoLower.includes('nequi')) document.getElementById('edit-pago-nequi').checked = true;
  else if (pagoLower.includes('daviplata')) document.getElementById('edit-pago-daviplata').checked = true;
  else if (pagoLower.includes('bancolombia')) document.getElementById('edit-pago-bancolombia').checked = true;

  // Abrir el modal
  document.getElementById('modalEditar').classList.add('open');
  document.body.style.overflow = 'hidden';
  showToast('✏️', 'Editar reserva', `Editando reserva #${id}`);
}

function cerrarEditar(event, forzar) {
  if (forzar || (event && event.target === document.getElementById('modalEditar'))) {
    document.getElementById('modalEditar').classList.remove('open');
    document.body.style.overflow = '';
  }
}

function cambiarPersonasEdit(delta) {
  const input = document.getElementById('edit-personas-input');
  const display = document.getElementById('edit-personas-num');
  const plural = document.getElementById('edit-personas-plural');
  let num = parseInt(input.value) + delta;
  num = Math.max(1, num); // Min 1
  input.value = num;
  display.textContent = num;
  plural.style.display = num > 1 ? 'inline' : 'none';
}

// Submit del form editar
document.getElementById('formEditar').addEventListener('submit', function(e) {
  e.preventDefault();
  const id = document.getElementById('edit-reserva-id').value;
  const fechaInicio = document.getElementById('edit-fecha-inicio').value;
  const fechaFin = document.getElementById('edit-fecha-fin').value;
  const personas = document.getElementById('edit-personas-input').value;
  const pago = document.querySelector('input[name="pago_edit"]:checked').value;

  fetch('index.php?action=actualizarReserva', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `reserva_id=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&personas=${personas}&pago_edit=${pago}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      cerrarEditar(null, true);
      showToast('✅', 'Reserva actualizada', `Reserva #${id} actualizada correctamente.`);
      // Recargar la página para mostrar cambios
      setTimeout(() => location.reload(), 1500);
    } else {
      showToast('❌', 'Error', 'No se pudo actualizar la reserva.');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('❌', 'Error', 'Error de conexión.');
  });
});

// ── Descargar ─────────────────────────────────────────────────
function descargarReserva(id, hab, entrada, salida, total, pago) {
  const texto = [
    '╔══════════════════════════════════════╗',
    '║   HOTEL VIÑA DEL MAR — RESERVA       ║',
    '╚══════════════════════════════════════╝',
    '',
    `  Código:      ${id}`,
    `  Habitación:  ${hab}`,
    `  Entrada:     ${entrada}`,
    `  Salida:      ${salida}`,
    `  Pago:        ${pago}`,
    `  Total:       ${total}`,
    '',
    '  Gracias por elegirnos.',
    '  reservas@hotelvinadelmar.cl | +56 32 000 0000',
    '',
    '══════════════════════════════════════════',
  ].join('\n');

  const blob = new Blob([texto], { type: 'text/plain;charset=utf-8' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = `Reserva-${id}.txt`;
  a.click();
  URL.revokeObjectURL(url);
  showToast('📄', 'Descarga lista', `Reserva ${id} guardada.`);
}

// ── Borrar ────────────────────────────────────────────────────
let reservaABorrar = null;

function confirmarBorrar(id) {
  reservaABorrar = id;
  document.getElementById('del-id').textContent = id;
  document.getElementById('modalDel').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function cerrarModalDel() {
  document.getElementById('modalDel').classList.remove('open');
  document.body.style.overflow = '';
  reservaABorrar = null;
}

function ejecutarBorrar() {
  const id = reservaABorrar;
  cerrarModalDel();

  // Enviar petición AJAX para eliminar de la base de datos
  fetch('index.php?action=eliminarReserva', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id_reserva=${id}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Animación de salida de la fila/card
      document.querySelectorAll(`[data-id="${id}"]`).forEach(el => {
        el.classList.add('row-removing');
        setTimeout(() => el.remove(), 400);
      });
      showToast('🗑️', 'Reserva cancelada', `La reserva ${id} fue cancelada.`);
      // Recargar para actualizar estadísticas
      setTimeout(() => location.reload(), 1500);
    } else {
      showToast('❌', 'Error', data.message || 'No se pudo cancelar la reserva.');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('❌', 'Error', 'Error de conexión.');
  });
}

// ── Reporte General ───────────────────────────────────────────
function generarReporte() {
  const filas = document.querySelectorAll('.res-row');
  if (filas.length === 0) {
    showToast('⚠️', 'Sin datos', 'No hay reservas para generar un reporte.');
    return;
  }

  const hoy   = new Date().toLocaleDateString('es-CO');
  let lineas  = [
    '╔══════════════════════════════════════════════════╗',
    '║       HOTEL VIÑA DEL MAR — REPORTE GENERAL       ║',
    '╚══════════════════════════════════════════════════╝',
    `  Generado: ${hoy}`,
    `  Total de reservas: ${filas.length}`,
    '',
    '──────────────────────────────────────────────────',
  ];

  filas.forEach(fila => {
    const celdas = fila.querySelectorAll('td');
    lineas.push(
      `  ${fila.dataset.id}  |  ` +
      celdas[0]?.querySelector('.hab-nombre')?.textContent?.trim() + '  |  ' +
      celdas[1]?.querySelector('.fecha-val')?.textContent?.trim() + '  |  ' +
      celdas[4]?.textContent?.trim()
    );
  });

  lineas.push('', '══════════════════════════════════════════════════');

  const blob = new Blob([lineas.join('\n')], { type: 'text/plain;charset=utf-8' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = `Reporte-Reservas-${hoy}.txt`;
  a.click();
  URL.revokeObjectURL(url);
  showToast('📊', 'Reporte descargado', `Reporte general generado el ${hoy}.`);
}

// Cerrar modal borrar al click fuera
document.getElementById('modalDel').addEventListener('click', e => {
  if (e.target === document.getElementById('modalDel')) cerrarModalDel();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') cerrarModalDel();
});

// Cerrar modal nueva reserva al click fuera
document.getElementById('modalNuevaReserva').addEventListener('click', e => {
  if (e.target === document.getElementById('modalNuevaReserva')) cerrarModalNuevaReserva();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') cerrarModalNuevaReserva();
});
</script>

<script>
// ── Datos de habitaciones ─────────────────────────────────────
const habitaciones = <?php echo json_encode($habitaciones_lista); ?>;

// ── Modal Nueva Reserva ──────────────────────────────────────
let precioNuevaActual = 0;
let currentNuevaViewDate = new Date();
let fechaNuevaInicio = null;
let fechaNuevaFin = null;

function abrirModalNuevaReserva() {
    document.getElementById('modalNuevaReserva').classList.add('open');
    document.body.style.overflow = 'hidden';
    renderizarCalendarioNueva();
}

function cerrarModalNuevaReserva(event, forzar) {
    if (forzar || (event && event.target === document.getElementById('modalNuevaReserva'))) {
        document.getElementById('modalNuevaReserva').classList.remove('open');
        document.body.style.overflow = '';
        // Resetear
        document.getElementById('select-categoria').value = '';
        document.getElementById('select-habitacion').innerHTML = '<option value="">Primero selecciona una categoría</option>';
        document.getElementById('select-habitacion').disabled = true;
        document.getElementById('modal-nueva-img').src = 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80';
        document.getElementById('modal-nueva-title').textContent = 'Selecciona tu habitación';
        document.getElementById('modal-nueva-precio-noche').textContent = '—';
        document.getElementById('res-nueva-nombre').textContent = '—';
        document.getElementById('res-nueva-noches').textContent = '—';
        document.getElementById('res-nueva-total').textContent = '—';
        fechaNuevaInicio = null;
        fechaNuevaFin = null;
        document.getElementById('fechas-nueva-texto').innerHTML = '<span class="fecha-chip-hint">Selecciona entrada y salida</span>';
        document.getElementById('personas-nueva-num').textContent = '1';
        document.getElementById('personas-nueva-input').value = '1';
        document.getElementById('personas-nueva-plural').style.display = 'none';
        document.getElementById('personas-nueva-decr').disabled = true;
        document.getElementById('personas-nueva-incr').disabled = true;
        document.getElementById('personas-nueva-hint').textContent = 'Selecciona categoría y habitación para activar';
    }
}

function cambiarCategoria() {
    const categoria = document.getElementById('select-categoria').value;
    const selectHab = document.getElementById('select-habitacion');
    selectHab.innerHTML = '<option value="">Selecciona una habitación</option>';
    if (categoria) {
        const habsFiltradas = habitaciones.filter(h => h.categoria_nombre === categoria);
        habsFiltradas.forEach(h => {
            const option = document.createElement('option');
            option.value = h.id;
            option.textContent = `${h.nombre} - ${h.descripcion}`;
            selectHab.appendChild(option);
        });
        selectHab.disabled = false;
    } else {
        selectHab.disabled = true;
    }
    cambiarHabitacion(); // Reset
}

function cambiarHabitacion() {
    const habId = document.getElementById('select-habitacion').value;
    const hab = habitaciones.find(h => h.id == habId);
    if (hab) {
        document.getElementById('modal-nueva-img').src = hab.img || 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80';
        document.getElementById('modal-nueva-title').textContent = hab.nombre;
        document.getElementById('modal-nueva-precio-noche').textContent = '$' + hab.precio.toLocaleString();
        document.getElementById('res-nueva-nombre').textContent = hab.nombre;
        document.getElementById('input-nueva-hab-id').value = hab.id;
        document.getElementById('input-nueva-precio-hidden').value = hab.precio;
        precioNuevaActual = hab.precio;
        // Activar el selector de personas cuando hay habitación
        actualizarPersonasNuevaControls();
        // Validar max personas
        const maxPersonas = hab.max_personas;
        const currentPersonas = parseInt(document.getElementById('personas-nueva-input').value);
        if (currentPersonas > maxPersonas) {
            document.getElementById('personas-nueva-num').textContent = maxPersonas;
            document.getElementById('personas-nueva-input').value = maxPersonas;
            document.getElementById('personas-nueva-plural').style.display = maxPersonas > 1 ? 'inline' : 'none';
            document.getElementById('personas-nueva-hint').textContent = `Máximo ${maxPersonas} persona${maxPersonas > 1 ? 's' : ''}`;
            showToast('⚠️', 'Máximo de personas', `Esta habitación permite máximo ${maxPersonas} persona${maxPersonas > 1 ? 's' : ''}.`);
        } else {
            document.getElementById('personas-nueva-hint').textContent = fechaNuevaInicio && fechaNuevaFin ? 'Ajusta el número de personas' : 'Selecciona fechas para continuar';
        }
    } else {
        document.getElementById('modal-nueva-img').src = 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80';
        document.getElementById('modal-nueva-title').textContent = 'Selecciona tu habitación';
        document.getElementById('modal-nueva-precio-noche').textContent = '—';
        document.getElementById('res-nueva-nombre').textContent = '—';
        document.getElementById('input-nueva-hab-id').value = '';
        document.getElementById('input-nueva-precio-hidden').value = '';
        precioNuevaActual = 0;
    }
    calcularTotalNueva();
}

function cambiarMesNueva(delta) {
    currentNuevaViewDate.setMonth(currentNuevaViewDate.getMonth() + delta);
    renderizarCalendarioNueva();
}

function renderizarCalendarioNueva() {
    const grid = document.getElementById('cal-nueva-grid');
    const label = document.getElementById('cal-nueva-month-label');
    if (!grid || !label) return;

    grid.innerHTML = '';
    const year = currentNuevaViewDate.getFullYear();
    const month = currentNuevaViewDate.getMonth();

    const monthName = new Intl.DateTimeFormat('es-ES', { month: 'long', year: 'numeric' }).format(currentNuevaViewDate);
    label.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0,0,0,0);

    for (let i = 0; i < firstDay; i++) {
        const div = document.createElement('div');
        div.className = 'cal-cell cal-blank';
        grid.appendChild(div);
    }

    for (let d = 1; d <= totalDays; d++) {
        const dateObj = new Date(year, month, d);
        const dateStr = dateObj.toISOString().split('T')[0];
        const div = document.createElement('div');
        div.className = 'cal-cell';
        div.textContent = d;

        if (dateObj < today) {
            div.classList.add('cal-past');
        } else {
            div.onclick = () => seleccionarFechaNueva(dateStr);
            if (dateStr === fechaNuevaInicio) div.classList.add('cal-start');
            if (dateStr === fechaNuevaFin) div.classList.add('cal-end');
            if (fechaNuevaInicio && fechaNuevaFin && dateStr > fechaNuevaInicio && dateStr < fechaNuevaFin) {
                div.classList.add('cal-range');
            }
        }
        grid.appendChild(div);
    }
}

function seleccionarFechaNueva(fecha) {
    if (!fechaNuevaInicio || (fechaNuevaInicio && fechaNuevaFin)) {
        fechaNuevaInicio = fecha;
        fechaNuevaFin = null;
        document.getElementById('fechas-nueva-texto').innerHTML = `<span class="fecha-chip entrada">Entrada: ${fecha}</span> <span class="fecha-chip-hint">Selecciona salida</span>`;
    } else if (fecha > fechaNuevaInicio) {
        fechaNuevaFin = fecha;
        document.getElementById('fechas-nueva-texto').innerHTML = `<span class="fecha-chip entrada">Entrada: ${fechaNuevaInicio}</span> <span class="fecha-chip salida">Salida: ${fechaNuevaFin}</span>`;
    } else {
        fechaNuevaInicio = fecha;
        fechaNuevaFin = null;
    }
    document.getElementById('fecha-nueva-inicio').value = fechaNuevaInicio || '';
    document.getElementById('fecha-nueva-fin').value = fechaNuevaFin || '';
    renderizarCalendarioNueva();
    calcularTotalNueva();
}

function cambiarPersonasNueva(delta) {
    const habId = document.getElementById('select-habitacion').value;
    const hab = habitaciones.find(h => h.id == habId);
    if (!hab) return;

    const input = document.getElementById('personas-nueva-input');
    const display = document.getElementById('personas-nueva-num');
    const plural = document.getElementById('personas-nueva-plural');
    let num = parseInt(input.value) + delta;
    const max = hab.max_personas;
    num = Math.max(1, Math.min(max, num));
    input.value = num;
    display.textContent = num;
    plural.style.display = num > 1 ? 'inline' : 'none';

    const hint = document.getElementById('personas-nueva-hint');
    if (num >= max) {
        hint.textContent = `Máximo ${max} persona${max > 1 ? 's' : ''}`;
    } else {
        hint.textContent = fechaNuevaInicio && fechaNuevaFin ? 'Ajusta el número de personas' : 'Selecciona fechas para continuar';
    }
}

function actualizarPersonasNuevaControls() {
    const habId = document.getElementById('select-habitacion').value;
    const hab = habitaciones.find(h => h.id == habId);
    const btnMinus = document.getElementById('personas-nueva-decr');
    const btnPlus = document.getElementById('personas-nueva-incr');
    const hint = document.getElementById('personas-nueva-hint');

    if (!hab) {
        btnMinus.disabled = true;
        btnPlus.disabled = true;
        hint.textContent = 'Selecciona categoría y habitación para activar';
        return;
    }

    btnMinus.disabled = false;
    btnPlus.disabled = false;
    const currentNum = parseInt(document.getElementById('personas-nueva-input').value);
    if (currentNum >= hab.max_personas) {
        hint.textContent = `Máximo ${hab.max_personas} persona${hab.max_personas > 1 ? 's' : ''}`;
    } else {
        hint.textContent = fechaNuevaInicio && fechaNuevaFin ? 'Ajusta el número de personas' : 'Selecciona fechas para continuar';
    }
}

function calcularTotalNueva() {
    if (fechaNuevaInicio && fechaNuevaFin && precioNuevaActual > 0) {
        const d1 = new Date(fechaNuevaInicio);
        const d2 = new Date(fechaNuevaFin);
        const noches = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        const resNoches = document.getElementById('res-nueva-noches');
        const resTotal = document.getElementById('res-nueva-total');
        if(resNoches) resNoches.textContent = noches > 0 ? noches : '—';
        if(resTotal) resTotal.textContent = (noches > 0) ? '$' + (noches * precioNuevaActual).toLocaleString() : '—';
    }
}

function actualizarPagoSeleccionado() {
    document.querySelectorAll('.pago-option').forEach(label => {
        const input = label.querySelector('input[type="radio"]');
        label.classList.toggle('selected', input && input.checked);
    });
}

document.querySelectorAll('.pago-option input[type="radio"]').forEach(input => {
    input.addEventListener('change', actualizarPagoSeleccionado);
});

actualizarPagoSeleccionado();
</script>

</body>
</html>