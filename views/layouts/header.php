<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>/public/img/logo.jpg">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css?v=<?= file_exists(ROOT_PATH . '/public/css/style.css') ? filemtime(ROOT_PATH . '/public/css/style.css') : time() ?>">
    <!-- PWA Manifest & Theme Color (FluviApp v2.0) -->
    <link rel="manifest" href="<?= BASE_URL ?>/public/manifest.json">
    <meta name="theme-color" content="#0284c7">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FluviApp">

    <script>
        window.fluviappBaseUrl = '<?= BASE_URL ?>';
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= BASE_URL ?>/public/sw.js').catch(err => console.warn('SW Error:', err));
            });
        }
    </script>
</head>
<body>
<div class="d-flex" id="wrapper">
