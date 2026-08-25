<?php
$currentUser = AuthHelper::user();
$currentRoute = $_SERVER['REQUEST_URI'] ?? '';
?>
<!-- Sidebar -->
<div class="sidebar bg-dark text-white" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom border-secondary">
        <i class="fa-solid fa-ship me-2 text-info"></i><?= APP_NAME ?>
        <div class="small text-muted fs-6 fw-normal text-capitalize mt-1">Gestión Fluvial</div>
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
        <?php endif; ?>
    </div>
</div>
<!-- /#sidebar-wrapper -->

<!-- Page Content Wrapper -->
<div id="page-content-wrapper" class="w-100">
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary me-3" id="menu-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h5 class="m-0 text-dark fw-bold"><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></h5>
        </div>
        
        <div class="ms-auto d-flex align-items-center">
            <div class="me-3 text-end d-none d-md-block">
                <div class="fw-bold text-dark"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?></div>
                <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;"><?= htmlspecialchars($currentUser['rol'] ?? 'Operador') ?></span>
            </div>
            <div class="dropdown">
                <button class="btn btn-light rounded-circle shadow-sm dropdown-toggle p-2" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user-tie text-primary"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                    <li><h6 class="dropdown-header"><?= htmlspecialchars($currentUser['email'] ?? '') ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout">
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
