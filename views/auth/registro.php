<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta de Usuario - <?= APP_NAME ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>/public/img/logo.jpg">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <!-- Theme Manager (Modo Oscuro) -->
    <script src="<?= BASE_URL ?>/public/js/theme.js"></script>

    <style>
        body {
            background-color: var(--bg-body, #f8fafc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 2.5rem 0;
            transition: background-color 0.3s ease;
        }
        .register-card {
            border: 1px solid var(--card-border, #e2e8f0);
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 520px;
            position: relative;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="register-card mx-auto p-4 p-md-5 card">
        <div class="position-absolute top-0 end-0 m-3">
            <button type="button" class="theme-toggle-btn shadow-sm" title="Alternar Modo Oscuro / Claro" aria-label="Alternar Tema">
                <i class="fa-solid fa-moon text-info"></i>
            </button>
        </div>
        <div class="text-center mb-4">
            <div class="mb-3 d-flex justify-content-center">
                <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img shadow" style="width: 80px; height: 80px;">
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

        <form action="<?= BASE_URL ?>/registro" method="POST" enctype="multipart/form-data">
            <?= SessionHelper::csrfField() ?>
            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold text-secondary">Nombre Completo *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Carlos Mario Pérez">
                </div>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label fw-semibold text-secondary">Foto de Perfil (Opcional)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-camera text-muted"></i></span>
                    <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif">
                </div>
                <small class="text-muted" style="font-size: 0.78rem;">Puedes subir tu foto ahora o más adelante desde tu perfil (máx. 5 MB).</small>
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

            <div class="row g-2 mb-3">
                <div class="col-md-7">
                    <label for="departamento" class="form-label fw-semibold text-secondary">
                        <i class="fa-solid fa-map-location-dot text-primary me-1"></i>Departamento Fluvial *
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-location-dot text-muted"></i></span>
                        <select class="form-select" id="departamento" name="departamento" required>
                            <option value="">-- Selecciona --</option>
                            <?php if (!empty($departamentos)): ?>
                                <?php foreach ($departamentos as $d): ?>
                                    <option value="<?= htmlspecialchars($d['departamento']) ?>">
                                        <?= htmlspecialchars($d['departamento']) ?> (<?= $d['total_muelles'] ?> puertos)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="Chocó">Chocó</option>
                                <option value="Bolívar">Bolívar</option>
                                <option value="Antioquia">Antioquia</option>
                                <option value="Amazonas">Amazonas</option>
                                <option value="Meta">Meta</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="genero" class="form-label fw-semibold text-secondary">
                        <i class="fa-solid fa-venus-mars text-primary me-1"></i>Género *
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-user-tag text-muted"></i></span>
                        <select class="form-select" id="genero" name="genero" required>
                            <option value="masculino">Masculino (Señor)</option>
                            <option value="femenino">Femenino (Señora)</option>
                            <option value="otro">Otro / Prefiero no decir</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">
                        <i class="fa-solid fa-circle-info me-1 text-info"></i>En tu portal se habilitarán únicamente las rutas y puertos del departamento seleccionado.
                    </small>
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

            <div class="text-center small text-muted mb-3">
                ¿Ya tienes una cuenta? <a href="<?= BASE_URL ?>/login" class="fw-bold text-primary text-decoration-none">Iniciar Sesión</a>
            </div>

            <div class="text-center border-top pt-3">
                <a href="<?= BASE_URL ?>/" class="text-secondary text-decoration-none small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i>Volver a la Página Principal (Servicios Fluviales)
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
