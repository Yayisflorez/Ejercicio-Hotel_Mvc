/**
 * ScriptHome2.js
 * 
 * Propósito:
 * Este archivo contiene la lógica de interacción para la página principal del huésped (home2.php).
 * Incluye las siguientes funcionalidades:
 * - Manejo del menú hamburguesa y navegación móvil.
 * - Resaltado dinámico del menú de navegación al hacer scroll por las secciones (Intersection Observer).
 * - Lógica del modal para "Reservar Habitación" desde la página de inicio, incluyendo:
 *   - Renderizado del calendario interactivo para selección de fechas de estadía.
 *   - Cálculo automático de la cantidad de noches y del costo total.
 *   - Control del contador de huéspedes (respetando la capacidad máxima de la habitación).
 *   - Actualización visual de la opción de método de pago seleccionada.
 *   - Validación estricta del formulario para asegurar que todos los datos estén presentes antes de enviar.
 * - Sistema de notificaciones emergentes (Toast) para mensajes al usuario.
 */

// Menú hamburguesa
const hamburger   = document.getElementById('hamburger');
const mobileMenu  = document.getElementById('mobileMenu');

if(hamburger && mobileMenu) {
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
}

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

// ===================== MODAL RESERVA =====================
let precioActual = 0;
let currentViewDate = new Date();
let fechaInicio = null;
let fechaFin = null;
let maxPersonas = 4; // default

function abrirReserva(btn) {
  document.getElementById('modalReserva').classList.add('open');
  document.body.style.overflow = 'hidden';

  fechaInicio = null;
  fechaFin = null;
  currentViewDate = new Date();

  // Cargar datos de la habitación
  document.getElementById('modal-img').src = btn.getAttribute('data-img');
  document.getElementById('modal-tipo').textContent = btn.getAttribute('data-tipo');
  document.getElementById('modal-title').textContent = btn.getAttribute('data-nombre');
  document.getElementById('modal-precio-noche').textContent = btn.getAttribute('data-precio');
  document.getElementById('input-hab-id').value = btn.getAttribute('data-id');
  document.getElementById('input-precio-hidden').value = btn.getAttribute('data-precio').replace(/[^\d]/g, '');
  document.getElementById('res-nombre').textContent = btn.getAttribute('data-nombre');
  maxPersonas = parseInt(btn.getAttribute('data-max-personas')) || 4;
  // Reset personas y fechas
  document.getElementById('personas-num').textContent = '1';
  document.getElementById('personas-input').value = '1';
  document.getElementById('fecha_inicio').value = '';
  document.getElementById('fecha_fin').value = '';
  document.getElementById('res-noches').textContent = '—';
  document.getElementById('res-total').textContent = '—';
  document.getElementById('fechas-texto').textContent = 'Selecciona tu fecha de entrada en el calendario';
  document.getElementById('personas-hint').textContent = 'Selecciona fechas para continuar';

  precioActual = parseInt(btn.getAttribute('data-precio').replace(/[^\d]/g, ''));

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

  // Formatear nombre del mes y año (Capitalizado)
  const monthName = new Intl.DateTimeFormat('es-ES', { month: 'long', year: 'numeric' }).format(currentViewDate);
  label.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

  const firstDay = new Date(year, month, 1).getDay();
  const totalDays = new Date(year, month + 1, 0).getDate();
  const today = new Date();
  today.setHours(0,0,0,0);

  // Celdas vacías iniciales
  for (let i = 0; i < firstDay; i++) {
    const div = document.createElement('div');
    div.className = 'cal-cell cal-blank';
    grid.appendChild(div);
  }

  // Generar días del mes
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
  cambiarPersonas(0); // Actualizar estado de botones
}

function cerrarReserva(event, forzar) {
  if (forzar || (event && event.target === document.getElementById('modalReserva'))) {
    document.getElementById('modalReserva').classList.remove('open');
    document.body.style.overflow = '';
  }
}

function cambiarPersonas(delta) {
  const input = document.getElementById('personas-input');
  const display = document.getElementById('personas-num');
  const plural = document.getElementById('personas-plural');
  const btnMenos = document.querySelector('.personas-btn:first-of-type');
  const btnMas = document.querySelector('.personas-btn:last-of-type');
  if(!input || !display) return;
  let num = parseInt(input.value) + delta;
  num = Math.max(1, Math.min(maxPersonas, num)); // Min 1, max maxPersonas
  input.value = num;
  display.textContent = num;
  plural.style.display = num > 1 ? 'inline' : 'none';
  const hint = document.getElementById('personas-hint');
  if (num >= maxPersonas) {
    hint.textContent = `Máximo ${maxPersonas} persona${maxPersonas > 1 ? 's' : ''}`;
  } else {
    hint.textContent = fechaInicio && fechaFin ? 'Ajusta el número de personas' : 'Selecciona fechas para continuar';
  }
  // Deshabilitar botones si no hay fechas
  const disabled = !fechaInicio || !fechaFin;
  btnMenos.disabled = disabled;
  btnMas.disabled = disabled;
}

function calcularTotalGeneral() {
  if (fechaInicio && fechaFin && precioActual > 0) {
    const d1 = new Date(fechaInicio);
    const d2 = new Date(fechaFin);
    const noches = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
    document.getElementById('res-noches').textContent = noches > 0 ? noches : '—';
    document.getElementById('res-total').textContent = (noches > 0) ? '$' + (noches * precioActual).toLocaleString() : '—';
  } else {
    document.getElementById('res-noches').textContent = '—';
    document.getElementById('res-total').textContent = '—';
  }
}

// Selección visual de pago
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

// Validación antes de enviar
const formReserva = document.getElementById('formReserva');
if(formReserva) {
  formReserva.addEventListener('submit', function(e) {
    const fechaInicioVal = document.getElementById('fecha_inicio').value;
    const fechaFinVal = document.getElementById('fecha_fin').value;
    const numPersonas = document.getElementById('personas-input').value;
    const pagoSeleccionado = document.querySelector('input[name="id_metodo_pago"]:checked');

    if (!fechaInicioVal || !fechaFinVal || !numPersonas || !pagoSeleccionado) {
      e.preventDefault();
      showToast('⚠️', 'Campos incompletos', 'Por favor llenar todos los campos para poder reservar.');
      return false;
    }
  });
}

function showToast(icon, title, subtitle) {
  const toast = document.getElementById('toast');
  const toastIcon = document.querySelector('.toast-icon');
  const toastTitle = document.querySelector('.toast-title');
  const toastSub = document.querySelector('.toast-sub');
  if(!toast) return;
  toastIcon.textContent = icon;
  toastTitle.textContent = title;
  toastSub.textContent = subtitle;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 4000);
}
