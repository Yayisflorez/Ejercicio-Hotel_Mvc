<?php
// ============================================================
// reservas.php — Gestión de reservas del usuario autenticado
// ============================================================

$nombre = htmlspecialchars($_SESSION['usuario']['nombre'] ?? $_SESSION['usuario_nombre'] ?? 'Huésped');

// -------------------------------------------------------
// DATOS DE EJEMPLO — En producción reemplazar por consulta
// a la BD: SELECT * FROM reservas WHERE usuario_id = ?
// -------------------------------------------------------
$reservas = [
    [
        'id'        => 'RES-0012',
        'habitacion'=> 'Suite Mar',
        'tipo'      => 'Superior',
        'img'       => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=200&q=60',
        'entrada'   => '2025-07-10',
        'salida'    => '2025-07-14',
        'noches'    => 4,
        'personas'  => 2,
        'pago'      => 'Bancolombia',
        'total'     => '$1.280',
        'estado'    => 'confirmada',
    ],
    [
        'id'        => 'RES-0009',
        'habitacion'=> 'Suite Presidencial',
        'tipo'      => 'Deluxe',
        'img'       => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=200&q=60',
        'entrada'   => '2025-08-01',
        'salida'    => '2025-08-05',
        'noches'    => 4,
        'personas'  => 3,
        'pago'      => 'Nequi',
        'total'     => '$2.600',
        'estado'    => 'pendiente',
    ],
    [
        'id'        => 'RES-0005',
        'habitacion'=> 'Habitación Clásica',
        'tipo'      => 'Estándar',
        'img'       => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=200&q=60',
        'entrada'   => '2025-05-20',
        'salida'    => '2025-05-22',
        'noches'    => 2,
        'personas'  => 1,
        'pago'      => 'Daviplata',
        'total'     => '$360',
        'estado'    => 'completada',
    ],
];

$hay_reservas = count($reservas) > 0;

// Helpers
$fmt_fecha = fn($f) => date('d M Y', strtotime($f));

$estado_cfg = [
    'confirmada' => ['label' => 'Confirmada', 'class' => 'estado-confirmada'],
    'pendiente'  => ['label' => 'Pendiente',  'class' => 'estado-pendiente'],
    'completada' => ['label' => 'Completada', 'class' => 'estado-completada'],
    'cancelada'  => ['label' => 'Cancelada',  'class' => 'estado-cancelada'],
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
      Bienvenido, <strong><?= $nombre ?></strong> — Aquí encuentras el historial y estado de todas tus reservas.
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
      $confirmadas     = count(array_filter($reservas, fn($r) => $r['estado'] === 'confirmada'));
      $pendientes      = count(array_filter($reservas, fn($r) => $r['estado'] === 'pendiente'));
      $completadas     = count(array_filter($reservas, fn($r) => $r['estado'] === 'completada'));
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
        <a href="home2.php#habitaciones" class="btn-nueva-reserva">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          Nueva Reserva
        </a>
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
      <a href="home2.php#habitaciones" class="btn-nueva-reserva">Explorar Habitaciones</a>
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
                <img src="<?= $r['img'] ?>" alt="<?= $r['habitacion'] ?>" class="hab-thumb"/>
                <div>
                  <p class="hab-nombre"><?= $r['habitacion'] ?></p>
                  <span class="hab-tipo"><?= $r['tipo'] ?></span>
                  <span class="res-id"><?= $r['id'] ?></span>
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
              <?= $r['total'] ?>
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 3v12"/>
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
          <img src="<?= $r['img'] ?>" alt="" class="mob-thumb"/>
          <div class="mob-card-info">
            <p class="mob-hab-nombre"><?= $r['habitacion'] ?></p>
            <span class="hab-tipo"><?= $r['tipo'] ?></span>
            <span class="res-id"><?= $r['id'] ?></span>
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
            <span class="mob-total"><?= $r['total'] ?></span>
          </div>
        </div>
        <div class="mob-card-footer">
          <button class="btn-accion btn-editar"    title="Editar"    onclick="editarReserva('<?= $r['id'] ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-1.414.586H8v-2a2 2 0 01.586-1.414z"/></svg>
            Editar
          </button>
          <button class="btn-accion btn-descargar" title="Descargar" onclick="descargarReserva('<?= $r['id'] ?>', '<?= addslashes($r['habitacion']) ?>', '<?= $r['entrada'] ?>', '<?= $r['salida'] ?>', '<?= $r['total'] ?>', '<?= $r['pago'] ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 3v12"/></svg>
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
     MODAL CONFIRMACIÓN BORRAR
     ============================================================ -->
<div class="modal-del-backdrop" id="modalDel">
  <div class="modal-del-box">
    <div class="modal-del-icon">🗑️</div>
    <h3 class="modal-del-title">¿Cancelar reserva?</h3>
    <p class="modal-del-msg">Esta acción no se puede deshacer. La reserva <strong id="del-id"></strong> será cancelada permanentemente.</p>
    <div class="modal-del-btns">
      <button class="btn-del-cancel" onclick="cerrarModalDel()">No, mantener</button>
      <button class="btn-del-confirm" onclick="ejecutarBorrar()">Sí, cancelar</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <span id="toast-icon">✅</span>
  <div>
    <p class="toast-title" id="toast-title">Acción realizada</p>
    <p class="toast-sub"   id="toast-sub"></p>
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
  showToast('✏️', 'Editar reserva', `Abriendo formulario para ${id}…`);
  // En producción: redirigir a editar_reserva.php?id=id
}

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
  // Animación de salida de la fila/card
  document.querySelectorAll(`[data-id="${id}"]`).forEach(el => {
    el.classList.add('row-removing');
    setTimeout(() => el.remove(), 400);
  });
  showToast('🗑️', 'Reserva cancelada', `La reserva ${id} fue cancelada.`);
  // En producción: fetch(`delete_reserva.php?id=${id}`, { method:'POST' })
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
</script>

</body>
</html>