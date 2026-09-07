<?php
$currentUser = AuthHelper::user();
$currentRoute = $_SERVER['REQUEST_URI'] ?? '';
?>
<!-- Sidebar -->
<div class="sidebar bg-dark text-white" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom border-secondary">
        <div class="d-flex justify-content-center mb-2">
            <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img shadow" style="width: 58px; height: 58px;">
        </div>
        <div class="d-flex align-items-center justify-content-center">
            <span>Fluvi<span class="text-info">App</span></span>
        </div>
        <div class="small text-muted fs-6 fw-normal text-capitalize mt-1">Gestión Fluvial</div>
        <?php if (!empty($currentUser['departamento'])): ?>
            <div class="mt-2"><span class="badge bg-warning text-dark text-capitalize fw-semibold px-2 py-1" style="font-size: 0.75rem;"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($currentUser['departamento']) ?></span></div>
        <?php elseif (($currentUser['rol'] ?? '') === 'admin'): ?>
            <div class="mt-2"><span class="badge bg-danger text-white text-capitalize fw-semibold px-2 py-1" style="font-size: 0.75rem;"><i class="fa-solid fa-earth-americas me-1"></i>Ámbito Nacional</span></div>
        <?php endif; ?>
    </div>
    <div class="list-group list-group-flush my-3">
        <?php if (AuthHelper::isCliente()): ?>
        <!-- Menú del Portal de Pasajeros -->
        <a href="<?= BASE_URL ?>/portal" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/portal') && !str_contains($currentRoute, '/cliente/') ? 'active' : '' ?>">
            <i class="fa-solid fa-compass me-3 text-info"></i>Explorar Rutas
        </a>
        <a href="<?= BASE_URL ?>/cliente/mis-boletos" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/mis-boletos') ? 'active' : '' ?>">
            <i class="fa-solid fa-ticket me-3 text-success"></i>Mis Tiquetes
        </a>
        <a href="<?= BASE_URL ?>/cliente/enviar-encomienda" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/enviar-encomienda') ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-packing me-3 text-warning"></i>Enviar Encomienda
        </a>
        <a href="<?= BASE_URL ?>/cliente/mis-encomiendas" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/mis-encomiendas') ? 'active' : '' ?>">
            <i class="fa-solid fa-box me-3 text-primary"></i>Mis Encomiendas
        </a>
        <a href="<?= BASE_URL ?>/perfil" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/perfil') ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-user me-3 text-info"></i>Mi Perfil
        </a>
        <?php else: ?>
        <!-- Menú Administrativo / Operativo -->
        <a href="<?= BASE_URL ?>/dashboard" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge me-3 text-info"></i>Dashboard
        </a>
        <a href="<?= BASE_URL ?>/viajes" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/viajes') ? 'active' : '' ?>">
            <i class="fa-solid fa-compass me-3 text-warning"></i>Itinerarios y Viajes
        </a>
        <a href="<?= BASE_URL ?>/boletos" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/boletos') ? 'active' : '' ?>">
            <i class="fa-solid fa-ticket me-3 text-success"></i>Boletería / Pasajes
        </a>
        <a href="<?= BASE_URL ?>/cargas" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/cargas') ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-packing me-3 text-primary"></i>Carga y Encomiendas
        </a>
        <a href="<?= BASE_URL ?>/embarcaciones" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/embarcaciones') ? 'active' : '' ?>">
            <i class="fa-solid fa-anchor me-3 text-info"></i>Flota Fluvial
        </a>
        <a href="<?= BASE_URL ?>/rutas" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/rutas') ? 'active' : '' ?>">
            <i class="fa-solid fa-route me-3 text-danger"></i>Rutas Fluviales
        </a>
        <a href="<?= BASE_URL ?>/muelles" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/muelles') ? 'active' : '' ?>">
            <i class="fa-solid fa-water me-3 text-cyan"></i>Muelles y Puertos
        </a>
        <?php if ($currentUser && in_array($currentUser['rol'], ['admin', 'operador'])): ?>
        <a href="<?= BASE_URL ?>/reportes" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/reportes') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line me-3 text-light"></i>Reportes y Estadísticas
        </a>
        <?php endif; ?>
        <?php if ($currentUser && $currentUser['rol'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/usuarios" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/usuarios') ? 'active' : '' ?>">
            <i class="fa-solid fa-users-gear me-3 text-warning"></i>Usuarios y Roles
        </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/perfil" class="list-group-item list-group-item-action bg-transparent text-white <?= str_contains($currentRoute, '/perfil') ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-user me-3 text-info"></i>Mi Perfil
        </a>
        <?php endif; ?>

        <div class="px-3 mt-4 pt-3 border-top border-secondary">
            <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-outline-info btn-sm w-100 text-start fw-semibold mb-2">
                <i class="fa-solid fa-globe me-2"></i>Página Pública / Landing
            </a>
            <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-danger btn-sm w-100 text-start fw-semibold">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar Sesión
            </a>
        </div>
    </div>
</div>
<!-- /#sidebar-wrapper -->

<!-- Page Content Wrapper -->
<div id="page-content-wrapper" class="w-100">
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-app px-4 py-3">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="menu-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="m-0 text-dark fw-bold"><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></h5>
        </div>
        
        <div class="ms-auto d-flex align-items-center gap-2">
            <!-- Botón Modo Oscuro / Claro -->
            <button type="button" class="theme-toggle-btn shadow-sm" title="Alternar Modo Oscuro / Claro" aria-label="Alternar Tema">
                <i class="fa-solid fa-moon text-info"></i>
            </button>

            <div class="me-2 text-end d-none d-md-block">
                <div class="fw-bold text-dark"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?></div>
                <?php if (($currentUser['rol'] ?? '') === 'admin'): ?>
                    <?php if (!empty($currentUser['departamento'])): ?>
                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem;"><i class="fa-solid fa-location-dot me-1"></i>Admin <?= htmlspecialchars($currentUser['departamento']) ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger text-white" style="font-size: 0.7rem;"><i class="fa-solid fa-earth-americas me-1"></i>Admin General</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;"><?= htmlspecialchars($currentUser['rol'] ?? 'Operador') ?></span>
                    <?php if (!empty($currentUser['departamento'])): ?>
                        <span class="badge bg-info-subtle text-info-emphasis ms-1" style="font-size: 0.7rem;"><?= htmlspecialchars($currentUser['departamento']) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-danger btn-sm px-3 fw-bold d-flex align-items-center" title="Cerrar Sesión">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
            </a>

            <div class="dropdown">
                <button class="btn p-0 border-0 shadow-sm dropdown-toggle d-flex align-items-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if (!empty($currentUser['foto']) && file_exists(ROOT_PATH . '/public/' . ltrim($currentUser['foto'], '/'))): ?>
                        <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars(ltrim($currentUser['foto'], '/')) ?>" alt="Avatar" class="rounded-circle object-fit-cover border border-2 border-primary" style="width: 38px; height: 38px;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 38px; height: 38px;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                    <li>
                        <div class="dropdown-header">
                            <div class="fw-bold text-dark"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?></div>
                            <small class="text-muted"><?= htmlspecialchars($currentUser['email'] ?? '') ?></small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item fw-semibold" href="<?= BASE_URL ?>/perfil">
                            <i class="fa-solid fa-id-card me-2 text-primary"></i>Mi Perfil y Foto
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger fw-semibold" href="<?= BASE_URL ?>/logout">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <div class="container-fluid px-4 py-4">
        <?php 
        $flash = SessionHelper::getFlash();
        if ($flash): 
        ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
