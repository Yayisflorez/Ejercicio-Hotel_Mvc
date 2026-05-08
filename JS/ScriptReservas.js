
// ── Hamburguesa ───────────────────────────────────────────────
const hamburger = document.getElementById("hamburger");
const mobileMenu = document.getElementById("mobileMenu");
hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("open");
  mobileMenu.classList.toggle("open");
});

// ── Modal de Reserva General ──────────────────────────────────
let precioActual = 0;
let currentViewDate = new Date();
let fechaInicio = null;
let fechaFin = null;

function abrirModalGeneral() {
  document.getElementById("modalReserva").classList.add("open");
  document.body.style.overflow = "hidden";
  renderizarCalendario();
}

function cambiarMes(delta) {
  currentViewDate.setMonth(currentViewDate.getMonth() + delta);
  renderizarCalendario();
}

function renderizarCalendario() {
  const grid = document.getElementById("cal-grid");
  const label = document.getElementById("cal-month-label");
  if (!grid || !label) return;

  grid.innerHTML = "";
  const year = currentViewDate.getFullYear();
  const month = currentViewDate.getMonth();

  const monthName = new Intl.DateTimeFormat("es-ES", {
    month: "long",
    year: "numeric",
  }).format(currentViewDate);
  label.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

  const firstDay = new Date(year, month, 1).getDay();
  const totalDays = new Date(year, month + 1, 0).getDate();
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  for (let i = 0; i < firstDay; i++) {
    const div = document.createElement("div");
    div.className = "cal-cell cal-blank";
    grid.appendChild(div);
  }

  for (let d = 1; d <= totalDays; d++) {
    const dateObj = new Date(year, month, d);
    const dateStr = dateObj.toISOString().split("T")[0];
    const div = document.createElement("div");
    div.className = "cal-cell";
    div.textContent = d;

    if (dateObj < today) {
      div.classList.add("cal-past");
    } else {
      div.onclick = () => seleccionarFecha(dateStr);
      if (dateStr === fechaInicio) div.classList.add("cal-start");
      if (dateStr === fechaFin) div.classList.add("cal-end");
      if (
        fechaInicio &&
        fechaFin &&
        dateStr > fechaInicio &&
        dateStr < fechaFin
      ) {
        div.classList.add("cal-range");
      }
    }
    grid.appendChild(div);
  }
}

function seleccionarFecha(fecha) {
  if (!fechaInicio || (fechaInicio && fechaFin)) {
    fechaInicio = fecha;
    fechaFin = null;
    document.getElementById("fechas-texto").innerHTML =
      `<span class="fecha-chip entrada">Entrada: ${fecha}</span> <span class="fecha-chip-hint">Selecciona salida</span>`;
  } else if (fecha > fechaInicio) {
    fechaFin = fecha;
    document.getElementById("fechas-texto").innerHTML =
      `<span class="fecha-chip entrada">Entrada: ${fechaInicio}</span> <span class="fecha-chip salida">Salida: ${fechaFin}</span>`;
  } else {
    fechaInicio = fecha;
    fechaFin = null;
  }
  document.getElementById("fecha_inicio").value = fechaInicio || "";
  document.getElementById("fecha_fin").value = fechaFin || "";
  renderizarCalendario();
  calcularTotalGeneral();
}

function cerrarReserva(event, forzar) {
  if (
    forzar ||
    (event && event.target === document.getElementById("modalReserva"))
  ) {
    document.getElementById("modalReserva").classList.remove("open");
    document.body.style.overflow = "";
  }
}

function calcularTotalGeneral() {
  if (fechaInicio && fechaFin && precioActual > 0) {
    const d1 = new Date(fechaInicio);
    const d2 = new Date(fechaFin);
    const noches = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
    const resNoches = document.getElementById("res-noches");
    const resTotal = document.getElementById("res-total");
    if (resNoches) resNoches.textContent = noches > 0 ? noches : "—";
    if (resTotal)
      resTotal.textContent =
        noches > 0 ? "$" + (noches * precioActual).toLocaleString() : "—";
  }
}

// ── Personas ──────────────────────────────────────────────────
function cambiarPersonas(delta) {
  const input = document.getElementById("personas-input");
  const display = document.getElementById("personas-num");
  const plural = document.getElementById("personas-plural");
  let num = parseInt(input.value) + delta;
  num = Math.max(1, Math.min(10, num)); // Min 1, max 10
  input.value = num;
  display.textContent = num;
  plural.style.display = num > 1 ? "inline" : "none";
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(icon, title, sub, duration = 4000) {
  document.getElementById("toast-icon").textContent = icon;
  document.getElementById("toast-title").textContent = title;
  document.getElementById("toast-sub").textContent = sub;
  const t = document.getElementById("toast");
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), duration);
}

