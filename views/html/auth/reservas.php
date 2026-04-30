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
  <link rel="stylesheet" href="css/home2.css"/>
  <link rel="stylesheet" href="css/reserva.css"/>
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
          <tr class="res-row" data-id="<?= $r['id'] ?>" data-max-personas="<?= $r['max_personas'] ?>" data-id-habitacion="<?= $r['id_habitacion'] ?>" data-categoria="<?= htmlspecialchars($r['tipo']) ?>" data-precio-noche="<?= $r['precio_noche'] ?>">

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
                  <span class="fecha-val" data-iso="<?= $r['entrada'] ?>"><?= $fmt_fecha($r['entrada']) ?></span>
                </div>
                <div class="fecha-sep">→</div>
                <div class="fecha-item">
                  <span class="fecha-lbl">🏁 Salida</span>
                  <span class="fecha-val" data-iso="<?= $r['salida'] ?>"><?= $fmt_fecha($r['salida']) ?></span>
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
  <div class="modal-box modal-edit-grande" role="dialog" aria-modal="true" aria-labelledby="edit-title">
    <button class="modal-close" onclick="cerrarEditar(null, true)" aria-label="Cerrar">✕</button>

    <div class="modal-header">
      <div class="modal-header-img-wrap">
        <img id="edit-img" src="" alt="" class="modal-header-img"/>
        <div class="modal-header-overlay"></div>
      </div>
      <div class="modal-header-info">
        <span id="edit-tipo" class="modal-hab-tipo"></span>
        <h2 id="edit-title" class="modal-hab-nombre">Editar Reserva</h2>
        <p class="edit-sub">Ajusta las fechas, personas y método de pago</p>
      </div>
    </div>

    <div class="modal-body">
      <form id="formEditar" class="modal-form">
        <input type="hidden" id="edit-reserva-id">
        <input type="hidden" id="edit-hab-id">
        <input type="hidden" id="edit-precio-hidden">

      <!-- Resumen de precio dinámico -->
      <div class="modal-resumen" id="modal-edit-resumen">
        <div class="resumen-row">
          <span class="resumen-label">🏨 Habitación</span>
          <span id="res-edit-nombre" class="resumen-valor">—</span>
        </div>
        <div class="resumen-row">
          <span class="resumen-label">📅 Noches</span>
          <span id="res-edit-noches" class="resumen-valor">—</span>
        </div>
        <div class="resumen-row resumen-total">
          <span class="resumen-label">💰 Total estimado</span>
          <span id="res-edit-total" class="resumen-valor resumen-total-val">—</span>
        </div>
      </div>

        <!-- Seleccionar categoría -->
        <div class="form-section">
          <p class="form-section-title">🏷️ Categoría de habitación</p>
          <select id="edit-select-categoria" class="form-select" onchange="cambiarCategoriaEdit()">
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Seleccionar habitación -->
        <div class="form-section">
          <p class="form-section-title">🏨 Habitación específica</p>
          <select id="edit-select-habitacion" class="form-select" onchange="cambiarHabitacionEdit()">
            <option value="">Primero selecciona una categoría</option>
          </select>
        </div>


        <!-- Calendario de edición -->
        <div class="form-section">
          <p class="form-section-title">📅 Selecciona tus fechas</p>
          <div class="edit-cal-wrap">
            <div class="cal-month-nav">
              <button type="button" class="cal-nav-btn" onclick="cambiarMesEdit(-1)">‹</button>
              <span class="cal-month-label" id="edit-cal-month-label"></span>
              <button type="button" class="cal-nav-btn" onclick="cambiarMesEdit(1)">›</button>
            </div>
            <div class="cal-dias-semana">
              <span>Do</span><span>Lu</span><span>Ma</span><span>Mi</span>
              <span>Ju</span><span>Vi</span><span>Sa</span>
            </div>
            <div class="cal-grid cal-grid--grande" id="edit-cal-grid"></div>
          </div>
          <div class="fechas-display" id="edit-fechas-texto">
            <span class="fecha-chip-hint">Selecciona entrada y salida</span>
          </div>
          <input type="hidden" id="edit-fecha-inicio">
          <input type="hidden" id="edit-fecha-fin">
        </div>

      <!-- Personas -->
      <div class="form-section">
        <p class="form-section-title">👥 Número de personas</p>
        <div class="personas-selector">
          <button type="button" class="personas-btn" onclick="cambiarPersonasEdit(-1)">−</button>
          <span class="personas-display">
            <span id="edit-personas-num" class="personas-num">1</span>
            <span class="personas-label">persona<span id="edit-personas-plural" style="display:none;">s</span></span>
            <span id="edit-personas-hint" class="personas-hint"></span>  <!-- ← agrega esta línea -->
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

        <!-- Botón guardar mejorado -->
        <button type="submit" class="btn-guardar-edit" id="btn-guardar-edit" disabled>
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          <span>Guardar cambios</span>
      </button>

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
// ── Datos de habitaciones ─────────────────────────────────────
const habitaciones = <?php echo json_encode($habitaciones_lista); ?>;
</script>
<script src="js/ScriptReservas.js"></script>
</body>
</html>
