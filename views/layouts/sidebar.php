<?php
$currentUser = AuthHelper::user();
$currentRoute = $_SERVER['REQUEST_URI'] ?? '';
?>
<!-- Menú Lateral Desplegable (Offcanvas) -->
<div class="offcanvas offcanvas-start bg-dark text-white sidebar-offcanvas" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header border-bottom border-secondary py-3 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2 cursor-pointer brand-logo-trigger" 
             data-img-zoom="<?= BASE_URL ?>/public/img/logo.jpg" 
             data-img-title="Logo Oficial - FluviApp" 
             data-img-caption="Emblema y marca oficial del transporte fluvial de Colombia"
             title="Clic para ampliar logo">
            <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img shadow zoomable-image" style="width: 44px; height: 44px;">
            <div>
                <h5 class="offcanvas-title text-white fw-bold m-0" id="sidebarOffcanvasLabel">Fluvi<span class="text-info">App</span></h5>
                <small class="text-muted" style="font-size: 0.75rem;">Gestión Fluvial</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    
    <div class="offcanvas-body p-0 d-flex flex-column" style="overflow-y: auto;">
        <?php if (!empty($currentUser['departamento'])): ?>
            <div class="px-4 pt-3">
                <span class="badge bg-warning text-dark text-capitalize fw-semibold px-2 py-1 w-100 text-center shadow-sm" style="font-size: 0.75rem;"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($currentUser['departamento']) ?></span>
            </div>
        <?php elseif (($currentUser['rol'] ?? '') === 'admin'): ?>
            <div class="px-4 pt-3">
                <span class="badge bg-danger text-white text-capitalize fw-semibold px-2 py-1 w-100 text-center shadow-sm" style="font-size: 0.75rem;"><i class="fa-solid fa-earth-americas me-1"></i>Ámbito Nacional</span>
            </div>
        <?php endif; ?>

        <div class="list-group list-group-flush my-3 flex-grow-1 sidebar-offcanvas-menu">
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
        </div>

        <div class="px-3 py-3 border-top border-secondary mt-auto bg-black bg-opacity-25">
            <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-outline-info btn-sm w-100 text-start fw-semibold mb-2">
                <i class="fa-solid fa-globe me-2"></i>Página Pública / Landing
            </a>
            <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-danger btn-sm w-100 text-start fw-semibold">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar Sesión
            </a>
        </div>
    </div>
</div>
<!-- /#sidebarOffcanvas -->

<!-- Page Content Wrapper -->
<div id="page-content-wrapper" class="w-100">
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-app px-3 px-md-4 py-3" style="position: relative; z-index: 1050;">
        <div class="d-flex align-items-center">
            <!-- Botón 3 barritas que activa el menú lateral desplegable -->
            <button class="btn btn-outline-primary me-2 me-md-3 d-flex align-items-center justify-content-center shadow-sm" id="menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" title="Menú de Navegación" style="width: 42px; height: 42px; border-radius: 10px;">
                <i class="fa-solid fa-bars fs-5"></i>
            </button>
            <div class="d-flex align-items-center me-2 me-md-3 cursor-pointer brand-logo-trigger" 
                 data-img-zoom="<?= BASE_URL ?>/public/img/logo.jpg" 
                 data-img-title="Logo Oficial - FluviApp" 
                 data-img-caption="Emblema y marca oficial del transporte fluvial de Colombia"
                 title="Clic para ampliar logo">
                <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img me-2 zoomable-image" style="width: 38px; height: 38px; cursor: pointer;">
                <span class="fw-bold fs-5 d-none d-sm-inline" style="color: var(--primary-color);">Fluvi<span class="text-info">App</span></span>
            </div>
            <h5 class="m-0 text-dark fw-bold border-start ps-3 d-none d-md-inline-block"><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></h5>
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

            <?php 
            $userPhotoUrl = !empty($currentUser['foto']) && file_exists(ROOT_PATH . '/public/' . ltrim($currentUser['foto'], '/')) 
                ? BASE_URL . '/public/' . htmlspecialchars(ltrim($currentUser['foto'], '/')) 
                : null;
            $hasRealPhoto = !empty($userPhotoUrl);
            ?>
            <div class="d-flex align-items-center">
                <!-- Foto de Perfil con Clic para Ampliar -->
                <div class="user-avatar-zoom-container position-relative cursor-pointer me-1" 
                     data-img-zoom="<?= $hasRealPhoto ? $userPhotoUrl : BASE_URL . '/public/img/logo.jpg' ?>" 
                     data-img-title="<?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?> (Foto de Perfil)" 
                     data-img-caption="Fotografía de <?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?> &bull; <?= htmlspecialchars($currentUser['email'] ?? '') ?>"
                     title="Clic para ver foto ampliada">
                    <?php if ($hasRealPhoto): ?>
                        <img src="<?= $userPhotoUrl ?>" alt="Avatar" class="rounded-circle object-fit-cover border border-2 border-primary shadow-sm zoomable-image" style="width: 40px; height: 40px; cursor: pointer;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white shadow-sm zoomable-image" style="width: 40px; height: 40px; cursor: pointer;" title="Clic para ampliar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <span class="avatar-zoom-indicator"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                </div>

                <!-- Menú desplegable de cuenta -->
                <div class="dropdown" style="position: relative; z-index: 1055;">
                    <button class="btn btn-sm p-1 text-muted border-0 dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" title="Menú de cuenta">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu" style="position: absolute; z-index: 9999; min-width: 230px;">
                        <li>
                            <div class="dropdown-header py-2 text-center">
                                <div class="mb-2 d-inline-block position-relative cursor-pointer zoomable-image" 
                                     data-img-zoom="<?= $hasRealPhoto ? $userPhotoUrl : BASE_URL . '/public/img/logo.jpg' ?>" 
                                     data-img-title="<?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?>"
                                     title="Clic para ampliar foto">
                                    <?php if ($hasRealPhoto): ?>
                                        <img src="<?= $userPhotoUrl ?>" alt="Avatar" class="rounded-circle object-fit-cover border border-2 border-primary shadow-sm" style="width: 58px; height: 58px;">
                                    <?php else: ?>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white shadow-sm mx-auto" style="width: 58px; height: 58px;">
                                            <i class="fa-solid fa-user fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="position-absolute bottom-0 end-0 badge bg-info rounded-circle p-1" style="font-size: 0.6rem;"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                                </div>
                                <div class="fw-bold dropdown-user-name" style="font-size: 0.95rem;"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?></div>
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
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <div class="container-fluid px-4 py-4" style="position: relative; z-index: 1;">
        <?php 
        $flash = SessionHelper::getFlash();
        if ($flash): 
        ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