// ── Editar con calendario + validaciones ──────────────────────
let editViewDate = new Date();
let editFechaInicio = null;
let editFechaFin = null;
let editMaxPersonas = 10;
let editPrecioActual = 0;

// Estado original para validar cambios
let editOriginalState = {};

function editarReserva(id) {
  const fila = document.querySelector(`.res-row[data-id='${id}']`);
  if (!fila) {
    showToast("⚠️", "Error", "Reserva no encontrada");
    return;
  }

  const celdas = fila.querySelectorAll("td");

  // Imagen y título
  document.getElementById("edit-img").src = celdas[0].querySelector("img").src;
  document.getElementById("edit-tipo").textContent =
    celdas[0].querySelector(".hab-tipo").textContent;
  document.getElementById("edit-title").textContent =
    celdas[0].querySelector(".hab-nombre").textContent;

  // Fechas ISO desde data-iso
  const fechaVals = celdas[1].querySelectorAll(".fecha-val");
  const entradaISO = fechaVals[0]?.getAttribute("data-iso") || "";
  const salidaISO = fechaVals[1]?.getAttribute("data-iso") || "";

  // Personas actuales y máximo
  const personasActuales =
    parseInt(celdas[2].querySelector(".personas-num").textContent) || 1;
  editMaxPersonas = parseInt(fila.getAttribute("data-max-personas")) || 10;
  const idHabitacionOriginal = fila.getAttribute("data-id-habitacion") || "";
  const categoriaOriginal = fila.getAttribute("data-categoria") || "";
  editPrecioActual = parseFloat(fila.getAttribute("data-precio-noche")) || 0;

  // Pago
  const pagoTexto = celdas[3].textContent.trim().toLowerCase();
  let pagoValue = "bancolombia";
  if (pagoTexto.includes("nequi")) {
    document.getElementById("edit-pago-nequi").checked = true;
    pagoValue = "nequi";
  } else if (pagoTexto.includes("daviplata")) {
    document.getElementById("edit-pago-daviplata").checked = true;
    pagoValue = "daviplata";
  } else {
    document.getElementById("edit-pago-bancolombia").checked = true;
    pagoValue = "bancolombia";
  }

  // Inicializar personas
  const pNum = Math.min(personasActuales, editMaxPersonas);
  document.getElementById("edit-personas-num").textContent = pNum;
  document.getElementById("edit-personas-input").value = pNum;
  document.getElementById("edit-personas-plural").style.display =
    pNum > 1 ? "inline" : "none";

  // Categoría y Habitación
  document.getElementById("edit-select-categoria").value = categoriaOriginal;
  // Cargar habitaciones de esa categoría y seleccionar la correcta
  cambiarCategoriaEdit(idHabitacionOriginal);

  // Inicializar calendario
  document.getElementById("edit-reserva-id").value = id;
  document.getElementById("edit-hab-id").value = idHabitacionOriginal;
  document.getElementById("edit-precio-hidden").value = editPrecioActual;
  editFechaInicio = entradaISO || null;
  editFechaFin = salidaISO || null;
  editViewDate = editFechaInicio
    ? new Date(editFechaInicio + "T12:00:00")
    : new Date();

  renderizarCalendarioEdit();
  actualizarTextoFechasEdit();
  calcularTotalEdit();

  // Guardar estado original
  editOriginalState = {
    categoria: categoriaOriginal,
    idHabitacion: idHabitacionOriginal,
    fechaInicio: editFechaInicio,
    fechaFin: editFechaFin,
    personas: pNum,
    pago: pagoValue,
  };

  validarCambiosEdit();

  document.getElementById("modalEditar").classList.add("open");
  document.body.style.overflow = "hidden";
}

