/* ──────────────────────────────────────────────────────────────
   SWEETALERT2 — Mensajes de bienvenida / cierre
─────────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'ok') {
        Swal.fire({
            title: '¡Bienvenido a MTB nogales!',
            text: 'se ha iniciado sesion correctamente.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'ok2'){
        Sawl.fire({
            title: '¡Cuenta creada exitosamente!',
            text: 'Bienvenido a MTB nogales, ahora puedes iniciar sesión con tus credenciales.',
            icon: 'success',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }
    if (msg === 'ok3'){
        
        Swal.fire({
       
            title: '¡Sesión cerrada!',
            text: 'Has salido de tu cuenta de forma segura. ¡Vuelve pronto!',
            icon: 'info',
            confirmButtonColor: '#E8630A'
        }).then(() => {
            limpiarURL();
        });
    }

 function limpiarURL() {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});

function confirmarCierreSesion(event) {
    event.preventDefault(); 
    Swal.fire({
        title: '¿Seguro que quieres cerrar sesión?',
        text: "Tendrás que volver a ingresar tus credenciales.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E8630A',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/UTNogales/actions/cerrarSesion.php'; 
        }
    });
}
////cuenta usuarios
 const toggleBtn = document.getElementById('toggleSidebar');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            document.getElementById('mtbSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        });
    }
function confirmarEliminar(id, rol) {
    console.log("ID recibido:", id, "Rol recibido:", rol);

    if (rol === 'ADMIN') {
        Swal.fire({
            title: 'No se puede realizar ese movimiento',
            text: 'Como Administrador, no puedes eliminar tu propia cuenta por seguridad.',
            icon: 'error',
            confirmButtonColor: '#ff6b00'
        });
        return; 
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "Tu cuenta será Desactivada. Podras reactivarla en un lapzo de 30 dias.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff6b00',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/UTNogales/actions/dar_de_baja_cuenta.php?numeroUser=${id}`;
        }
    });
}

function abrirModal(id) {
    const overlay = document.getElementById(id);
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(id) {
    const overlay = document.getElementById(id);
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function abrirModalEditar() { abrirModal('modalEditarUsuarios'); }

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msj = urlParams.get('msj');
    const errorText = urlParams.get('error_text');

    // 1. Mensaje de Éxito
    if (msj === 'edit_ok') {
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: 'Tus datos se guardaron correctamente.',
            confirmButtonColor: '#ff6b00'
        });
    }

    // 2. Mensaje de Error (Viene del Trigger/SP)
    if (msj === 'edit_error') {
        Swal.fire({
            icon: 'error',
            title: 'No se pudo actualizar',
            text: errorText ? decodeURIComponent(errorText) : 'Ocurrió un error inesperado.',
            confirmButtonColor: '#ff6b00'
        });
    }

    // Limpiar la URL para que no repita el mensaje al recargar
    if (msj) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

/* ──────────────────────────────────────────────────────────────
   DATOS MOCK — reemplazar con llamadas AJAX al backend en producción
─────────────────────────────────────────────────────────────── */
const DATA_EVENTOS = [
    { nombre:'Enduro Nogales',     pista:'La Rumorosa',       fecha:'2026-04-05', inscritos:78,  estatus:'Abierto' },
    { nombre:'XCO Hermosillo',     pista:'Cerro de la Silla', fecha:'2026-04-19', inscritos:112, estatus:'Abierto' },
    { nombre:'DH Sierra Madre',    pista:'Sierra Fría',       fecha:'2026-05-10', inscritos:55,  estatus:'Próximo' },
    { nombre:'Cross Country Ures', pista:'Monte Albán',       fecha:'2026-05-24', inscritos:30,  estatus:'Próximo' },
    { nombre:'Marathon Alamos',    pista:'Alamos Trail',      fecha:'2026-06-07', inscritos:14,  estatus:'Convocatoria' },
];

const DATA_RANKING = [
    { pos:1, nombre:'Carlos Mendoza',  puntos:480, equipo:'Trek Racing MX' },
    { pos:2, nombre:'Sofía Gutiérrez', puntos:440, equipo:'Canyon Women' },
    { pos:3, nombre:'Roberto Vega',    puntos:395, equipo:'Specialized BC' },
    { pos:4, nombre:'Ana Torres',      puntos:360, equipo:'Giant Team NL' },
    { pos:5, nombre:'Luis Herrera',    puntos:310, equipo:'Trek Racing MX' },
];

