<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTN - Acceso al Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="icon" href="css/logo_utn.ico">
    <style>
         /* Estilo de los grupos de input */
        .input-group { margin-bottom: 18px; }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

 

        .input-wrapper input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #334155;
            border-radius: 10px;
            font-size: 0.95rem;
            background-color: #0f172a;
            color: white;
            transition: all 0.3s;
        }


</style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>MTB Nogales Sonora</h2>
                <p>Mountain Bike System</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="procesar_reactivacion.php">
               <div class="input-group">
                    <label>Notamos que tu cuenta está desactivada. Si decides reactivarla hoy, conservaras todas tus inscripciones y estadisticas previas.</label>
                    </div>
                    <button type="submit" name="submit" class="btn-login">
                    Reactivar mi cuenta
                </button>
                <a href="index.php" class="btn-cancelar" style="display: block; width-line: 100%; width: 397px; text-align: center; text-decoration: none; background-color: #ae1e1e; margin-top: 10px; padding: 12px; border-radius: 10px; color: white; font-weight: 600; font-size: 0.95rem;">
                 Cancelar
                </a>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('contraseña');
            const icon = document.querySelector('.toggle-password');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>