async function cambiarCategoriaEdit(preselectId = null) {
  const categoria = document.getElementById("edit-select-categoria").value;
  const selectHab = document.getElementById("edit-select-habitacion");

  selectHab.innerHTML = '<option value="">Selecciona una habitación</option>';
  selectHab.disabled = true;

  if (!categoria) {
    validarCambiosEdit();
    return;
  }

  try {
    const response = await fetch(
      `model/Habitacion.php?action=getHabitacionesByCategoria&categoria=${encodeURIComponent(categoria)}`,
    );
    const result = await response.json();
    const habitacionesData = result.data;

    habitacionesData.forEach((hab) => {
      const option = document.createElement("option");
      option.value = hab.id;
      option.textContent = `${hab.nombre} - ${hab.descripcion}`;
      option.dataset.precio = hab.precio;
      option.dataset.img = hab.img
        ? hab.img
        : "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
      option.dataset.maxpersonas = hab.max_personas;
      if (preselectId && hab.id == preselectId) {
        option.selected = true;
      }
      selectHab.appendChild(option);
    });

    selectHab.disabled = false;
  } catch (error) {
    console.error("Error AJAX habitaciones", error);
  }

  cambiarHabitacionEdit();
}

function cambiarHabitacionEdit() {
  const selectHab = document.getElementById("edit-select-habitacion");
  const selectedOption = selectHab.options[selectHab.selectedIndex];
  const habId = selectHab.value;

  const noHayHabitacion = habId === "" || selectedOption === undefined;

  if (noHayHabitacion) {
    document.getElementById("edit-img").src =
      "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
    document.getElementById("edit-title").textContent =
      "Selecciona tu habitación";
    document.getElementById("res-edit-nombre").textContent = "—";
    document.getElementById("edit-hab-id").value = "";
    document.getElementById("edit-precio-hidden").value = "";
    editPrecioActual = 0;
    calcularTotalEdit();
    validarCambiosEdit();
    return;
  }

  const precio = parseFloat(selectedOption.dataset.precio) || 0;
  const img = selectedOption.dataset.img || "";
  const maxPersonas = parseInt(selectedOption.dataset.maxpersonas) || 1;
  const nombre = selectedOption.textContent.split(" - ")[0];

  document.getElementById("edit-img").src =
    img ||
    "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
  document.getElementById("edit-title").textContent = nombre;
  document.getElementById("res-edit-nombre").textContent = nombre;
  document.getElementById("edit-hab-id").value = habId;
  document.getElementById("edit-precio-hidden").value = precio;

  editPrecioActual = precio;
  editMaxPersonas = maxPersonas;

  const currentPersonas = parseInt(
    document.getElementById("edit-personas-input").value,
  );
  if (currentPersonas > maxPersonas) {
    document.getElementById("edit-personas-num").textContent = maxPersonas;
    document.getElementById("edit-personas-input").value = maxPersonas;
    document.getElementById("edit-personas-plural").style.display =
      maxPersonas > 1 ? "inline" : "none";
    showToast(
      "⚠️",
      "Máximo ajustado",
      `La habitación seleccionada permite máximo ${maxPersonas} personas.`,
    );
  }

  const hint = document.getElementById("edit-personas-hint");
  if (hint) {
    hint.textContent = `Máximo permitido: ${maxPersonas} persona${maxPersonas > 1 ? "s" : ""}`;
  }

  calcularTotalEdit();
  validarCambiosEdit();
}

function calcularTotalEdit() {
  if (editFechaInicio && editFechaFin && editPrecioActual > 0) {
    const d1 = new Date(editFechaInicio);
    const d2 = new Date(editFechaFin);
    const noches = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
    const resNoches = document.getElementById("res-edit-noches");
    const resTotal = document.getElementById("res-edit-total");
    if (resNoches) resNoches.textContent = noches > 0 ? noches : "—";
    if (resTotal)
      resTotal.textContent =
        noches > 0 ? "$" + (noches * editPrecioActual).toLocaleString() : "—";
  } else {
    const resNoches = document.getElementById("res-edit-noches");
    const resTotal = document.getElementById("res-edit-total");
    if (resNoches) resNoches.textContent = "—";
    if (resTotal) resTotal.textContent = "—";
  }
}

function validarCambiosEdit() {
  const currentState = {
    categoria: document.getElementById("edit-select-categoria").value,
    idHabitacion: document.getElementById("edit-select-habitacion").value,
    fechaInicio: editFechaInicio,
    fechaFin: editFechaFin,
    personas: parseInt(document.getElementById("edit-personas-input").value),
    pago:
      document.querySelector('input[name="pago_edit"]:checked')?.value || "",
  };

  const btn = document.getElementById("btn-guardar-edit");
  if (!btn) return;

  if (
    !currentState.categoria ||
    !currentState.idHabitacion ||
    !currentState.fechaInicio ||
    !currentState.fechaFin
  ) {
    btn.disabled = true;
    return;
  }

  const hasChanges =
    currentState.categoria !== editOriginalState.categoria ||
    currentState.idHabitacion !== editOriginalState.idHabitacion ||
    currentState.fechaInicio !== editOriginalState.fechaInicio ||
    currentState.fechaFin !== editOriginalState.fechaFin ||
    currentState.personas !== editOriginalState.personas ||
    currentState.pago !== editOriginalState.pago;

  btn.disabled = !hasChanges;
}

