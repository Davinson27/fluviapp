// =======================================================
// FluviApp - Script de Gestión de Modo Oscuro y UI
// =======================================================

(function () {
    // Función para obtener el tema actual (localStorage o preferencia del sistema)
    function getPreferredTheme() {
        const storedTheme = localStorage.getItem('fluviapp-theme');
        if (storedTheme) {
            return storedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    // Aplicar tema inmediatamente al elemento raíz <html> para evitar flash visual
    function setTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('fluviapp-theme', theme);
        updateToggleButtons(theme);
    }

    // Actualizar íconos y textos en botones de toggle
    function updateToggleButtons(theme) {
        const buttons = document.querySelectorAll('.theme-toggle-btn');
        buttons.forEach(btn => {
            if (theme === 'dark') {
                btn.innerHTML = '<i class="fa-solid fa-sun text-warning"></i>';
                btn.setAttribute('title', 'Cambiar a modo claro');
                btn.setAttribute('aria-label', 'Modo Claro');
            } else {
                btn.innerHTML = '<i class="fa-solid fa-moon text-info"></i>';
                btn.setAttribute('title', 'Cambiar a modo oscuro');
                btn.setAttribute('aria-label', 'Modo Oscuro');
            }
        });
    }

    // Inicializar tema temprano
    const initialTheme = getPreferredTheme();
    document.documentElement.setAttribute('data-bs-theme', initialTheme);

    // Event listeners al cargar el DOM
    document.addEventListener('DOMContentLoaded', function () {
        updateToggleButtons(initialTheme);

        // Delegación de eventos para toggles de tema
        document.addEventListener('click', function (e) {
            const toggleBtn = e.target.closest('.theme-toggle-btn');
            if (toggleBtn) {
                e.preventDefault();
                const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(nextTheme);
            }
        });

        // Escuchar cambios en preferencias del sistema operativo
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (!localStorage.getItem('fluviapp-theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    });

    // Exponer globalmente si se necesita
    window.FluviTheme = {
        getTheme: getPreferredTheme,
        setTheme: setTheme
    };
})();