/* NOTA: DATA_INSCRIPCIONES debe definirse aquí o cargarse vía AJAX.
   Ejemplo mínimo para que el código funcione en desarrollo: */
const DATA_INSCRIPCIONES = [
    { dorsal:1,  deportista:'Carlos Mendoza',  evento:'Enduro Nogales', categoria:'Elite',  pista:'La Rumorosa', fecha:'2026-03-10', estatus:'Confirmado' },
    { dorsal:2,  deportista:'Sofía Gutiérrez', evento:'XCO Hermosillo', categoria:'Sub-23', pista:'Cerro de la Silla', fecha:'2026-03-12', estatus:'Pendiente' },
    { dorsal:3,  deportista:'Roberto Vega',    evento:'DH Sierra Madre',categoria:'Master', pista:'Sierra Fría',  fecha:'2026-03-15', estatus:'Confirmado' },
];

/* ──────────────────────────────────────────────────────────────
   ESTADO DE PAGINACIÓN
─────────────────────────────────────────────────────────────── */
let paginaActual   = 1;
let tamPagina      = 10;
let datosFiltrados = [...DATA_INSCRIPCIONES];

/* ──────────────────────────────────────────────────────────────
   RENDER: TABLA DE EVENTOS
─────────────────────────────────────────────────────────────── */
function renderEventos() {
    const clase = { 'Abierto':'badge-success', 'Próximo':'badge-info', 'Convocatoria':'badge-warning' };
    document.getElementById('tbodyEventos').innerHTML = DATA_EVENTOS.map(e => `
        <tr>
            <td><strong>${e.nombre}</strong></td>
            <td><i class="fas fa-map-pin" style="color:var(--mtb-primary);margin-right:4px;"></i>${e.pista}</td>
            <td>${formatFecha(e.fecha)}</td>
            <td><span style="font-weight:700;">${e.inscritos}</span> <span style="color:var(--mtb-gray-500);font-size:.8rem;">/ cupo</span></td>
            <td><span class="badge ${clase[e.estatus]||'badge-dark'}"><span class="dot"></span>${e.estatus}</span></td>
        </tr>
    `).join('');
}

/* ──────────────────────────────────────────────────────────────
   RENDER: RANKING
─────────────────────────────────────────────────────────────── */
function renderRanking() {
    const medallas = ['🥇','🥈','🥉'];
    document.getElementById('rankingList').innerHTML = DATA_RANKING.map(r => `
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:1.3rem;width:28px;text-align:center;">${medallas[r.pos-1]||r.pos}</span>
            <div class="avatar avatar-sm">${r.nombre.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
            <div style="flex:1;">
                <div style="font-weight:700;font-size:.875rem;color:var(--mtb-dark);">${r.nombre}</div>
                <div style="font-size:.75rem;color:var(--mtb-gray-600);">${r.equipo}</div>
            </div>
            <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--mtb-primary);">
                ${r.puntos} <span style="font-size:.65rem;font-weight:400;color:var(--mtb-gray-500);">pts</span>
            </div>
        </div>
    `).join('');
}