document.querySelectorAll('input[name="pago_edit"]').forEach((radio) => {
  radio.addEventListener("change", validarCambiosEdit);
});

function cambiarMesEdit(delta) {
  editViewDate.setMonth(editViewDate.getMonth() + delta);
  renderizarCalendarioEdit();
}

function renderizarCalendarioEdit() {
  const grid = document.getElementById("edit-cal-grid");
  const label = document.getElementById("edit-cal-month-label");
  if (!grid || !label) return;

  grid.innerHTML = "";
  const year = editViewDate.getFullYear();
  const month = editViewDate.getMonth();
  const monthName = new Intl.DateTimeFormat("es-ES", {
    month: "long",
    year: "numeric",
  }).format(editViewDate);
  label.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

  const firstDay = new Date(year, month, 1).getDay();
  const totalDays = new Date(year, month + 1, 0).getDate();

  // Hoy a medianoche para comparar
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  for (let i = 0; i < firstDay; i++) {
    const blank = document.createElement("div");
    blank.className = "cal-cell cal-blank";
    grid.appendChild(blank);
  }

  for (let d = 1; d <= totalDays; d++) {
    const dateObj = new Date(year, month, d);
    const dateStr = dateObj.toISOString().split("T")[0];
    const div = document.createElement("div");
    div.className = "cal-cell";
    div.textContent = d;

    // ✅ Validación 1: bloquear fechas pasadas
    if (dateObj < today) {
      div.classList.add("cal-past");
      div.title = "Fecha no disponible";
    } else {
      div.onclick = () => seleccionarFechaEdit(dateStr);

      if (dateStr === editFechaInicio) div.classList.add("cal-start");
      if (dateStr === editFechaFin) div.classList.add("cal-end");
      if (
        editFechaInicio &&
        editFechaFin &&
        dateStr > editFechaInicio &&
        dateStr < editFechaFin
      )
        div.classList.add("cal-range");
    }
    grid.appendChild(div);
  }
}

function seleccionarFechaEdit(fecha) {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const seleccionada = new Date(fecha + "T12:00:00");

  // Doble check: no permitir pasadas
  if (seleccionada < today) {
    showToast("⛔", "Fecha no válida", "No puedes reservar fechas pasadas.");
    return;
  }

  if (!editFechaInicio || (editFechaInicio && editFechaFin)) {
    editFechaInicio = fecha;
    editFechaFin = null;
  } else if (fecha > editFechaInicio) {
    editFechaFin = fecha;
  } else {
    // Si selecciona antes del inicio, reinicia
    editFechaInicio = fecha;
    editFechaFin = null;
  }

  document.getElementById("edit-fecha-inicio").value = editFechaInicio || "";
  document.getElementById("edit-fecha-fin").value = editFechaFin || "";
  renderizarCalendarioEdit();
  actualizarTextoFechasEdit();
  calcularTotalEdit();
  validarCambiosEdit();
}

function actualizarTextoFechasEdit() {
  const cont = document.getElementById("edit-fechas-texto");
  if (!cont) return;
  if (editFechaInicio && editFechaFin) {
    const noches = Math.round(
      (new Date(editFechaFin) - new Date(editFechaInicio)) / 86400000,
    );
    cont.innerHTML = `
      <span class="fecha-chip entrada">✈️ ${editFechaInicio}</span>
      <span class="fecha-chip salida">🏁 ${editFechaFin}</span>
      <span class="noches-badge">${noches} noche${noches !== 1 ? "s" : ""}</span>`;
  } else if (editFechaInicio) {
    cont.innerHTML = `<span class="fecha-chip entrada">✈️ ${editFechaInicio}</span>
      <span class="fecha-chip-hint">Ahora selecciona la salida</span>`;
  } else {
    cont.innerHTML = `<span class="fecha-chip-hint">Selecciona entrada y salida</span>`;
  }
}

