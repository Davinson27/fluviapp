<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>/public/img/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <!-- Theme Manager (Modo Oscuro) -->
    <script src="<?= BASE_URL ?>/public/js/theme.js"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 85% 15%, rgba(14, 165, 233, 0.25) 0%, transparent 60%),
                        linear-gradient(135deg, #050b14 0%, #0b1f3b 50%, #034b79 100%);
        }
        [data-bs-theme="dark"] body {
            background: radial-gradient(circle at 85% 15%, rgba(14, 165, 233, 0.15) 0%, transparent 60%),
                        linear-gradient(135deg, #02060d 0%, #07101d 50%, #091c33 100%);
        }
        .login-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 440px;
            position: relative;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="login-card mx-auto p-4 p-md-5 card">
        <div class="position-absolute top-0 end-0 m-3">
            <button type="button" class="theme-toggle-btn shadow-sm" title="Alternar Modo Oscuro / Claro" aria-label="Alternar Tema">
                <i class="fa-solid fa-moon text-info"></i>
            </button>
        </div>
        <div class="text-center mb-4">
            <div class="mb-3 d-flex justify-content-center">
                <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img shadow" style="width: 80px; height: 80px;">
            </div>
            <h3 class="fw-bold text-dark mb-1"><?= APP_NAME ?></h3>
            <p class="text-muted small"><?= APP_TAGLINE ?></p>
        </div>

        <?php 
        $flash = SessionHelper::getFlash();
        if ($flash): 
        ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST">
            <?= SessionHelper::csrfField() ?>
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control" id="email" name="email" value="admin@fluviapp.com" required placeholder="nombre@correo.com" autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-secondary">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control" id="password" name="password" value="admin123" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar al Sistema
            </button>

            <div class="text-center small text-muted mb-3">
                ¿No tienes cuenta de pasajero? <a href="<?= BASE_URL ?>/registro" class="fw-bold text-primary text-decoration-none">Regístrate aquí</a>
            </div>
        </form>

        <div class="mt-3 p-3 bg-light rounded-4 text-center small text-muted border">
            <div class="fw-bold text-dark mb-2">Cuentas de Acceso Rápido:</div>
            <div class="d-flex gap-2 justify-content-center mb-2">
                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" onclick="document.getElementById('email').value='admin@fluviapp.com';document.getElementById('password').value='admin123';">
                    <i class="fa-solid fa-user-shield me-1"></i>Admin
                </button>
                <button type="button" class="btn btn-outline-success btn-sm py-1 px-2" onclick="document.getElementById('email').value='cliente@fluviapp.com';document.getElementById('password').value='cliente123';">
                    <i class="fa-solid fa-user me-1"></i>Cliente / Pasajero
                </button>
            </div>
            <div style="font-size: 0.75rem;">Claves: <code>admin123</code> / <code>cliente123</code></div>
        </div>

        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/" class="text-secondary text-decoration-none small fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i>Volver a la Página Principal (Servicios Fluviales)
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
