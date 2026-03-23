<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> UTN - Acceso al Sistema </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="css/logo_utn.ico">
    <style>
        /* Estilo rápido para la alerta de error */
        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="split-screen"> 
        <div class="left-pane">
            <div class="overlay">
                <div class="brand">
                    <img src="css/logo_utn.ico" alt="Logo UTN" class="brand-logo">
                    <h2 class="brand-text">Universidad Tecnológica de Nogales</h2>
                </div>
                <div class="welcome-text">
                    <h1>Gestión de Mobiliario</h1>
                    <h3>Plataforma integral para el control, inventario y administración de activos universitarios.</h3>
                </div>
            </div>
        </div>
        <div class="right-pane">
            <div class="login-container">  
                <div class="mobile-logo">
                    <img src="css/logo_utn.ico" alt="Logo UTN">
                </div>
                <div class="header-text">
                    <h2>Ingresa tus credenciales para continuar.</h2>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="iniciarSesion.php">   
                    <div class="input-group">
                        <label>Matrícula</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" name="nombre" placeholder="Matrícula" required autocomplete="off">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="contraseña" id="contraseña" placeholder="••••••••" required>
                            <i class="fa-regular fa-eye toggle-password" onclick="togglePassword()" style="cursor:pointer; position: absolute; right: 15px; color: #6b7280;"></i>
                        </div>
                    </div>
                    <button type="submit" name="submit" class="btn-login">
                        Acceder al Sistema
                    </button>
                    <a href="mtb-user-admin.php" style="display:block; text-align:center; margin-top:15px; font-size:0.8rem; color:#6b7280;">
                        ¿Olvidaste tu contraseña?
                    </a> 
                </form>
                <p class="footer-copy">&copy; <?php echo date("Y"); ?> Universidad Tecnológica de Nogales. </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('contraseña'); // Corregido el ID para que coincida con el input
            const icon = document.querySelector('.toggle-password');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>