// ✅ Validación 2: máximo de personas por habitación
function cambiarPersonasEdit(delta) {
  const input = document.getElementById("edit-personas-input");
  const display = document.getElementById("edit-personas-num");
  const plural = document.getElementById("edit-personas-plural");
  const hint = document.getElementById("edit-personas-hint");

  let num = parseInt(input.value) + delta;
  num = Math.max(1, Math.min(editMaxPersonas, num));

  input.value = num;
  display.textContent = num;
  plural.style.display = num > 1 ? "inline" : "none";

  if (hint) {
    if (num >= editMaxPersonas) {
      hint.textContent = `Máximo ${editMaxPersonas} persona${editMaxPersonas > 1 ? "s" : ""}`;
      hint.classList.add("personas-hint--max");
    } else {
      hint.textContent = `Máximo permitido: ${editMaxPersonas} personas`;
      hint.classList.remove("personas-hint--max");
    }
  }
  validarCambiosEdit();
}

function cerrarEditar(event, forzar) {
  if (
    forzar ||
    (event && event.target === document.getElementById("modalEditar"))
  ) {
    document.getElementById("modalEditar").classList.remove("open");
    document.body.style.overflow = "";
  }
}

// Submit con validaciones finales antes de enviar
document.getElementById("formEditar").addEventListener("submit", function (e) {
  e.preventDefault();

  const id = document.getElementById("edit-reserva-id").value;
  const idHab = document.getElementById("edit-hab-id").value;
  const precio = document.getElementById("edit-precio-hidden").value;
  const fechaInicio = document.getElementById("edit-fecha-inicio").value;
  const fechaFin = document.getElementById("edit-fecha-fin").value;
  const personas = parseInt(
    document.getElementById("edit-personas-input").value,
  );
  const pago = document.querySelector('input[name="pago_edit"]:checked').value;

  // Validar fechas seleccionadas
  if (!fechaInicio || !fechaFin) {
    showToast(
      "⚠️",
      "Fechas incompletas",
      "Selecciona fecha de entrada y salida.",
    );
    return;
  }

  // Validar habitación
  if (!idHab || !precio) {
    showToast(
      "⚠️",
      "Habitación incompleta",
      "Selecciona una habitación válida.",
    );
    return;
  }

  // Validar que no sean pasadas
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  if (new Date(fechaInicio + "T12:00:00") < hoy) {
    showToast(
      "⛔",
      "Fecha inválida",
      "La fecha de entrada no puede ser en el pasado.",
    );
    return;
  }

  // Validar personas
  if (personas < 1 || personas > editMaxPersonas) {
    showToast(
      "⚠️",
      "Personas inválidas",
      `Debe ser entre 1 y ${editMaxPersonas} persona${editMaxPersonas > 1 ? "s" : ""}.`,
    );
    return;
  }

  fetch("index.php?action=actualizarReserva", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `reserva_id=${id}&id_habitacion=${idHab}&precio=${precio}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&personas=${personas}&pago_edit=${pago}`,
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.status === "success") {
        cerrarEditar(null, true);
        showToast(
          "✅",
          "Reserva actualizada",
          `Reserva #${id} guardada correctamente.`,
        );
        setTimeout(() => location.reload(), 1500);
      } else {
        showToast("❌", "Error", "No se pudo actualizar la reserva.");
      }
    })
    .catch(() => showToast("❌", "Error", "Error de conexión."));
});

// ── Descargar ─────────────────────────────────────────────────
function descargarReserva(id) {
  window.open(`index.php?action=descargarPdfReserva&id=${id}`, "_blank");
  showToast("📄", "Generando PDF", `Abriendo comprobante de reserva ${id}...`);
}

// ── Borrar ────────────────────────────────────────────────────
let reservaABorrar = null;

function confirmarBorrar(id) {
  reservaABorrar = id;
  document.getElementById("del-id").textContent = id;
  document.getElementById("modalDel").classList.add("open");
  document.body.style.overflow = "hidden";
}

function cerrarModalDel(event) {
  // Solo cerrar si se hace click en el backdrop o si no hay evento (llamada directa)
  if (!event || event.target === document.getElementById("modalDel")) {
    document.getElementById("modalDel").classList.remove("open");
    document.body.style.overflow = "";
    reservaABorrar = null;
  }
}

