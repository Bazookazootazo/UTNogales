<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTB - Registro e Inscripciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== REUTILIZACIÓN DE VARIABLES DEL LOGIN ORIGINAL ===== */
        :root {
            --primary: #008170;
            --primary-dark: #005B41;
            --primary-light: #78C841;
            --primary-three: #343634;
            --bg-input: #f8fafc;
            --border-input: #e2e8f0;
            --text-dark: #1e293b;
            --font-principal: 'Source Sans 3', sans-serif;
            --card-bg: #ffffff;
            --shadow-sm: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-md: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        @import url('https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-principal);
            background: linear-gradient(135deg, #f4f7fc 0%, #e9f0f5 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        /* Contenedor principal */
        .app-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 2rem;
            color: var(--primary-dark);
        }
        .header p {
            color: #4a5568;
        }

        /* Tabs */
        .tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .tab-btn {
            background: var(--card-bg);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
            font-family: var(--font-principal);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tab-btn i {
            font-size: 1.1rem;
        }
        .tab-btn.active {
            background: var(--primary);
            color: white;
        }
        .tab-btn:hover:not(.active) {
            background: #e2e8f0;
        }

        /* Paneles */
        .panel {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tarjetas de formulario */
        .form-card {
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: var(--shadow-md);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        .form-group label i {
            margin-right: 6px;
            color: var(--primary);
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-input);
            border-radius: 12px;
            background: var(--bg-input);
            font-family: var(--font-principal);
            transition: all 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,129,112,0.2);
            background: white;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn-primary, .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-family: var(--font-principal);
        }
        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, var(--primary-light), var(--primary));
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: var(--text-dark);
        }
        .btn-secondary:hover {
            background: #cbd5e1;
        }
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        /* Tablas */
        .table-responsive {
            overflow-x: auto;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            background: white;
            margin-top: 1rem;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        .data-table thead tr {
            background: #f1f5f9;
            border-bottom: 2px solid var(--border-input);
        }
        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-input);
        }
        .data-table tbody tr:hover {
            background: #f8fafc;
        }
        .badge {
            background: var(--primary-light);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 40px;
            font-size: 0.8rem;
        }

        /* Mensajes */
        .message {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .form-card { padding: 1rem; }
            .form-row { flex-direction: column; gap: 0.8rem; }
            .tabs { gap: 0.5rem; }
            .tab-btn { padding: 0.5rem 1rem; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
<div class="app-container">
    <div class="header">
        <h1><i class="fas fa-bicycle"></i> MTB - Sistema de Inscripciones</h1>
        <p>Regístrate como ciclista o inscríbete en una carrera</p>
    </div>

    <div class="tabs">
        <button class="tab-btn active" data-tab="user"><i class="fas fa-user-plus"></i> Usuario</button>
        <button class="tab-btn" data-tab="admin"><i class="fas fa-users"></i> Administrador</button>
    </div>

    <!-- PANEL USUARIO -->
    <div id="userPanel" class="panel active">
        <!-- Formulario Registro Ciclista -->
        <div class="form-card">
            <h2><i class="fas fa-id-card"></i> Registro de Ciclista</h2>
            <form id="formRegistroCiclista">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nombre completo</label>
                        <input type="text" id="ciclistaNombre" placeholder="Ej: Ana García" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="ciclistaEmail" placeholder="ana@ejemplo.com" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Categoría</label>
                        <select id="ciclistaCategoria" required>
                            <option value="">Seleccione</option>
                            <option value="Élite">Élite (18-29)</option>
                            <option value="Máster 30+">Máster 30+</option>
                            <option value="Juvenil">Juvenil (14-17)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Teléfono (opcional)</label>
                        <input type="text" id="ciclistaTelefono" placeholder="Opcional">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Registrar Ciclista</button>
                    <button type="reset" class="btn-secondary"><i class="fas fa-undo"></i> Limpiar</button>
                </div>
            </form>
        </div>

        <!-- Formulario Inscripción a Carrera -->
        <div class="form-card">
            <h2><i class="fas fa-flag-checkered"></i> Inscripción a Carrera</h2>
            <form id="formInscripcionCarrera">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Ciclista (selecciona tu nombre)</label>
                        <select id="inscripcionCiclista" required>
                            <option value="">-- Elige un ciclista registrado --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Carrera</label>
                        <select id="inscripcionCarrera" required>
                            <option value="">-- Selecciona carrera --</option>
                            <option value="Enduro MTB">Enduro MTB - 15/06/2026</option>
                            <option value="Cross Country">Cross Country - 22/06/2026</option>
                            <option value="Maratón">Maratón - 30/06/2026</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-bicycle"></i> Inscribirme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- PANEL ADMINISTRADOR -->
    <div id="adminPanel" class="panel">
        <div class="form-card">
            <h2><i class="fas fa-users"></i> Solicitudes de Registro de Ciclistas</h2>
            <div class="table-responsive">
                <table class="data-table" id="tablaCiclistas">
                    <thead>
                        <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Categoría</th><th>Teléfono</th><th>Fecha registro</th><th>Estado</th></tr>
                    </thead>
                    <tbody id="tbodyCiclistas">
                        <!-- Datos dinámicos -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-card">
            <h2><i class="fas fa-ticket-alt"></i> Solicitudes de Inscripción a Carreras</h2>
            <div class="table-responsive">
                <table class="data-table" id="tablaInscripciones">
                    <thead>
                        <tr><th>ID</th><th>Ciclista</th><th>Carrera</th><th>Fecha solicitud</th><th>Estado</th></tr>
                    </thead>
                    <tbody id="tbodyInscripciones">
                        <!-- Datos dinámicos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== MODELO DE DATOS (almacenado en localStorage) =====
    let ciclistas = [];
    let inscripciones = [];

    // Cargar datos iniciales desde localStorage o crear ejemplos
    function cargarDatos() {
        const storedCiclistas = localStorage.getItem('mtb_ciclistas');
        if (storedCiclistas) {
            ciclistas = JSON.parse(storedCiclistas);
        } else {
            // Datos de ejemplo
            ciclistas = [
                { id: 1, nombre: "Juan Pérez", email: "juan@example.com", categoria: "Élite", telefono: "555-1234", fechaRegistro: "2026-03-15", estado: "pendiente" },
                { id: 2, nombre: "María López", email: "maria@example.com", categoria: "Máster 30+", telefono: "555-5678", fechaRegistro: "2026-03-16", estado: "pendiente" }
            ];
            guardarCiclistas();
        }

        const storedInscripciones = localStorage.getItem('mtb_inscripciones');
        if (storedInscripciones) {
            inscripciones = JSON.parse(storedInscripciones);
        } else {
            inscripciones = [
                { id: 1, ciclistaId: 1, ciclistaNombre: "Juan Pérez", carrera: "Enduro MTB", fechaSolicitud: "2026-03-17", estado: "pendiente" }
            ];
            guardarInscripciones();
        }
    }

    function guardarCiclistas() {
        localStorage.setItem('mtb_ciclistas', JSON.stringify(ciclistas));
    }

    function guardarInscripciones() {
        localStorage.setItem('mtb_inscripciones', JSON.stringify(inscripciones));
    }

    // Helper para obtener fecha actual YYYY-MM-DD
    function fechaActual() {
        return new Date().toISOString().slice(0,10);
    }

    // Renderizar listas en admin y actualizar select de ciclistas en formulario de inscripción
    function actualizarVistas() {
        renderTablaCiclistas();
        renderTablaInscripciones();
        actualizarSelectCiclistas();
    }

    function renderTablaCiclistas() {
        const tbody = document.getElementById('tbodyCiclistas');
        tbody.innerHTML = '';
        ciclistas.forEach(c => {
            const row = tbody.insertRow();
            row.insertCell(0).innerText = c.id;
            row.insertCell(1).innerText = c.nombre;
            row.insertCell(2).innerText = c.email;
            row.insertCell(3).innerText = c.categoria;
            row.insertCell(4).innerText = c.telefono || '—';
            row.insertCell(5).innerText = c.fechaRegistro;
            const estadoCell = row.insertCell(6);
            estadoCell.innerHTML = `<span class="badge">${c.estado === 'pendiente' ? 'Pendiente' : 'Aprobado'}</span>`;
            // Podríamos agregar botón de aprobar si se desea, pero el enunciado solo pide visualizar
        });
    }

    function renderTablaInscripciones() {
        const tbody = document.getElementById('tbodyInscripciones');
        tbody.innerHTML = '';
        inscripciones.forEach(i => {
            const row = tbody.insertRow();
            row.insertCell(0).innerText = i.id;
            row.insertCell(1).innerText = i.ciclistaNombre;
            row.insertCell(2).innerText = i.carrera;
            row.insertCell(3).innerText = i.fechaSolicitud;
            const estadoCell = row.insertCell(4);
            estadoCell.innerHTML = `<span class="badge">${i.estado === 'pendiente' ? 'Pendiente' : 'Aprobado'}</span>`;
        });
    }

    function actualizarSelectCiclistas() {
        const select = document.getElementById('inscripcionCiclista');
        select.innerHTML = '<option value="">-- Elige un ciclista registrado --</option>';
        ciclistas.forEach(c => {
            const option = document.createElement('option');
            option.value = c.id;
            option.textContent = `${c.nombre} (${c.categoria})`;
            select.appendChild(option);
        });
    }

    // Registrar nuevo ciclista
    document.getElementById('formRegistroCiclista').addEventListener('submit', (e) => {
        e.preventDefault();
        const nombre = document.getElementById('ciclistaNombre').value.trim();
        const email = document.getElementById('ciclistaEmail').value.trim();
        const categoria = document.getElementById('ciclistaCategoria').value;
        const telefono = document.getElementById('ciclistaTelefono').value.trim();

        if (!nombre || !email || !categoria) {
            mostrarMensaje('error', 'Por favor completa todos los campos obligatorios.');
            return;
        }

        // Validar email único (simulación)
        if (ciclistas.some(c => c.email === email)) {
            mostrarMensaje('error', 'Este email ya está registrado.');
            return;
        }

        const nuevoId = ciclistas.length > 0 ? Math.max(...ciclistas.map(c => c.id)) + 1 : 1;
        const nuevoCiclista = {
            id: nuevoId,
            nombre: nombre,
            email: email,
            categoria: categoria,
            telefono: telefono,
            fechaRegistro: fechaActual(),
            estado: 'pendiente'
        };
        ciclistas.push(nuevoCiclista);
        guardarCiclistas();
        actualizarVistas();
        document.getElementById('formRegistroCiclista').reset();
        mostrarMensaje('success', '¡Ciclista registrado exitosamente! El administrador podrá ver la solicitud.');
    });

    // Inscribir a carrera
    document.getElementById('formInscripcionCarrera').addEventListener('submit', (e) => {
        e.preventDefault();
        const ciclistaId = parseInt(document.getElementById('inscripcionCiclista').value);
        const carrera = document.getElementById('inscripcionCarrera').value;

        if (!ciclistaId || !carrera) {
            mostrarMensaje('error', 'Selecciona un ciclista y una carrera.');
            return;
        }

        const ciclista = ciclistas.find(c => c.id === ciclistaId);
        if (!ciclista) {
            mostrarMensaje('error', 'Ciclista no encontrado.');
            return;
        }

        const nuevoId = inscripciones.length > 0 ? Math.max(...inscripciones.map(i => i.id)) + 1 : 1;
        const nuevaInscripcion = {
            id: nuevoId,
            ciclistaId: ciclistaId,
            ciclistaNombre: ciclista.nombre,
            carrera: carrera,
            fechaSolicitud: fechaActual(),
            estado: 'pendiente'
        };
        inscripciones.push(nuevaInscripcion);
        guardarInscripciones();
        actualizarVistas();
        document.getElementById('formInscripcionCarrera').reset();
        mostrarMensaje('success', '¡Inscripción enviada! El administrador revisará tu solicitud.');
    });

    // Mensajes flotantes temporales
    function mostrarMensaje(tipo, texto) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${tipo}`;
        msgDiv.innerHTML = `<i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${texto}`;
        const container = document.querySelector('.app-container');
        container.insertBefore(msgDiv, container.firstChild);
        setTimeout(() => msgDiv.remove(), 4000);
    }

    // Tabs switching
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = {
        user: document.getElementById('userPanel'),
        admin: document.getElementById('adminPanel')
    };
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-tab');
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            Object.values(panels).forEach(p => p.classList.remove('active'));
            panels[target].classList.add('active');
        });
    });

    // Inicialización
    cargarDatos();
    actualizarVistas();
</script>
</body>
</html>