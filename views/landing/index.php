<?php
$currentUser = AuthHelper::user();
?>
<!DOCTYPE html>
<html lang="es-CO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'FluviApp - Red Nacional de Transporte Fluvial en Colombia') ?></title>
    
    <!-- Meta Tags SEO -->
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Red nacional de transporte fluvial en Colombia. Consulta rutas, horarios, tarifas y puertos en 29 departamentos y 20 ríos navegables como el Magdalena, Cauca, Atrato y Amazonas. Boletos y facturación electrónica oficial.') ?>">
    <meta name="keywords" content="transporte fluvial colombia, rutas fluviales, rio magdalena, rio cauca, rio atrato, rio meta, rio amazonas, puertos fluviales colombia, pasajes fluviales, encomiendas fluviales, lanchas rapidas, ferry fluvial, cormagdalena, dimar">
    <meta name="author" content="FluviApp S.A.S.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#070e1a">
    <link rel="canonical" href="<?= BASE_URL ?>/">
    <link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>/public/img/logo.jpg">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL ?>/">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? 'FluviApp - Red Nacional de Transporte Fluvial') ?>">
    <meta property="og:description" content="Navega por los ríos de Colombia. Consulta rutas fluviales intermunicipales en 29 departamentos con mapa interactivo, tarifas oficiales y facturación digital.">
    <meta property="og:locale" content="es_CO">
    <meta property="og:site_name" content="FluviApp Colombia">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle ?? 'FluviApp - Red Nacional de Transporte Fluvial') ?>">
    <meta name="twitter:description" content="Consulta rutas y tarifas fluviales en los ríos de Colombia con mapa interactivo y comprobantes electrónicos oficiales.">

    <!-- Schema.org JSON-LD para SEO y Motores de Búsqueda -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "@id": "<?= BASE_URL ?>/#organization",
          "name": "FluviApp S.A.S.",
          "url": "<?= BASE_URL ?>/",
          "description": "Sistema de gestión y operaciones fluviales de transporte de pasajeros y carga en Colombia.",
          "areaServed": "Colombia",
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+57-605-687-0000",
            "contactType": "customer service",
            "areaServed": "CO",
            "availableLanguage": "Spanish"
          }
        },
        {
          "@type": "WebSite",
          "@id": "<?= BASE_URL ?>/#website",
          "url": "<?= BASE_URL ?>/",
          "name": "FluviApp Colombia",
          "publisher": { "@id": "<?= BASE_URL ?>/#organization" }
        },
        {
          "@type": "Service",
          "name": "Transporte Fluvial de Pasajeros y Carga",
          "provider": { "@id": "<?= BASE_URL ?>/#organization" },
          "serviceType": "Transporte Fluvial",
          "areaServed": {
            "@type": "Country",
            "name": "Colombia"
          },
          "description": "Transporte intermunicipal en lanchas rápidas, ferries y carga fluvial conectando 29 departamentos y 84 puertos."
        }
      ]
    }
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <!-- Theme Manager (Modo Oscuro) -->
    <script src="<?= BASE_URL ?>/public/js/theme.js"></script>

    <style>
        :root {
            --nav-navy: #070e1a;
            --brand-cyan: #0ea5e9;
            --brand-blue: #0284c7;
            --water-deep: #0b1f3b;
            --emerald-accent: #10b981;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body, #f8fafc);
            color: var(--text-main, #0f172a);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        /* Navbar Glassmorphism */
        .landing-nav {
            background: rgba(7, 14, 26, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .landing-nav.scrolled {
            background: rgba(7, 14, 26, 0.98);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }
        /* Hero Section */
        .landing-hero {
            position: relative;
            background: radial-gradient(circle at 85% 15%, rgba(14, 165, 233, 0.3) 0%, transparent 60%),
                        radial-gradient(circle at 10% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 50%),
                        linear-gradient(135deg, #050b14 0%, #0b1f3b 50%, #034b79 100%);
            color: #ffffff;
            padding: 130px 0 110px 0;
            overflow: hidden;
        }
        .landing-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 48px;
            background: var(--bg-body, #f8fafc);
            clip-path: ellipse(55% 100% at 50% 100%);
            transition: background-color 0.3s ease;
        }
        /* Map Container */
        #mapa-colombia {
            height: 520px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 12px 36px -8px rgba(0, 0, 0, 0.15);
            z-index: 10;
        }
        .custom-div-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .map-popup h6 {
            color: #0369a1;
            font-weight: 700;
            margin-bottom: 4px;
        }
        /* Cards */
        .card-stat-national {
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            border: 1px solid #e0f2fe;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .card-stat-national:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -10px rgba(2, 132, 199, 0.18);
        }
        .rio-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            height: 100%;
        }
        .rio-card:hover {
            transform: translateY(-6px);
            border-color: #38bdf8;
            box-shadow: 0 20px 35px -8px rgba(3, 105, 161, 0.12);
        }
        .service-card {
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.08);
            border-color: #0284c7;
        }
        .service-icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.25rem;
        }
        .feature-pill {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 7px 18px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            color: #e0f2fe;
        }
        .timeline-step {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 2rem;
        }
        .timeline-step::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 14px;
            bottom: 0;
            width: 2px;
            background: #cbd5e1;
        }
        .timeline-step:last-child::before {
            display: none;
        }
        .timeline-dot {
            position: absolute;
            left: 0;
            top: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #0284c7;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 2px #0284c7;
        }
        .route-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.25s ease;
        }
        .route-card:hover {
            border-color: #0ea5e9;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.12);
        }
        .dept-accordion-btn:not(.collapsed) {
            background-color: #f0f9ff;
            color: #0369a1;
            font-weight: 700;
        }
        /* Animaciones */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }

        /* Armonía Sección #mapa y Landing en Modo Claro y Oscuro */
        #mapa {
            background: linear-gradient(180deg, #f8fafc 0%, #f0f9ff 100%);
            transition: background 0.3s ease, color 0.3s ease;
        }
        [data-bs-theme="dark"] #mapa {
            background: linear-gradient(180deg, #070d18 0%, #0a1526 50%, #0d1b32 100%) !important;
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] #mapa h2,
        [data-bs-theme="dark"] #mapa h5,
        [data-bs-theme="dark"] #mapa h6,
        [data-bs-theme="dark"] .text-dark {
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] #mapa .text-muted {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] #mapa .badge.bg-primary-subtle {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.25) 0%, rgba(2, 132, 199, 0.15) 100%) !important;
            color: #38bdf8 !important;
            border: 1px solid rgba(56, 189, 248, 0.35) !important;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.2);
        }
        [data-bs-theme="dark"] #mapa #explorar {
            background: linear-gradient(145deg, #0c182b 0%, #0e1e36 100%) !important;
            border: 1px solid #1e3352 !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35) !important;
        }
        [data-bs-theme="dark"] .landing-hero::after {
            background: #070d18 !important;
        }
        [data-bs-theme="dark"] .bg-white {
            background-color: #0e1b2f !important;
            color: #f1f5f9;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #13243d !important;
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .border {
            border-color: #1e3352 !important;
        }
        .map-legend-box {
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
            border-color: rgba(226, 232, 240, 0.8) !important;
            color: #0f172a;
        }
        [data-bs-theme="dark"] .map-legend-box {
            background-color: rgba(14, 27, 47, 0.9) !important;
            backdrop-filter: blur(8px);
            border-color: #1e3352 !important;
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .border-top,
        [data-bs-theme="dark"] .border-bottom,
        [data-bs-theme="dark"] .border-start,
        [data-bs-theme="dark"] .border-end {
            border-color: #1e3352 !important;
        }
    </style>
</head>
<body>

<!-- Navbar Profesional -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top landing-nav py-2" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-white" href="<?= BASE_URL ?>/">
            <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img me-2 shadow-sm" style="width: 44px; height: 44px;">
            <span>Fluvi<span class="text-info">App</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLandingContent" aria-controls="navbarLandingContent" aria-expanded="false" aria-label="Navegación">
            <i class="fa-solid fa-bars text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarLandingContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#buscador"><i class="fa-solid fa-magnifying-glass me-1"></i>Itinerarios</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#mapa"><i class="fa-solid fa-map-location-dot me-1 text-info"></i>Mapa Fluvial</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#explorar"><i class="fa-solid fa-filter me-1 text-warning"></i>Filtrar Rutas</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#rios"><i class="fa-solid fa-water me-1 text-primary"></i>Ríos de Colombia</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#puertos"><i class="fa-solid fa-anchor me-1"></i>Puertos</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white-50" href="#rastreo"><i class="fa-solid fa-box me-1"></i>Rastrear</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <!-- Botón Modo Oscuro / Claro en Landing -->
                <button type="button" class="theme-toggle-btn shadow-sm" title="Alternar Modo Oscuro / Claro" aria-label="Alternar Tema">
                    <i class="fa-solid fa-moon text-info"></i>
                </button>

                <?php if ($currentUser): ?>
                    <a href="<?= BASE_URL ?>/portal" class="btn btn-outline-info btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-user me-1"></i>Mi Portal (<?= htmlspecialchars($currentUser['nombre']) ?>)
                    </a>
                    <?php if (in_array($currentUser['rol'], ['admin', 'operador', 'taquilla', 'capitan'])): ?>
                        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary btn-sm px-3 fw-bold">
                            <i class="fa-solid fa-gauge me-1"></i>Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-danger btn-sm px-3 fw-semibold" title="Cerrar Sesión">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Cerrar Sesión
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-right-to-bracket me-1"></i>Iniciar Sesión
                    </a>
                    <a href="<?= BASE_URL ?>/registro" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                        <i class="fa-solid fa-user-plus me-1"></i>Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section Principal -->
<header class="landing-hero" id="inicio">
    <div class="container pt-4">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7 text-center text-lg-start mb-5 mb-lg-0">
                <div class="d-inline-flex align-items-center mb-3">
                    <span class="feature-pill shadow-sm">
                        <i class="fa-solid fa-shield-halved text-info me-2"></i>Red Fluvial Regulada &bull; 29 Departamentos &bull; MinTransporte & DIMAR
                    </span>
                </div>

                <h1 class="display-4 fw-extrabold text-white mb-3" style="line-height: 1.15; font-weight: 800;">
                    Conectando a Colombia por sus <span class="text-info">Ríos Navegables</span>
                </h1>

                <p class="lead text-white-50 mb-4 pe-lg-4" style="font-size: 1.15rem;">
                    La plataforma oficial de navegación fluvial para la consulta de rutas, tarifas intermunicipales, boletos en lanchas rápidas, transporte de vehículos y carga con <strong>facturación digital oficial (PDF/CUFE)</strong>.
                </p>

                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="#mapa" class="btn btn-primary btn-lg px-4 py-3 fw-bold shadow-lg">
                        <i class="fa-solid fa-map-location-dot me-2"></i>Explorar Mapa de Colombia
                    </a>
                    <a href="#explorar" class="btn btn-outline-info btn-lg px-4 py-3 fw-semibold">
                        <i class="fa-solid fa-filter me-2"></i>Filtrar por Departamento y Río
                    </a>
                </div>

                <!-- Métricas destacadas en el Hero -->
                <div class="row g-3 mt-4 pt-2 text-start">
                    <div class="col-3">
                        <div class="border-start border-2 border-info ps-3">
                            <div class="h3 fw-bold text-white mb-0"><span class="counter-value" data-target="<?= $stats['total_departamentos'] ?>"><?= $stats['total_departamentos'] ?></span></div>
                            <small class="text-white-50">Departamentos</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-start border-2 border-success ps-3">
                            <div class="h3 fw-bold text-white mb-0"><span class="counter-value" data-target="<?= $stats['total_muelles'] ?>"><?= $stats['total_muelles'] ?></span>+</div>
                            <small class="text-white-50">Puertos & Muelles</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-start border-2 border-primary ps-3">
                            <div class="h3 fw-bold text-white mb-0"><span class="counter-value" data-target="<?= $stats['total_rios'] ?>"><?= $stats['total_rios'] ?></span></div>
                            <small class="text-white-50">Ríos Monitoreados</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-start border-2 border-warning ps-3">
                            <div class="h3 fw-bold text-white mb-0"><span class="counter-value" data-target="<?= $stats['total_rutas'] ?>"><?= $stats['total_rutas'] ?></span>+</div>
                            <small class="text-white-50">Rutas Activas</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buscador Rápido de Viajes en el Hero -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 p-4 text-dark bg-white" id="buscador">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-2">
                            <i class="fa-solid fa-compass fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Consultar Itinerarios en Vivo</h5>
                            <small class="text-muted">Horarios, cupos y compra de tiquetes</small>
                        </div>
                    </div>

                    <form action="<?= BASE_URL ?>/#buscador" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-location-dot text-primary me-1"></i>Puerto de Origen
                            </label>
                            <select name="origen_id" class="form-select fs-6" required>
                                <option value="">Selecciona muelle de zarpe...</option>
                                <?php foreach ($departamentos as $d): ?>
                                    <optgroup label="<?= htmlspecialchars($d['departamento']) ?>">
                                        <?php if (!empty($muellesGrouped[$d['departamento']])): ?>
                                            <?php foreach ($muellesGrouped[$d['departamento']] as $m): ?>
                                                <option value="<?= $m['id'] ?>" <?= ($filtros['origen_id'] == $m['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['municipio']) ?> - <?= htmlspecialchars($m['rio']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-location-crosshairs text-success me-1"></i>Puerto de Destino
                            </label>
                            <select name="destino_id" class="form-select fs-6" required>
                                <option value="">Selecciona muelle de llegada...</option>
                                <?php foreach ($departamentos as $d): ?>
                                    <optgroup label="<?= htmlspecialchars($d['departamento']) ?>">
                                        <?php if (!empty($muellesGrouped[$d['departamento']])): ?>
                                            <?php foreach ($muellesGrouped[$d['departamento']] as $m): ?>
                                                <option value="<?= $m['id'] ?>" <?= ($filtros['destino_id'] == $m['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['municipio']) ?> - <?= htmlspecialchars($m['rio']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <i class="fa-solid fa-calendar me-1 text-info"></i>Fecha de Viaje
                            </label>
                            <input type="date" name="fecha" class="form-control fs-6" value="<?= htmlspecialchars($filtros['fecha']) ?>">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-search me-1"></i>Buscar Viajes Programados
                        </button>
                    </form>

                    <!-- Resultados de Viajes si se filtró -->
                    <?php if (!empty($filtros['origen_id']) || !empty($filtros['destino_id'])): ?>
                        <div class="mt-3 pt-3 border-top">
                            <div class="small fw-bold text-dark mb-2">
                                <i class="fa-solid fa-list-check me-1 text-primary"></i>Resultados encontrados: <?= count($viajesDisponibles) ?>
                            </div>
                            <?php if (empty($viajesDisponibles)): ?>
                                <div class="alert alert-light border small mb-0 py-2">
                                    No hay viajes programados para esta fecha. Consulta la sección de <a href="#explorar" class="fw-bold">Rutas Habilitadas</a> abajo para conocer tarifas y distancias.
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush small">
                                    <?php foreach ($viajesDisponibles as $v): ?>
                                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= substr($v['hora_salida'], 0, 5) ?></strong> &bull; <?= htmlspecialchars($v['embarcacion_nombre']) ?>
                                                <div class="text-muted"><?= $v['cupos_disponibles'] ?> cupos libres</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary">$<?= number_format($v['precio_pasaje'], 0, ',', '.') ?></div>
                                                <a href="<?= BASE_URL ?>/cliente/comprar?viaje_id=<?= $v['id'] ?>" class="btn btn-sm btn-success py-0 px-2 fw-bold">Comprar</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- SECCIÓN MAPA INTERACTIVO Y EXPLORADOR DE RUTAS POR DEPARTAMENTO Y RÍO -->
<section class="py-5" id="mapa">
    <div class="container py-3">
        <div class="text-center mb-4">
            <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-1 mb-2">
                Cartografía Fluvial de Colombia
            </span>
            <h2 class="display-6 fw-bold text-dark">Mapa Interactivo de Muelles y Rutas Fluviales</h2>
            <p class="text-muted max-w-700 mx-auto">
                Selecciona cualquier departamento o río para ver en el mapa los puertos conectados, rutas navegables intermunicipales, distancias y tarifas calculadas en tiempo real.
            </p>
        </div>

        <!-- Filtros Inteligentes Dinámicos -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" id="explorar">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5 col-lg-4">
                    <label for="filtro-departamento" class="form-label fw-bold small text-secondary">
                        <i class="fa-solid fa-map me-1 text-primary"></i>Departamento Fluvial (<?= count($departamentos) ?> disponibles)
                    </label>
                    <select id="filtro-departamento" class="form-select form-select-lg fs-6">
                        <option value="Todos">Todos los Departamentos (Nacional)</option>
                        <?php foreach ($departamentos as $dep): ?>
                            <option value="<?= htmlspecialchars($dep['departamento']) ?>" <?= ($filtros['departamento'] === $dep['departamento']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dep['departamento']) ?> (<?= $dep['total_muelles'] ?> puertos)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4 col-lg-4">
                    <label for="filtro-rio" class="form-label fw-bold small text-secondary">
                        <i class="fa-solid fa-water me-1 text-info"></i>Río Navegable (<?= count($riosLista) ?> arterias)
                    </label>
                    <select id="filtro-rio" class="form-select form-select-lg fs-6">
                        <option value="Todos">Todos los Ríos</option>
                        <?php foreach ($riosLista as $r): ?>
                            <option value="<?= htmlspecialchars($r['rio']) ?>" <?= ($filtros['rio'] === $r['rio']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['rio']) ?> (<?= $r['total_muelles'] ?> puertos)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-3 col-lg-4">
                    <div class="d-flex gap-2">
                        <button type="button" id="btn-buscar-mapa" class="btn btn-primary btn-lg flex-grow-1 fs-6 fw-bold shadow-sm">
                            <i class="fa-solid fa-search me-1"></i>Buscar
                        </button>
                        <button type="button" id="btn-limpiar-mapa" class="btn btn-outline-secondary btn-lg px-3 fs-6" title="Restablecer filtros" aria-label="Restablecer">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Mapa Leaflet -->
            <div class="col-12 col-lg-7">
                <div class="position-relative">
                    <div id="mapa-colombia"></div>
                    <div class="position-absolute bottom-0 start-0 m-3 p-2 rounded-3 shadow-sm small border d-none d-md-block map-legend-box" style="z-index: 500; font-size: 0.75rem;">
                        <span class="d-block mb-1"><span class="badge bg-primary me-1">&bull;</span> Puerto / Muelle Fluvial</span>
                        <span class="d-block text-muted">&mdash;&mdash; Ruta fluvial intermunicipal</span>
                    </div>
                </div>
            </div>

            <!-- Panel de Rutas Filtradas en Tiempo Real -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column" id="panel-rutas-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-route text-primary me-2"></i>Rutas Habilitadas
                        </h5>
                        <span class="badge bg-info text-dark" id="conteo-rutas-badge"><?= count($rutasFiltradas) ?> rutas</span>
                    </div>
                    <p class="text-muted small mb-3">
                        Mostrando conexiones intermunicipales con tarifas oficiales por trayecto:
                    </p>

                    <!-- Contenedor scrollable de tarjetas de rutas -->
                    <div id="resultados-rutas" class="overflow-auto pe-1 flex-grow-1" style="max-height: 440px;">
                        <?php if (empty($rutasFiltradas)): ?>
                            <div class="alert alert-info small" role="alert">
                                <i class="fa-solid fa-info-circle me-2"></i>No se encontraron rutas con los filtros seleccionados. Prueba seleccionando otro departamento o río.
                            </div>
                        <?php else: ?>
                            <?php foreach ($rutasFiltradas as $rf): ?>
                                <div class="card mb-3 shadow-sm route-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-dark mb-0">
                                                <?= htmlspecialchars($rf['origen_municipio']) ?> <i class="fa-solid fa-arrow-right text-muted mx-1"></i> <?= htmlspecialchars($rf['destino_municipio']) ?>
                                            </h6>
                                            <span class="badge bg-primary rounded-pill text-white fw-bold">
                                                $<?= number_format($rf['tarifa_base'], 0, ',', '.') ?> COP
                                            </span>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <i class="fa-solid fa-water text-info me-1"></i><?= htmlspecialchars($rf['origen_rio'] ?? 'Arteria Fluvial') ?> &bull; <?= htmlspecialchars($rf['origen_departamento']) ?> &rarr; <?= htmlspecialchars($rf['destino_departamento']) ?>
                                        </div>
                                        <div class="row text-center border-top pt-2 small">
                                            <div class="col-6 border-end text-secondary">
                                                <i class="fa-solid fa-ruler-horizontal me-1"></i><strong><?= number_format($rf['distancia_km'], 1) ?> km</strong>
                                            </div>
                                            <div class="col-6 text-secondary">
                                                <i class="fa-solid fa-clock me-1"></i><strong><?= floor($rf['duracion_estimada_min']/60) ?>h <?= ($rf['duracion_estimada_min']%60) ?>m</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN RÍOS MÁS NAVEGADOS DE COLOMBIA (INFORMACIÓN Y RELEVANCIA MUNICIPAL) -->
<section class="py-5 bg-white border-top border-bottom" id="rios">
    <div class="container py-4">
        <div class="text-center mb-5 max-w-700 mx-auto">
            <span class="badge bg-info-subtle text-info-emphasis fw-bold text-uppercase px-3 py-1 mb-2">
                Conocimiento Hidrográfico Nacional
            </span>
            <h2 class="display-6 fw-bold text-dark">Los Ríos Más Navegados de Colombia y su Importancia</h2>
            <p class="text-muted">
                Conoce las principales arterias fluviales que movilizan la economía, conectan municipios aislados de carretera y garantizan la supervivencia y el comercio en el territorio nacional.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($riosInfo as $rio): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card rio-card p-4 d-flex flex-column shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary text-uppercase px-2 py-1" style="font-size: 0.75rem;">
                                <?= htmlspecialchars($rio['cuenca']) ?>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fa-solid fa-ship me-1 text-primary"></i><?= htmlspecialchars($rio['calado_promedio_pies']) ?>
                            </span>
                        </div>

                        <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($rio['nombre']) ?></h4>

                        <!-- Métricas del río -->
                        <div class="d-flex gap-3 mb-3 p-2 bg-light rounded-3 small">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">Longitud Total</span>
                                <strong><?= number_format($rio['longitud_total_km'], 0, ',', '.') ?> km</strong>
                            </div>
                            <div class="border-start ps-3">
                                <span class="text-muted d-block" style="font-size: 0.75rem;">Navegables</span>
                                <strong class="text-success"><?= number_format($rio['longitud_navegable_km'], 0, ',', '.') ?> km</strong>
                            </div>
                        </div>

                        <!-- Importancia económica y municipal -->
                        <p class="small text-secondary mb-3 flex-grow-1" style="line-height: 1.55;">
                            <?= htmlspecialchars($rio['importancia']) ?>
                        </p>

                        <!-- Departamentos que conecta -->
                        <div class="mb-3 pt-2 border-top">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-map-location-dot me-1 text-info"></i>Departamentos que conecta:
                            </small>
                            <div class="small text-dark" style="font-size: 0.8rem;">
                                <?= htmlspecialchars($rio['departamentos_que_conecta']) ?>
                            </div>
                        </div>

                        <!-- Cargas y flota -->
                        <div class="p-2 bg-light rounded-3 small mt-auto" style="font-size: 0.78rem;">
                            <div class="mb-1"><strong>Cargas:</strong> <?= htmlspecialchars($rio['principales_cargas']) ?></div>
                            <div><strong>Flota:</strong> <?= htmlspecialchars($rio['tipo_embarcaciones']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SECCIÓN DIRECTORIO DE PUERTOS Y MUELLES POR DEPARTAMENTO -->
<section class="py-5" id="puertos">
    <div class="container py-4">
        <div class="text-center mb-5 max-w-700 mx-auto">
            <span class="badge bg-success-subtle text-success fw-bold text-uppercase px-3 py-1 mb-2">
                Infraestructura Portuaria
            </span>
            <h2 class="display-6 fw-bold text-dark">Directorio de Puertos y Muelles en 29 Departamentos</h2>
            <p class="text-muted">
                Consulta los terminales y muelles fluviales registrados con taquillas activas, coordenadas GPS y riberas de embarque en toda Colombia.
            </p>
        </div>

        <div class="accordion" id="accordionDepartamentos">
            <div class="row g-3">
                <?php 
                $countDept = 0;
                foreach ($muellesGrouped as $deptoNombre => $muellesDelDepto): 
                    $countDept++;
                    $collapseId = 'deptCollapse_' . $countDept;
                ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border rounded-4 shadow-sm h-100">
                            <div class="card-header bg-white p-3 border-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="fa-solid fa-anchor text-primary me-2"></i><?= htmlspecialchars($deptoNombre) ?>
                                    </h6>
                                    <small class="text-muted"><?= count($muellesDelDepto) ?> puertos activos</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary dept-header collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                                    Ver Puertos
                                </button>
                            </div>
                            <div id="<?= $collapseId ?>" class="collapse dept-collapse" data-bs-parent="#accordionDepartamentos">
                                <div class="card-body pt-0 px-3 pb-3 border-top">
                                    <ul class="list-unstyled small mb-0 mt-2">
                                        <?php foreach ($muellesDelDepto as $m): ?>
                                            <li class="py-2 border-bottom">
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($m['nombre']) ?></div>
                                                <div class="text-muted" style="font-size: 0.78rem;">
                                                    <i class="fa-solid fa-water text-info me-1"></i><?= htmlspecialchars($m['rio']) ?> &bull; <?= htmlspecialchars($m['municipio']) ?>
                                                </div>
                                                <?php if (!empty($m['descripcion'])): ?>
                                                    <div class="text-secondary" style="font-size: 0.75rem;"><?= htmlspecialchars($m['descripcion']) ?></div>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN CATÁLOGO DE SERVICIOS -->
<section class="py-5 bg-white border-top" id="servicios">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-1 mb-2">
                Nuestros Servicios
            </span>
            <h2 class="display-6 fw-bold text-dark">Soluciones Integrales de Transporte Fluvial</h2>
            <p class="text-muted">
                FluviApp opera con la flota más segura y moderna del país, facilitando la conectividad de pasajeros, fletes de comercio y encomiendas.
            </p>
        </div>

        <div class="row g-4">
            <!-- Servicio 1: Lanchas Rápidas -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="service-card p-4 d-flex flex-column">
                    <div class="service-icon-wrapper bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-sailboat"></i>
                    </div>
                    <span class="badge bg-primary text-uppercase align-self-start mb-2" style="font-size: 0.7rem;">Pasajeros Regular</span>
                    <h5 class="fw-bold text-dark mb-2">Lanchas Rápidas de Pasajeros</h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        Transporte expreso intermunicipal con motores fuera de borda de alto desempeño, chalecos salvavidas reglamentarios y póliza de viaje incluida.
                    </p>
                    <ul class="list-unstyled small text-muted mb-4 border-top pt-3">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Rutas fijas garantizadas</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Asignación de asiento numerado</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Factura digital instantánea</li>
                    </ul>
                    <a href="#buscador" class="btn btn-outline-primary btn-sm fw-bold mt-auto w-100">Consultar Rutas</a>
                </div>
            </div>

            <!-- Servicio 2: Ferry Fluvial -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="service-card p-4 d-flex flex-column">
                    <div class="service-icon-wrapper bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-ferry"></i>
                    </div>
                    <span class="badge bg-info text-dark text-uppercase align-self-start mb-2" style="font-size: 0.7rem;">Vehicular & Pesado</span>
                    <h5 class="fw-bold text-dark mb-2">Ferry Fluvial de Carga y Vehículos</h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        Planchones y ferris de gran capacidad para el cruce de automóviles, camionetas, camiones de víveres, ganado en pie y maquinaria agrícola.
                    </p>
                    <ul class="list-unstyled small text-muted mb-4 border-top pt-3">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Capacidad de hasta 120 toneladas</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Rampa hidráulica de atraque</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Comprobante de paso vehicular</li>
                    </ul>
                    <a href="#contacto" class="btn btn-outline-info btn-sm fw-bold mt-auto w-100">Cotizar Cruce</a>
                </div>
            </div>

            <!-- Servicio 3: FluviCarga -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="service-card p-4 d-flex flex-column">
                    <div class="service-icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <span class="badge bg-success text-uppercase align-self-start mb-2" style="font-size: 0.7rem;">Paquetería & Fletes</span>
                    <h5 class="fw-bold text-dark mb-2">FluviCarga Express & Encomiendas</h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        Envío de paquetes, repuestos, mercancía seca y productos agrícolas entre muelles con rastreo en vivo y entrega en taquilla o puerta a muelle.
                    </p>
                    <ul class="list-unstyled small text-muted mb-4 border-top pt-3">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Guía digital con código único</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Embalaje impermeable protegido</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Rastreo satelital por guía</li>
                    </ul>
                    <a href="#rastreo" class="btn btn-outline-success btn-sm fw-bold mt-auto w-100">Rastrear Encomienda</a>
                </div>
            </div>

            <!-- Servicio 4: Chárter & Ecoturismo -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="service-card p-4 d-flex flex-column">
                    <div class="service-icon-wrapper bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-camera-retro"></i>
                    </div>
                    <span class="badge bg-warning text-dark text-uppercase align-self-start mb-2" style="font-size: 0.7rem;">Turismo & Exclusivo</span>
                    <h5 class="fw-bold text-dark mb-2">Chárter Fluvial & Ecoturismo</h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        Alquiler privado de embarcaciones para expediciones ecológicas en el río Magdalena, Brazo de Mompox, avistamiento de aves y eventos corporativos.
                    </p>
                    <ul class="list-unstyled small text-muted mb-4 border-top pt-3">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Capitán y tripulación certificada</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Itinerarios a la medida</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Seguro de responsabilidad civil</li>
                    </ul>
                    <a href="#contacto" class="btn btn-outline-warning btn-sm fw-bold mt-auto w-100">Reservar Chárter</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN RASTREO DE ENCOMIENDAS -->
<section class="py-5" id="rastreo">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-12 col-lg-5 mb-4 mb-lg-0">
                <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-1 mb-2">
                    Trazabilidad en Vivo
                </span>
                <h2 class="display-6 fw-bold text-dark mb-3">Rastrea tu Encomienda Fluvial en Tiempo Real</h2>
                <p class="text-muted mb-4">
                    Ingresa el número de guía asignado al despachar tu carga para conocer al instante el estado de transporte, la embarcación asignada y la fecha estimada de arribo a muelle.
                </p>

                <form action="<?= BASE_URL ?>/#rastreo" method="GET" class="p-3 bg-white rounded-4 shadow-sm border mb-3">
                    <label for="guia_input" class="form-label fw-semibold small text-secondary">
                        <i class="fa-solid fa-barcode text-primary me-1"></i>Número de Guía o Código de Envío
                    </label>
                    <div class="input-group">
                        <input type="text" id="guia_input" name="guia" class="form-control" placeholder="Ej: GUIA-2026-0001" value="<?= htmlspecialchars($guiaBusqueda) ?>" required>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fa-solid fa-search me-1"></i>Rastrear
                        </button>
                    </div>
                </form>

                <div class="small text-muted">
                    <i class="fa-solid fa-lightbulb text-warning me-1"></i>Guía de prueba activa: 
                    <a href="<?= BASE_URL ?>/?guia=GUIA-2026-0001#rastreo" class="fw-bold text-primary text-decoration-none">GUIA-2026-0001</a>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <?php if (!empty($errorRastreo)): ?>
                        <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                            <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-warning"></i>
                            <div><?= htmlspecialchars($errorRastreo) ?></div>
                        </div>
                    <?php elseif (!empty($encomiendaRastreo)): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div>
                                <span class="badge bg-primary text-uppercase px-2 py-1">Guía Activa</span>
                                <h5 class="fw-bold text-dark mb-0 mt-1"><?= htmlspecialchars($encomiendaRastreo['guia_numero']) ?></h5>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success fs-6 text-uppercase"><?= htmlspecialchars($encomiendaRastreo['estado']) ?></span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Origen</small>
                                <strong><?= htmlspecialchars($encomiendaRastreo['origen_nombre'] ?? 'Muelle Salida') ?></strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Destino</small>
                                <strong><?= htmlspecialchars($encomiendaRastreo['destino_nombre'] ?? 'Muelle Llegada') ?></strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Peso Registrado</small>
                                <strong><?= $encomiendaRastreo['peso_kg'] ?> kg</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Flete Pagado</small>
                                <strong class="text-primary">$<?= number_format($encomiendaRastreo['valor_flete'], 0, ',', '.') ?> COP</strong>
                            </div>
                        </div>

                        <!-- Línea de Tiempo de Entrega -->
                        <div class="ps-2">
                            <div class="timeline-step">
                                <div class="timeline-dot bg-success"></div>
                                <h6 class="fw-bold text-dark mb-0">Recepción en Taquilla de Muelle</h6>
                                <small class="text-muted"><?= $encomiendaRastreo['created_at'] ?> &bull; Paquete verificado y rotulado</small>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-dot <?= in_array($encomiendaRastreo['estado'], ['cargada', 'en_transito', 'entregada']) ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <h6 class="fw-bold text-dark mb-0">Embarcado en Motonave</h6>
                                <small class="text-muted"><?= htmlspecialchars($encomiendaRastreo['embarcacion_nombre'] ?? 'Embarcación Asignada') ?></small>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-dot <?= in_array($encomiendaRastreo['estado'], ['en_transito', 'entregada']) ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <h6 class="fw-bold text-dark mb-0">En Navegación Fluvial</h6>
                                <small class="text-muted">Ruta directa sin escalas intermedias</small>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-dot <?= ($encomiendaRastreo['estado'] === 'entregada') ? 'bg-success' : 'bg-secondary' ?>"></div>
                                <h6 class="fw-bold text-dark mb-0">Arribo y Entrega en Destino</h6>
                                <small class="text-muted">Reclamo en taquilla con documento de identidad</small>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Estado inicial sin búsqueda -->
                        <div class="text-center py-5">
                            <div class="text-primary mb-3 display-4"><i class="fa-solid fa-dolly"></i></div>
                            <h5 class="fw-bold text-dark">Rastreador Satelital de FluviCarga</h5>
                            <p class="text-muted small max-w-500 mx-auto">
                                Ingresa el número de guía en el formulario de la izquierda para ver el historial y la posición fluvial de tu envío.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN FACTURACIÓN DIGITAL -->
<section class="py-5 bg-dark text-white position-relative" id="facturacion">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                <span class="badge bg-info text-dark text-uppercase px-3 py-1 mb-2">Comprobantes Oficiales</span>
                <h2 class="display-6 fw-bold text-white mb-3">Factura y Recibo Digital con Código QR en Cada Viaje</h2>
                <p class="text-white-50 mb-4">
                    Al adquirir tu tiquete fluvial o despachar una encomienda con FluviApp, obtienes de inmediato tu comprobante y factura electrónica descargable en PDF, cumpliendo con la normativa tributaria y portuaria de Colombia.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10">
                            <h6 class="fw-bold text-info mb-1"><i class="fa-solid fa-file-pdf me-2"></i>Descarga en PDF</h6>
                            <small class="text-white-50">Guarda o imprime tu factura en tamaño carta oficial para reembolsos o contabilidad.</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-10">
                            <h6 class="fw-bold text-info mb-1"><i class="fa-solid fa-qrcode me-2"></i>Validación por QR</h6>
                            <small class="text-white-50">Abordaje ágil en muelle escaneando el código único de tu boleto electrónico.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 text-center">
                <div class="p-4 bg-white text-dark rounded-4 shadow-lg text-start mx-auto" style="max-width: 440px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <span class="fw-bold text-primary"><i class="fa-solid fa-ship me-1"></i>FluviApp S.A.S.</span>
                        <span class="badge bg-success">FACTURA DIGITAL</span>
                    </div>
                    <div class="small mb-2"><strong>Pasajero:</strong> Carlos Pasajero Fluvial</div>
                    <div class="small mb-2"><strong>Ruta:</strong> Muelle Magangué &rarr; Mompox</div>
                    <div class="small mb-3"><strong>Tarifa:</strong> $25.000 COP (Exento IVA Art. 476 E.T.)</div>
                    <div class="p-2 bg-light rounded text-center mb-3 border">
                        <i class="fa-solid fa-qrcode fs-1 text-dark"></i>
                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">CUFE: 8b7d92f4...0a92e1</div>
                    </div>
                    <a href="<?= BASE_URL ?>/registro" class="btn btn-primary w-100 fw-bold">
                        Regístrate y Obtén tu Factura
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Institucional Completo -->
<footer class="bg-black text-white-50 py-5" id="contacto">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-12 col-lg-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img me-2 shadow-sm" style="width: 42px; height: 42px;">
                    <h4 class="fw-bold text-white mb-0">Fluvi<span class="text-info">App</span></h4>
                </div>
                <p class="small mb-3">
                    Red Nacional de Transporte y Gestión Fluvial de Colombia. Conectando 29 departamentos a través de 20 arterias navegables.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <h6 class="text-white fw-bold mb-3">Servicios</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#servicios" class="text-white-50 text-decoration-none">Lanchas Rápidas</a></li>
                    <li class="mb-2"><a href="#servicios" class="text-white-50 text-decoration-none">Ferry de Vehículos</a></li>
                    <li class="mb-2"><a href="#servicios" class="text-white-50 text-decoration-none">Carga & Encomiendas</a></li>
                    <li class="mb-2"><a href="#servicios" class="text-white-50 text-decoration-none">Chárter Fluvial</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <h6 class="text-white fw-bold mb-3">Atención & Taquillas</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fa-solid fa-phone me-2 text-info"></i>Línea Nacional: (605) 687-0000</li>
                    <li class="mb-2"><i class="fa-brands fa-whatsapp me-2 text-success"></i>WhatsApp: +57 300 123 4567</li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-warning"></i>contacto@fluviapp.com</li>
                    <li class="mb-2"><i class="fa-solid fa-clock me-2 text-info"></i>Lunes a Domingo: 5:00 AM - 6:00 PM</li>
                </ul>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <h6 class="text-white fw-bold mb-3">Marco Legal & Seguridad</h6>
                <p class="small mb-2">
                    Operaciones sujetas a la reglamentación de la Dirección General Marítima (DIMAR) y el Ministerio de Transporte de la República de Colombia.
                </p>
                <div class="badge bg-secondary text-uppercase px-2 py-1">Versión 2.0.0 &bull; 29 Deptos</div>
            </div>
        </div>

        <div class="border-top border-white border-opacity-10 pt-4 text-center small">
            &copy; 2026 <strong>FluviApp</strong> - Todos los derechos reservados. Diseñado para el transporte fluvial integral de Colombia.
        </div>
    </div>
</footer>

<!-- Leaflet JS & MarkerCluster -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Datos Globales de Inicialización para el Mapa -->
<script>
    window.fluviappBaseUrl = '<?= BASE_URL ?>';
    window.fluviappMuelles = <?= json_encode($muellesMapa, JSON_UNESCAPED_UNICODE) ?>;
    window.fluviappRutas = <?= json_encode($rutasMapa, JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Modal Visor de Imágenes Ampliadas (Logo y Foto de Perfil) -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" aria-labelledby="imageViewerTitle" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-white border border-secondary shadow-2xl" style="background: linear-gradient(180deg, #0b1728 0%, #060d17 100%) !important; border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-secondary py-3 px-4 d-flex justify-content-between align-items-center" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass-plus text-info fs-5"></i>
                    <h5 class="modal-title fw-bold text-white mb-0" id="imageViewerTitle">Vista Previa Ampliada</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-3 p-md-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 300px; background: rgba(0, 0, 0, 0.4);">
                <div class="position-relative d-inline-block">
                    <img id="imageViewerImg" src="" alt="Vista Previa" class="img-fluid rounded-4 shadow-lg" style="max-height: 70vh; max-width: 100%; object-fit: contain; border: 3px solid rgba(14, 165, 233, 0.4);">
                </div>
                <div id="imageViewerCaption" class="mt-3 text-secondary small fw-semibold"></div>
            </div>
            <div class="modal-footer border-secondary py-2 px-4 d-flex justify-content-between align-items-center" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <span class="small text-muted"><i class="fa-solid fa-water me-1 text-info"></i>FluviApp - Visualizador</span>
                <div class="d-flex gap-2">
                    <a id="imageViewerFullLink" href="#" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Ver Original
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Modular de la Landing Page -->
<script src="<?= BASE_URL ?>/public/js/landing.js"></script>
<script src="<?= BASE_URL ?>/public/js/app.js?v=<?= file_exists(ROOT_PATH . '/public/js/app.js') ? filemtime(ROOT_PATH . '/public/js/app.js') : time() ?>"></script>
</body>
</html>