function ejecutarBorrar() {
  const id = reservaABorrar;
  cerrarModalDel();

  // Enviar petición AJAX para eliminar de la base de datos
  fetch("index.php?action=eliminarReserva", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id_reserva=${id}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Animación de salida de la fila/card
        document.querySelectorAll(`[data-id="${id}"]`).forEach((el) => {
          el.classList.add("row-removing");
          setTimeout(() => el.remove(), 400);
        });
        showToast("🗑️", "Reserva cancelada", `La reserva ${id} fue cancelada.`);
        // Recargar para actualizar estadísticas
        setTimeout(() => location.reload(), 1500);
      } else {
        showToast(
          "❌",
          "Error",
          data.message || "No se pudo cancelar la reserva.",
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast("❌", "Error", "Error de conexión.");
    });
}

// ── Reporte General ───────────────────────────────────────────
function generarReporte() {
  const filas = document.querySelectorAll(".res-row");
  if (filas.length === 0) {
    showToast("⚠️", "Sin datos", "No hay reservas para generar un reporte.");
    return;
  }

  showToast("📊", "Generando Excel", "Preparando reporte general de reservas...");
  
  // Redirigir al generador de Excel
  setTimeout(() => {
    window.location.href = "index.php?action=descargarExcelReservas";
  }, 1000);
}

// Cerrar modal borrar al click fuera
document.getElementById("modalDel").addEventListener("click", (e) => {
  if (e.target === document.getElementById("modalDel")) cerrarModalDel();
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") cerrarModalDel();
});

// Cerrar modal nueva reserva al click fuera
document.getElementById("modalNuevaReserva").addEventListener("click", (e) => {
  if (e.target === document.getElementById("modalNuevaReserva"))
    cerrarModalNuevaReserva();
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") cerrarModalNuevaReserva();
});
// ── Modal Nueva Reserva ──────────────────────────────────────
let precioNuevaActual = 0;
let currentNuevaViewDate = new Date();
let fechaNuevaInicio = null;
let fechaNuevaFin = null;

function abrirModalNuevaReserva() {
  document.getElementById("modalNuevaReserva").classList.add("open");
  document.body.style.overflow = "hidden";
  renderizarCalendarioNueva();
}

function cerrarModalNuevaReserva(event, forzar) {
  if (
    forzar ||
    (event && event.target === document.getElementById("modalNuevaReserva"))
  ) {
    document.getElementById("modalNuevaReserva").classList.remove("open");
    document.body.style.overflow = "";
    // Resetear
    document.getElementById("select-categoria").value = "";
    document.getElementById("select-habitacion").innerHTML =
      '<option value="">Primero selecciona una categoría</option>';
    document.getElementById("select-habitacion").disabled = true;
    document.getElementById("modal-nueva-img").src =
      "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
    document.getElementById("modal-nueva-title").textContent =
      "Selecciona tu habitación";
    document.getElementById("modal-nueva-precio-noche").textContent = "—";
    document.getElementById("res-nueva-nombre").textContent = "—";
    document.getElementById("res-nueva-noches").textContent = "—";
    document.getElementById("res-nueva-total").textContent = "—";
    fechaNuevaInicio = null;
    fechaNuevaFin = null;
    document.getElementById("fechas-nueva-texto").innerHTML =
      '<span class="fecha-chip-hint">Selecciona entrada y salida</span>';
    document.getElementById("personas-nueva-num").textContent = "1";
    document.getElementById("personas-nueva-input").value = "1";
    document.getElementById("personas-nueva-plural").style.display = "none";
    document.getElementById("personas-nueva-decr").disabled = true;
    document.getElementById("personas-nueva-incr").disabled = true;
    document.getElementById("personas-nueva-hint").textContent =
      "Selecciona categoría y habitación para activar";
  }
}

async function cambiarCategoria() {
  const categoria = document.getElementById("select-categoria").value;
  const selectHab = document.getElementById("select-habitacion");

  selectHab.innerHTML = '<option value="">Selecciona una habitación</option>';
  selectHab.disabled = true;

  if (!categoria) return;

  try {
    const response = await fetch(
      `model/Habitacion.php?action=getHabitacionesByCategoria&categoria=${encodeURIComponent(categoria)}`,
    );

    const result = await response.json();

    const habitaciones = result.data;

    habitaciones.forEach((hab) => {
      const option = document.createElement("option");

      option.value = hab.id;
      option.textContent = `${hab.nombre} - ${hab.descripcion}`;

      option.dataset.precio = hab.precio;
      option.dataset.img = hab.img
        ? hab.img
        : "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
      option.dataset.maxpersonas = hab.max_personas;

      selectHab.appendChild(option);
    });

    selectHab.disabled = false;
  } catch (error) {
    console.log("Error AJAX habitaciones");
    console.error(error);
  }

  cambiarHabitacion();
}