/* ──────────────────────────────────────────────────────────────
   RENDER: TABLA DE INSCRIPCIONES
─────────────────────────────────────────────────────────────── */
function renderInscripciones() {
    const inicio = (paginaActual - 1) * tamPagina;
    const pagina = datosFiltrados.slice(inicio, inicio + tamPagina);
    const badge  = { 'Confirmado':'badge-success', 'Pendiente':'badge-warning', 'Cancelado':'badge-danger' };
    const tbody  = document.getElementById('tbodyInscripciones');

    tbody.innerHTML = pagina.length === 0
        ? `<tr><td colspan="8" class="table-empty"><i class="fas fa-inbox"></i> Sin resultados</td></tr>`
        : pagina.map(ins => `
            <tr>
                <td><strong>#${ins.dorsal}</strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="avatar avatar-sm">${ins.deportista.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
                        <span style="font-weight:600;">${ins.deportista}</span>
                    </div>
                </td>
                <td>${ins.evento}</td>
                <td><span class="badge badge-dark">${ins.categoria}</span></td>
                <td><i class="fas fa-map-pin" style="color:var(--mtb-primary);margin-right:4px;font-size:.75rem;"></i>${ins.pista}</td>
                <td style="color:var(--mtb-gray-600);font-size:.85rem;">${formatFecha(ins.fecha)}</td>
                <td><span class="badge ${badge[ins.estatus]||'badge-dark'}"><span class="dot"></span>${ins.estatus}</span></td>
                <td class="center">
                    <div style="display:flex;gap:4px;justify-content:center;">
                        <button class="btn btn-ghost btn-sm" title="Ver detalle"
                                onclick="verDetalle(${ins.dorsal})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-ghost btn-sm" title="Editar"
                                style="color:var(--mtb-info)">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-ghost btn-sm" title="Eliminar"
                                style="color:var(--mtb-danger)"
                                onclick="eliminarFila(${ins.dorsal})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

    renderPaginacion();
}

/* ──────────────────────────────────────────────────────────────
   RENDER: PAGINACIÓN
─────────────────────────────────────────────────────────────── */
function renderPaginacion() {
    const total    = datosFiltrados.length;
    const totalPag = Math.ceil(total / tamPagina);
    const inicio   = total === 0 ? 0 : (paginaActual - 1) * tamPagina + 1;
    const fin      = Math.min(paginaActual * tamPagina, total);

    document.getElementById('paginacionInfo').textContent =
        `Mostrando ${inicio}–${fin} de ${total} inscripciones`;

    const container = document.getElementById('paginacionControls');
    let html = '';

    html += `<button class="page-btn" onclick="irPagina(${paginaActual-1})" ${paginaActual===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;

    const desde = Math.max(1, paginaActual - 2);
    const hasta = Math.min(totalPag, paginaActual + 2);
    if (desde > 1) html += `<button class="page-btn" onclick="irPagina(1)">1</button>${desde>2?'<span style="padding:0 4px;color:var(--mtb-gray-500)">…</span>':''}`;
    for (let i = desde; i <= hasta; i++) {
        html += `<button class="page-btn ${i===paginaActual?'active':''}" onclick="irPagina(${i})">${i}</button>`;
    }
    if (hasta < totalPag) html += `${hasta<totalPag-1?'<span style="padding:0 4px;color:var(--mtb-gray-500)">…</span>':''}<button class="page-btn" onclick="irPagina(${totalPag})">${totalPag}</button>`;
    html += `<button class="page-btn" onclick="irPagina(${paginaActual+1})" ${paginaActual===totalPag||totalPag===0?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;

    container.innerHTML = html;
}

function irPagina(n) {
    const total = Math.ceil(datosFiltrados.length / tamPagina);
    if (n < 1 || n > total) return;
    paginaActual = n;
    renderInscripciones();
    window.scrollTo({ top:0, behavior:'smooth' });
}

function cambiarTamano() {
    tamPagina    = parseInt(document.getElementById('pageSize').value);
    paginaActual = 1;
    renderInscripciones();
}

/* ──────────────────────────────────────────────────────────────
   FILTROS
─────────────────────────────────────────────────────────────── */
function aplicarFiltros() {
    const evento    = document.getElementById('filtroEvento').value.toLowerCase();
    const categoria = document.getElementById('filtroCategoria').value.toLowerCase();
    const estatus   = document.getElementById('filtroEstatus').value.toLowerCase();
    const busqueda  = document.getElementById('buscarDeportista').value.toLowerCase().trim();

    datosFiltrados = DATA_INSCRIPCIONES.filter(ins => {
        return (!evento    || ins.evento.toLowerCase().includes(evento))
            && (!categoria || ins.categoria.toLowerCase() === categoria)
            && (!estatus   || ins.estatus.toLowerCase() === estatus)
            && (!busqueda  || ins.deportista.toLowerCase().includes(busqueda)
                           || String(ins.dorsal).includes(busqueda));
    });

    paginaActual = 1;
    renderInscripciones();
}

function limpiarFiltros() {
    ['filtroEvento','filtroCategoria','filtroEstatus','buscarDeportista']
        .forEach(id => document.getElementById(id).value = '');
    datosFiltrados = [...DATA_INSCRIPCIONES];
    paginaActual   = 1;
    renderInscripciones();
}

/* ──────────────────────────────────────────────────────────────
   MODALES
─────────────────────────────────────────────────────────────── */
function abrirModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}

function abrirModalInscripcion() { abrirModal('modalInscripcion'); }
function abrirModalEvento()      { abrirModal('modalEvento'); }

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(this.id);
    });
});

function guardarInscripcion(e) {
    if (e) e.preventDefault();
    cerrarModal('modalInscripcion');
    showToast('Inscripción guardada correctamente', 'success');
}

/* ──────────────────────────────────────────────────────────────
   DETALLE DE INSCRIPCIÓN
─────────────────────────────────────────────────────────────── */
function verDetalle(dorsal) {
    const ins = DATA_INSCRIPCIONES.find(i => i.dorsal === dorsal);
    if (!ins) return;

    const badge = { 'Confirmado':'badge-success', 'Pendiente':'badge-warning', 'Cancelado':'badge-danger' };

    document.getElementById('modalDetalleBody').innerHTML = `
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
            <div class="avatar avatar-lg">${ins.deportista.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
            <div>
                <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--mtb-dark);">${ins.deportista}</div>
                <div style="color:var(--mtb-gray-600);font-size:.875rem;">Dorsal #${ins.dorsal} · ${ins.categoria}</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="card" style="padding:16px;"><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Evento</div><div style="font-weight:700;">${ins.evento}</div></div>
            <div class="card" style="padding:16px;"><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Pista</div><div style="font-weight:700;">${ins.pista}</div></div>
            <div class="card" style="padding:16px;"><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Fecha</div><div style="font-weight:700;">${formatFecha(ins.fecha)}</div></div>
            <div class="card" style="padding:16px;"><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--mtb-gray-500);letter-spacing:.5px;margin-bottom:4px;">Estatus</div><span class="badge ${badge[ins.estatus]||'badge-dark'}">${ins.estatus}</span></div>
        </div>
    `;
    abrirModal('modalDetalle');
}

/* ──────────────────────────────────────────────────────────────
   ELIMINAR FILA
─────────────────────────────────────────────────────────────── */
function eliminarFila(dorsal) {
    if (!confirm(`¿Eliminar inscripción #${dorsal}?`)) return;
    const idx = DATA_INSCRIPCIONES.findIndex(i => i.dorsal === dorsal);
    if (idx > -1) DATA_INSCRIPCIONES.splice(idx, 1);
    datosFiltrados = datosFiltrados.filter(i => i.dorsal !== dorsal);
    renderInscripciones();
    showToast('Inscripción eliminada', 'danger');
}

/* ──────────────────────────────────────────────────────────────
   SIDEBAR RESPONSIVE
─────────────────────────────────────────────────────────────── */
document.getElementById('toggleSidebar').addEventListener('click', function() {
    document.getElementById('mtbSidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});

document.getElementById('sidebarOverlay').addEventListener('click', function() {
    document.getElementById('mtbSidebar').classList.remove('open');
    this.classList.remove('active');
});

/* ──────────────────────────────────────────────────────────────
   TOAST
─────────────────────────────────────────────────────────────── */
function showToast(msg, tipo = 'primary') {
    const iconos  = { success:'fa-circle-check', danger:'fa-circle-xmark', warning:'fa-triangle-exclamation', info:'fa-circle-info', primary:'fa-bell' };
    const colores = { success:'var(--mtb-success)', danger:'var(--mtb-danger)', warning:'var(--mtb-warning)', info:'var(--mtb-info)', primary:'var(--mtb-primary)' };

    const container = document.getElementById('toastContainer');
    const toast     = document.createElement('div');
    toast.className = 'toast';
    toast.style.borderLeftColor = colores[tipo] || colores.primary;
    toast.innerHTML = `<i class="fas ${iconos[tipo]||iconos.primary}" style="color:${colores[tipo]||colores.primary};"></i><span>${msg}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.cssText += 'opacity:0;transform:translateX(30px);transition:all .3s ease;';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/* ──────────────────────────────────────────────────────────────
   UTILIDADES
─────────────────────────────────────────────────────────────── */
function formatFecha(iso) {
    const [y, m, d] = iso.split('-');
    const meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    return `${parseInt(d)} ${meses[parseInt(m)-1]} ${y}`;
}
