// =======================================================
// Scripts Principales - FluviApp
// =======================================================

document.addEventListener('DOMContentLoaded', function () {
    // Las funciones de apertura y cierre del menú lateral son gestionadas
    // automáticamente por el atributo data-bs-toggle="offcanvas" de Bootstrap 5.

    // =======================================================
    // Visor de Imágenes Ampliadas (Logo y Foto de Perfil)
    // =======================================================
    window.openImageViewer = function (src, title, caption) {
        if (!src || src === '#' || src.endsWith('/#')) return;

        const modalEl = document.getElementById('imageViewerModal');
        const imgEl = document.getElementById('imageViewerImg');
        const titleEl = document.getElementById('imageViewerTitle');
        const captionEl = document.getElementById('imageViewerCaption');

        if (!modalEl || !imgEl) return;

        imgEl.src = src;
        if (titleEl) titleEl.textContent = title || 'Visualización Ampliada';
        if (captionEl) captionEl.textContent = caption || '';

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }
    };

    // Delegación global para ampliar imágenes con data-img-zoom o clases
    document.addEventListener('click', function (e) {
        const target = e.target.closest('[data-img-zoom], .zoomable-image, .brand-logo-img, .brand-logo-trigger, .user-avatar-zoom-container');
        if (target) {
            // No abrir si se hace clic en botones de cierre o controles de modal
            if (e.target.closest('.btn-close') || e.target.closest('[data-bs-dismiss]')) return;

            let src = target.getAttribute('data-img-zoom');
            if (!src) {
                if (target.tagName === 'IMG') {
                    src = target.src;
                } else {
                    const img = target.querySelector('img');
                    if (img) src = img.src;
                }
            }

            if (src && !src.endsWith('#') && !src.endsWith('/#')) {
                e.preventDefault();
                e.stopPropagation();

                const isLogo = target.classList.contains('brand-logo-img') || 
                               target.classList.contains('brand-logo-trigger') ||
                               src.includes('logo.jpg');
                const defaultTitle = isLogo ? 'Logo Oficial - FluviApp' : 'Foto de Perfil de Usuario';
                const defaultCaption = isLogo ? 'Emblema y marca oficial del transporte fluvial de Colombia' : 'Fotografía de perfil en FluviApp';

                const title = target.getAttribute('data-img-title') || defaultTitle;
                const caption = target.getAttribute('data-img-caption') || defaultCaption;

                window.openImageViewer(src, title, caption);
            }
        }
    });

    // Auto-cerrar alertas después de 6 segundos
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                try {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                } catch (e) {
                    // Alert already closed
                }
            }
        }, 6000);
    });
});
