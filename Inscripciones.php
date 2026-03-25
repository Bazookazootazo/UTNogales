<?php 
$pagina_actual = 'inicio';
require_once 'config/auth.php'; 
?>

<?php include_once 'includes/header_sidebar.php'; ?>

    <div class="layout">
<main class="content">
            <h2>Módulo de Inscripciones</h2>

            <?php if ($_SESSION['rol'] === 'cliente'): ?>
                <form id="formInscripcion">
                    <label>Selecciona tu carrera:</label>
                    <select name="id_carrera" id="id_carrera" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($carreras as $c): ?>
                            <option value="<?= $c['id_carrera'] ?>"><?= $c['nombre_carrera'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Inscribirme</button>
                </form>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <div class="admin-panel">
                    <h3>Gestión General (Admin)</h3>
                    </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    // Lógica de envío Fetch
    document.getElementById('formInscripcion')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('procesar_inscripcion.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    title: data.exito == 1 ? '¡Listo!' : 'Atención',
                    text: data.mensaje,
                    icon: data.exito == 1 ? 'success' : 'warning',
                    confirmButtonColor: '#E8630A'
                });
            });
    });
    </script>

<?php include 'includes/footer_scripts.php'; ?>