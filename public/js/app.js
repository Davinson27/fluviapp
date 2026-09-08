// =======================================================
// Scripts Principales - FluviApp
// =======================================================

document.addEventListener('DOMContentLoaded', function () {
    // Control del Menú Lateral Desplegable (Offcanvas)
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarOffcanvas = document.getElementById('sidebarOffcanvas');

    if (menuToggle && sidebarOffcanvas) {
        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(sidebarOffcanvas);
                bsOffcanvas.toggle();
            }
        });
    }

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
