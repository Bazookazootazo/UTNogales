<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTN - Acceso al Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="css/logo_utn.ico">
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

            <form method="post" action="iniciarSesion.php">
                <div class="input-group">
                    <label>Correo electronico</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="nombre" placeholder="Ingresa tu Correo electronico" required autocomplete="off">
                    </div>
                </div>

                <div class="input-group">
                    <label>Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="contraseña" id="contraseña" placeholder="••••••••" required>
                        <i class="fa-regular fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-login">
                    Acceder al Sistema
                </button>

                <div class="login-footer">
                    <a href="registro.php">¿No tienes una cuenta? Regístrate aquí</a>
                    <p class="copy">&copy; <?php echo date("Y"); ?> MTB Nogales Sonora</p>
                </div>
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