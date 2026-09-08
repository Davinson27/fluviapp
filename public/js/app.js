// =======================================================
// Scripts Principales - FluviApp
// =======================================================

document.addEventListener('DOMContentLoaded', function () {
    // Las funciones de apertura y cierre del menú lateral son gestionadas
    // automáticamente por el atributo data-bs-toggle="offcanvas" de Bootstrap 5.

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