function cambiarHabitacion() {
  const selectHab = document.getElementById("select-habitacion");
  const selectedOption = selectHab.options[selectHab.selectedIndex];
  const habId = selectHab.value;

  if (!habId || !selectedOption) {
    // Sin selección: resetear UI
    document.getElementById("modal-nueva-img").src =
      "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
    document.getElementById("modal-nueva-title").textContent =
      "Selecciona tu habitación";
    document.getElementById("modal-nueva-precio-noche").textContent = "—";
    document.getElementById("res-nueva-nombre").textContent = "—";
    document.getElementById("input-nueva-hab-id").value = "";
    document.getElementById("input-nueva-precio-hidden").value = "";
    precioNuevaActual = 0;
    actualizarPersonasNuevaControls();
    calcularTotalNueva();
    return;
  }

  // Leer datos desde el dataset del <option> (guardados por el AJAX)
  const precio = parseFloat(selectedOption.dataset.precio) || 0;
  const img = selectedOption.dataset.img || "";
  const maxPersonas = parseInt(selectedOption.dataset.maxpersonas) || 1;
  const nombre = selectedOption.textContent.split(" - ")[0]; // extrae solo el nombre

  document.getElementById("modal-nueva-img").src =
    img ||
    "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80";
  document.getElementById("modal-nueva-title").textContent = nombre;
  document.getElementById("modal-nueva-precio-noche").textContent =
    "$" + precio.toLocaleString();
  document.getElementById("res-nueva-nombre").textContent = nombre;
  document.getElementById("input-nueva-hab-id").value = habId;
  document.getElementById("input-nueva-precio-hidden").value = precio;
  precioNuevaActual = precio;

  // Validar personas
  const currentPersonas = parseInt(
    document.getElementById("personas-nueva-input").value,
  );
  if (currentPersonas > maxPersonas) {
    document.getElementById("personas-nueva-num").textContent = maxPersonas;
    document.getElementById("personas-nueva-input").value = maxPersonas;
    document.getElementById("personas-nueva-plural").style.display =
      maxPersonas > 1 ? "inline" : "none";
    document.getElementById("personas-nueva-hint").textContent =
      `Máximo ${maxPersonas} persona${maxPersonas > 1 ? "s" : ""}`;
    showToast(
      "⚠️",
      "Máximo de personas",
      `Esta habitación permite máximo ${maxPersonas} persona${maxPersonas > 1 ? "s" : ""}.`,
    );
  } else {
    document.getElementById("personas-nueva-hint").textContent =
      fechaNuevaInicio && fechaNuevaFin
        ? "Ajusta el número de personas"
        : "Selecciona fechas para continuar";
  }

  actualizarPersonasNuevaControls();
  calcularTotalNueva();
}

function cambiarMesNueva(delta) {
  currentNuevaViewDate.setMonth(currentNuevaViewDate.getMonth() + delta);
  renderizarCalendarioNueva();
}

function renderizarCalendarioNueva() {
  const grid = document.getElementById("cal-nueva-grid");
  const label = document.getElementById("cal-nueva-month-label");
  if (!grid || !label) return;

  grid.innerHTML = "";
  const year = currentNuevaViewDate.getFullYear();
  const month = currentNuevaViewDate.getMonth();

  const monthName = new Intl.DateTimeFormat("es-ES", {
    month: "long",
    year: "numeric",
  }).format(currentNuevaViewDate);
  label.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

  const firstDay = new Date(year, month, 1).getDay();
  const totalDays = new Date(year, month + 1, 0).getDate();
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  for (let i = 0; i < firstDay; i++) {
    const div = document.createElement("div");
    div.className = "cal-cell cal-blank";
    grid.appendChild(div);
  }

  for (let d = 1; d <= totalDays; d++) {
    const dateObj = new Date(year, month, d);
    const dateStr = dateObj.toISOString().split("T")[0];
    const div = document.createElement("div");
    div.className = "cal-cell";
    div.textContent = d;

    if (dateObj < today) {
      div.classList.add("cal-past");
    } else {
      div.onclick = () => seleccionarFechaNueva(dateStr);
      if (dateStr === fechaNuevaInicio) div.classList.add("cal-start");
      if (dateStr === fechaNuevaFin) div.classList.add("cal-end");
      if (
        fechaNuevaInicio &&
        fechaNuevaFin &&
        dateStr > fechaNuevaInicio &&
        dateStr < fechaNuevaFin
      ) {
        div.classList.add("cal-range");
      }
    }
    grid.appendChild(div);
  }
}

