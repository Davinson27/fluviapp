document.addEventListener('DOMContentLoaded', () => {
    // Variables globales y de configuración
    const baseUrl = window.fluviappBaseUrl || '';
    const muelles = window.fluviappMuelles || [];
    let rutas = window.fluviappRutas || [];
    
    let map;
    let markersLayer;
    let routesLayer;
    
    // 1. Inicialización del Mapa (Leaflet)
    const initMap = () => {
        const mapElement = document.getElementById('mapa-colombia');
        if (!mapElement) return;

        // Centrar en Colombia
        map = L.map('mapa-colombia').setView([4.5, -73.0], 6);

        // Capa de mosaicos adaptativa (clara y oscura)
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const lightTileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        const darkTileUrl = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
        
        let currentTileLayer = L.tileLayer(isDark ? darkTileUrl : lightTileUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Escuchar cambios de tema para actualizar el mapa si cambia
        const observer = new MutationObserver(() => {
            const darkNow = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            map.removeLayer(currentTileLayer);
            currentTileLayer = L.tileLayer(darkNow ? darkTileUrl : lightTileUrl, {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });

        // Capas para marcadores y rutas
        if (typeof L.markerClusterGroup === 'function') {
            markersLayer = L.markerClusterGroup();
        } else {
            markersLayer = L.layerGroup();
        }
        
        routesLayer = L.layerGroup().addTo(map);
        map.addLayer(markersLayer);

        renderMarkers(muelles);
        renderRoutes(rutas);
    };

    // Renderizar marcadores
    const renderMarkers = (datosMuelles) => {
        markersLayer.clearLayers();
        
        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#0d6efd; width:12px; height:12px; border-radius:50%; border:2px solid white;'></div>",
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        const bounds = [];

        datosMuelles.forEach(muelle => {
            if (muelle.latitud && muelle.longitud) {
                const marker = L.marker([muelle.latitud, muelle.longitud], { icon: customIcon });
                const popupContent = `
                    <div class="map-popup">
                        <h6 class="mb-1">${muelle.nombre}</h6>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-water"></i> ${muelle.rio || 'N/A'}<br>
                            <i class="fas fa-map-marker-alt"></i> ${muelle.municipio}, ${muelle.departamento}
                        </p>
                    </div>
                `;
                marker.bindPopup(popupContent);
                markersLayer.addLayer(marker);
                bounds.push([muelle.latitud, muelle.longitud]);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        } else {
            map.setView([4.5, -73.0], 6);
        }
    };

    // Renderizar rutas (polilíneas)
    const renderRoutes = (datosRutas) => {
        routesLayer.clearLayers();
        
        datosRutas.forEach(ruta => {
            if (ruta.origen_lat && ruta.origen_lng && ruta.destino_lat && ruta.destino_lng) {
                const latlngs = [
                    [ruta.origen_lat, ruta.origen_lng],
                    [ruta.destino_lat, ruta.destino_lng]
                ];
                
                const polyline = L.polyline(latlngs, {
                    color: '#0d6efd',
                    weight: 2,
                    opacity: 0.4,
                    dashArray: '5, 10'
                });
                
                routesLayer.addLayer(polyline);
            }
        });
    };

    // 2. Filtros de Departamento y Río
    const filtroDepartamento = document.getElementById('filtro-departamento');
    const filtroRio = document.getElementById('filtro-rio');
    const btnBuscarMapa = document.getElementById('btn-buscar-mapa');
    const btnLimpiarMapa = document.getElementById('btn-limpiar-mapa');

    const updateFilters = async () => {
        const depto = filtroDepartamento ? filtroDepartamento.value : '';
        const rio = filtroRio ? filtroRio.value : '';

        // Consultar muelles actualizados desde la API para reflejar muelles recién creados
        let muellesFiltrados = muelles;
        try {
            let mParams = [];
            if (depto && depto !== 'Todos') mParams.push(`departamento=${encodeURIComponent(depto)}`);
            if (rio && rio !== 'Todos') mParams.push(`rio=${encodeURIComponent(rio)}`);
            const mQueryStr = mParams.length > 0 ? '?' + mParams.join('&') : '';
            
            const respMuelles = await fetch(`${baseUrl}/api/muelles${mQueryStr}`);
            if (respMuelles.ok) {
                const jsonM = await respMuelles.json();
                muellesFiltrados = jsonM.data || [];
            }
        } catch (errM) {
            // Fallback a array local si no hay conexión
            if (depto && depto !== 'Todos') {
                muellesFiltrados = muellesFiltrados.filter(m => m.departamento === depto);
            }
            if (rio && rio !== 'Todos') {
                muellesFiltrados = muellesFiltrados.filter(m => m.rio === rio);
            }
        }

        renderMarkers(muellesFiltrados);

        // Consultar rutas filtradas
        try {
            let queryParams = [];
            if (depto && depto !== 'Todos') queryParams.push(`departamento=${encodeURIComponent(depto)}`);
            if (rio && rio !== 'Todos') queryParams.push(`rio=${encodeURIComponent(rio)}`);
            
            let queryStr = queryParams.length > 0 ? '?' + queryParams.join('&') : '';
            
            const response = await fetch(`${baseUrl}/api/rutas${queryStr}`);
            if (response.ok) {
                const resJson = await response.json();
                rutas = Array.isArray(resJson) ? resJson : (resJson.data || []);
                renderRoutes(rutas);
                renderRutaCards(rutas);
                const badge = document.getElementById('conteo-rutas-badge');
                if (badge) badge.innerText = `${rutas.length} rutas`;
            } else {
                console.error('Error en la respuesta de la API de rutas');
            }
        } catch (error) {
            console.error('Error al obtener rutas:', error);
        }
    };

    if (filtroDepartamento) {
        filtroDepartamento.addEventListener('change', updateFilters);
    }
    
    if (filtroRio) {
        filtroRio.addEventListener('change', updateFilters);
    }

    if (btnBuscarMapa) {
        btnBuscarMapa.addEventListener('click', updateFilters);
    }

    if (btnLimpiarMapa) {
        btnLimpiarMapa.addEventListener('click', () => {
            if (filtroDepartamento) filtroDepartamento.value = 'Todos';
            if (filtroRio) filtroRio.value = 'Todos';
            updateFilters();
        });
    }

    // Funciones de formateo
    const formatMoney = (amount) => {
        return '$' + parseFloat(amount).toLocaleString('es-CO') + ' COP';
    };

    const formatDuration = (minutes) => {
        if (!minutes) return 'N/A';
        const h = Math.floor(minutes / 60);
        const m = minutes % 60;
        if (h > 0 && m > 0) return `${h}h ${m}m`;
        if (h > 0) return `${h}h`;
        return `${m}m`;
    };

    // 3. Actualizar Panel de Resultados
    const renderRutaCards = (datosRutas) => {
        const contenedor = document.getElementById('resultados-rutas');
        if (!contenedor) return;

        contenedor.innerHTML = '';

        if (datosRutas.length === 0) {
            contenedor.innerHTML = `
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i> No se encontraron rutas para los filtros seleccionados.
                </div>
            `;
            return;
        }

        let html = '';
        datosRutas.forEach(ruta => {
            html += `
                <div class="card mb-3 shadow-sm route-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">
                                ${ruta.origen_nombre} <i class="fas fa-arrow-right text-muted mx-2"></i> ${ruta.destino_nombre}
                            </h5>
                            <span class="badge bg-primary rounded-pill text-white fs-6">
                                ${formatMoney(ruta.tarifa_base)}
                            </span>
                        </div>
                        <p class="text-muted small mb-3"><i class="fas fa-water text-info me-1"></i> Río: ${ruta.origen_rio || ruta.destino_rio || ruta.rio || 'Arteria Fluvial'} ${ruta.origen_departamento ? ' &bull; ' + ruta.origen_departamento : ''}</p>
                        
                        <div class="row text-center border-top pt-3">
                            <div class="col-6 border-end">
                                <span class="d-block text-muted small"><i class="fas fa-ruler-horizontal"></i> Distancia</span>
                                <strong>${ruta.distancia_km || 'N/A'} km</strong>
                            </div>
                            <div class="col-6">
                                <span class="d-block text-muted small"><i class="fas fa-clock"></i> Tiempo Est.</span>
                                <strong>${formatDuration(ruta.duracion_estimada_min)}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        contenedor.innerHTML = html;
    };

    // 4. Contadores Animados
    const easeOutQuart = (x) => 1 - Math.pow(1 - x, 4);

    const counters = document.querySelectorAll('.counter-value');
    if (counters.length > 0) {
        const animateCounter = (el) => {
            const target = parseInt(el.getAttribute('data-target'), 10);
            const duration = 2000;
            let start = null;
            
            const step = (timestamp) => {
                if (!start) start = timestamp;
                const progress = timestamp - start;
                const percentage = Math.min(progress / duration, 1);
                const easedPercentage = easeOutQuart(percentage);
                
                const currentVal = Math.floor(target * easedPercentage);
                el.innerText = currentVal.toLocaleString('es-CO');
                
                if (progress < duration) {
                    window.requestAnimationFrame(step);
                } else {
                    el.innerText = target.toLocaleString('es-CO');
                }
            };
            window.requestAnimationFrame(step);
        };

        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    // 5. Efecto Scroll Navbar
    const navbar = document.querySelector('.landing-nav');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 6. Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const offset = 72; // Altura del navbar fijo
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = targetElement.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // 7. Animaciones de Scroll (Fade-in)
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    if (animateElements.length > 0) {
        const scrollObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animateElements.forEach(el => {
            scrollObserver.observe(el);
        });
    }

    // 8. Accordion para Directorio de Departamentos
    const deptHeaders = document.querySelectorAll('.dept-header');
    deptHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const targetId = header.getAttribute('data-target');
            const targetContent = document.querySelector(targetId);
            
            // Cerrar otros abiertos (comportamiento accordion)
            document.querySelectorAll('.dept-collapse').forEach(collapse => {
                if (collapse !== targetContent && collapse.classList.contains('show')) {
                    collapse.classList.remove('show');
                }
            });

            // Toggle el clickeado
            if (targetContent) {
                targetContent.classList.toggle('show');
            }
        });
    });

    // Inicializar mapa si existe Leaflet
    if (typeof L !== 'undefined') {
        initMap();
    } else {
        console.warn('Leaflet no está cargado.');
    }
    
    // Renderizado inicial de tarjetas si hay rutas
    renderRutaCards(rutas);
});
