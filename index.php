<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> UTN - Acceso al Sistema </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="css/logo_utn.ico">
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
                    <h3>Plataforma integral para el control, invnetario y administración de activos universitarios.</h3>
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
                <form method="post" action="iniciarSesion.php">   
                    <div class="input-group">
                        <label>Matricula</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" name="nombre" placeholder="Matricula" required autocomplete="off">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="contraseña" id="contraseña" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" name="submit" class="btn-login">
                        Acceder al Sistema
                    </button> 
                </form>
                <p class="footer-copy">&copy; <?php echo date("Y"); ?> Universidad Tecnológica de Nogales. </p>
            </div>

                

        </div>
    </div>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
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