function seleccionarFechaNueva(fecha) {
  if (!fechaNuevaInicio || (fechaNuevaInicio && fechaNuevaFin)) {
    fechaNuevaInicio = fecha;
    fechaNuevaFin = null;
    document.getElementById("fechas-nueva-texto").innerHTML =
      `<span class="fecha-chip entrada">Entrada: ${fecha}</span> <span class="fecha-chip-hint">Selecciona salida</span>`;
  } else if (fecha > fechaNuevaInicio) {
    fechaNuevaFin = fecha;
    document.getElementById("fechas-nueva-texto").innerHTML =
      `<span class="fecha-chip entrada">Entrada: ${fechaNuevaInicio}</span> <span class="fecha-chip salida">Salida: ${fechaNuevaFin}</span>`;
  } else {
    fechaNuevaInicio = fecha;
    fechaNuevaFin = null;
  }
  document.getElementById("fecha-nueva-inicio").value = fechaNuevaInicio || "";
  document.getElementById("fecha-nueva-fin").value = fechaNuevaFin || "";
  renderizarCalendarioNueva();
  calcularTotalNueva();
}

function cambiarPersonasNueva(delta) {
  const habId = document.getElementById("select-habitacion").value;
  const hab = habitaciones.find((h) => h.id == habId);
  if (!hab) return;

  const input = document.getElementById("personas-nueva-input");
  const display = document.getElementById("personas-nueva-num");
  const plural = document.getElementById("personas-nueva-plural");
  let num = parseInt(input.value) + delta;
  const max = hab.max_personas;
  num = Math.max(1, Math.min(max, num));
  input.value = num;
  display.textContent = num;
  plural.style.display = num > 1 ? "inline" : "none";

  const hint = document.getElementById("personas-nueva-hint");
  if (num >= max) {
    hint.textContent = `Máximo ${max} persona${max > 1 ? "s" : ""}`;
  } else {
    hint.textContent =
      fechaNuevaInicio && fechaNuevaFin
        ? "Ajusta el número de personas"
        : "Selecciona fechas para continuar";
  }
}

function actualizarPersonasNuevaControls() {
  const habId = document.getElementById("select-habitacion").value;
  const hab = habitaciones.find((h) => h.id == habId);
  const btnMinus = document.getElementById("personas-nueva-decr");
  const btnPlus = document.getElementById("personas-nueva-incr");
  const hint = document.getElementById("personas-nueva-hint");

  if (!hab) {
    btnMinus.disabled = true;
    btnPlus.disabled = true;
    hint.textContent = "Selecciona categoría y habitación para activar";
    return;
  }

  btnMinus.disabled = false;
  btnPlus.disabled = false;
  const currentNum = parseInt(
    document.getElementById("personas-nueva-input").value,
  );
  if (currentNum >= hab.max_personas) {
    hint.textContent = `Máximo ${hab.max_personas} persona${hab.max_personas > 1 ? "s" : ""}`;
  } else {
    hint.textContent =
      fechaNuevaInicio && fechaNuevaFin
        ? "Ajusta el número de personas"
        : "Selecciona fechas para continuar";
  }
}

function calcularTotalNueva() {
  if (fechaNuevaInicio && fechaNuevaFin && precioNuevaActual > 0) {
    const d1 = new Date(fechaNuevaInicio);
    const d2 = new Date(fechaNuevaFin);
    const noches = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
    const resNoches = document.getElementById("res-nueva-noches");
    const resTotal = document.getElementById("res-nueva-total");
    if (resNoches) resNoches.textContent = noches > 0 ? noches : "—";
    if (resTotal)
      resTotal.textContent =
        noches > 0 ? "$" + (noches * precioNuevaActual).toLocaleString() : "—";
  }
}

function actualizarPagoSeleccionado() {
  document.querySelectorAll(".pago-option").forEach((label) => {
    const input = label.querySelector('input[type="radio"]');
    label.classList.toggle("selected", input && input.checked);
  });
}

document
  .querySelectorAll('.pago-option input[type="radio"]')
  .forEach((input) => {
    input.addEventListener("change", actualizarPagoSeleccionado);
  });

actualizarPagoSeleccionado();
