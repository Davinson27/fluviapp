<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Crear Cuenta - ' . APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .register-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            background: #ffffff;
            width: 100%;
            max-width: 520px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="register-card mx-auto p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-6 text-primary mb-2">
                <i class="fa-solid fa-ship"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Crea tu Cuenta en <?= APP_NAME ?></h3>
            <p class="text-muted small">Regístrate para comprar pasajes en línea y solicitar encomiendas fluviales</p>
        </div>

        <?php 
        $flash = SessionHelper::getFlash();
        if ($flash): 
        ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/registro" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold text-secondary">Nombre Completo *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Carlos Mario Pérez">
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label for="documento" class="form-label fw-semibold text-secondary">Cédula / Documento *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-id-card text-muted"></i></span>
                        <input type="text" class="form-control" id="documento" name="documento" required placeholder="1045678901">
                    </div>
                </div>
                <div class="col-6">
                    <label for="telefono" class="form-label fw-semibold text-secondary">Teléfono / WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted"></i></span>
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="3001234567">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary">Correo Electrónico *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="nombre@correo.com">
                </div>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-6">
                    <label for="password" class="form-label fw-semibold text-secondary">Contraseña *</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Mínimo 6 caract.">
                </div>
                <div class="col-6">
                    <label for="password_confirm" class="form-label fw-semibold text-secondary">Confirmar *</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="Repetir clave">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="fa-solid fa-user-plus me-2"></i>Registrarme y Continuar
            </button>

            <div class="text-center small text-muted">
                ¿Ya tienes una cuenta? <a href="<?= BASE_URL ?>/login" class="fw-bold text-primary text-decoration-none">Iniciar Sesión</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
