<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Moderno</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-200 min-h-screen">

    <div class="navbar bg-base-100 shadow-sm mb-8">
        <div class="flex-1">
            <a href="index.php" class="btn btn-ghost text-xl">🚀 MiApp</a>
        </div>
        <div class="flex-none gap-2">
            <div class="form-control">
                <input type="text" placeholder="Buscar..." class="input input-bordered w-24 md:w-auto" />
            </div>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Avatar de usuario" src="https://ui-avatars.com/api/?name=Admin+User&background=random" />
                    </div>
                </div>
                <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                    <li><a class="justify-between">Perfil <span class="badge">Nuevo</span></a></li>
                    <li><a>Configuración</a></li>
                    <li><a class="text-error">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <main class="container mx-auto px-4">