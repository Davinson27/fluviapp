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

    // =======================================================
    // Gestión de Notificaciones (Campanita)
    // =======================================================
    const btnMarcarLeidas = document.getElementById('btnMarcarTodasLeidas');
    if (btnMarcarLeidas) {
        btnMarcarLeidas.addEventListener('click', function (e) {
            e.preventDefault();
            const baseUrl = window.fluviappBaseUrl || '';
            fetch(baseUrl + '/api/notificaciones/marcar-leidas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    const badge = document.querySelector('.notif-badge');
                    if (badge) badge.remove();

                    const unreadBadge = document.getElementById('notifUnreadBadge');
                    if (unreadBadge) {
                        unreadBadge.textContent = 'Al día';
                        unreadBadge.className = 'badge bg-white-50 text-white';
                    }

                    const items = document.querySelectorAll('.notif-list-container .list-group-item');
                    items.forEach(el => {
                        el.classList.remove('bg-light', 'fw-semibold');
                    });

                    btnMarcarLeidas.style.display = 'none';
                }
            })
            .catch(err => console.error('Error al actualizar notificaciones:', err));
        });
    }

    // =======================================================
    // Modal de Soporte para Encomiendas
    // =======================================================
    document.addEventListener('click', function (e) {
        const btnSoporte = e.target.closest('.btn-soporte-trigger');
        if (btnSoporte) {
            const cargaId = btnSoporte.getAttribute('data-carga-id') || '';
            const guia = btnSoporte.getAttribute('data-guia') || 'N/A';
            const desc = btnSoporte.getAttribute('data-descripcion') || 'N/A';
            const origen = btnSoporte.getAttribute('data-origen') || '';
            const destino = btnSoporte.getAttribute('data-destino') || '';
            const estado = btnSoporte.getAttribute('data-estado') || 'registrada';

            // Actualizar modal
            const elGuia = document.getElementById('soporteModalGuia');
            const elEstado = document.getElementById('soporteModalEstado');
            const elTrayecto = document.getElementById('soporteModalTrayecto');
            const elContenido = document.getElementById('soporteModalContenido');

            if (elGuia) elGuia.textContent = guia;
            if (elEstado) {
                elEstado.textContent = estado.replace('_', ' ');
                elEstado.className = 'badge text-uppercase ' + (estado === 'entregada' ? 'bg-success' : 'bg-primary');
            }
            if (elTrayecto) elTrayecto.textContent = origen + ' → ' + destino;
            if (elContenido) elContenido.textContent = desc;

            const inputCargaId = document.getElementById('soporteFormCargaId');
            const inputGuia = document.getElementById('soporteFormGuiaNumero');
            if (inputCargaId) inputCargaId.value = cargaId;
            if (inputGuia) inputGuia.value = guia;

            // Enlace de WhatsApp directo prellenado
            const btnWa = document.getElementById('soporteBtnWhatsapp');
            if (btnWa) {
                const waText = encodeURIComponent(`Hola FluviApp Soporte, requiero asistencia con mi encomienda Guía: ${guia} (${origen} -> ${destino}). Estado actual: ${estado}.`);
                btnWa.href = `https://wa.me/573108901234?text=${waText}`;
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
