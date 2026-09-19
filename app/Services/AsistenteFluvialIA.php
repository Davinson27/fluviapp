<?php
// =======================================================
// Servicio: Asistente Fluvial IA (FluviApp Gemini AI)
// =======================================================

class AsistenteFluvialIA {
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->model = env('GEMINI_MODEL', 'gemini-1.5-flash');
    }

    /**
     * Determina el tratamiento según el género del usuario
     * @param string $genero 'masculino', 'femenino', u 'otro'
     * @return string "Señor", "Señora" o "Estimado/a usuario/a"
     */
    public static function obtenerTratamiento(string $genero): string {
        return match (strtolower(trim($genero))) {
            'femenino'  => 'Señora',
            'masculino' => 'Señor',
            default     => 'Estimado/a usuario/a'
        };
    }

    /**
     * Genera un mensaje inteligente y amigable cuando no hay rutas para la fecha filtrada
     */
    public function generarSugerencia(
        array $usuario,
        string $fechaBuscada,
        ?array $origen,
        ?array $destino,
        array $alternativas,
        bool $existeRuta = false
    ): array {
        $genero = $usuario['genero'] ?? 'masculino';
        $nombre = $usuario['nombre'] ?? '';
        $tratamiento = self::obtenerTratamiento($genero);

        // Formatear fecha buscada en español
        $fechaBuscadaTexto = $this->formatearFechaTexto($fechaBuscada);

        // 1. Mensaje temporal principal requerido:
        // "Señor o señora dependiendo si es masculino o femenino, en este momento no hay rutas disponibles para ese día que filtró el usuario..."
        $origenTexto = $origen ? htmlspecialchars($origen['nombre']) : 'el puerto seleccionado';
        $destinoTexto = $destino ? htmlspecialchars($destino['nombre']) : 'el destino seleccionado';

        $mensajePrincipal = "{$tratamiento} {$nombre}, en este momento no hay rutas disponibles para el día {$fechaBuscadaTexto} que filtró.";

        // Preparar sugerencias estructuradas a partir de las alternativas
        $fechasUnicas = [];
        foreach ($alternativas as $alt) {
            $f = $alt['fecha_salida'];
            if (!isset($fechasUnicas[$f])) {
                $fechasUnicas[$f] = [
                    'fecha'          => $f,
                    'fecha_formato'  => $this->formatearFechaTexto($f),
                    'hora'           => substr($alt['hora_salida'], 0, 5),
                    'precio'         => (float)$alt['precio_pasaje'],
                    'cupos'          => (int)$alt['cupos_disponibles'],
                    'embarcacion'    => $alt['embarcacion_nombre'],
                    'codigo_viaje'   => $alt['codigo_viaje'],
                    'origen_nombre'  => $alt['origen_nombre'],
                    'destino_nombre' => $alt['destino_nombre'],
                    'origen_id'      => $alt['origen_id'],
                    'destino_id'     => $alt['destino_id']
                ];
            }
        }
        $sugerencias = array_values($fechasUnicas);

        // Si tenemos API Key de Gemini configurada, enriquecer con Google Gemini
        if (!empty($this->apiKey)) {
            $mensajeIA = $this->consultarGemini(
                $tratamiento,
                $nombre,
                $fechaBuscadaTexto,
                $origenTexto,
                $destinoTexto,
                $sugerencias,
                $existeRuta
            );

            if ($mensajeIA !== null) {
                return [
                    'tratamiento'       => $tratamiento,
                    'mensaje_principal' => $mensajePrincipal,
                    'mensaje_ia'        => $mensajeIA,
                    'sugerencias'       => $sugerencias,
                    'motor'             => 'gemini'
                ];
            }
        }

        // Motor Inteligente Heurístico Experto Fluvial (fallback autónomo de alta calidad)
        $mensajeIA = $this->generarMensajeHeuristico(
            $tratamiento,
            $nombre,
            $fechaBuscadaTexto,
            $origenTexto,
            $destinoTexto,
            $sugerencias,
            $existeRuta
        );

        return [
            'tratamiento'       => $tratamiento,
            'mensaje_principal' => $mensajePrincipal,
            'mensaje_ia'        => $mensajeIA,
            'sugerencias'       => $sugerencias,
            'motor'             => 'heuristico_experto'
        ];
    }

    /**
     * Motor Heurístico Experto Fluvial
     */
    private function generarMensajeHeuristico(
        string $tratamiento,
        string $nombre,
        string $fechaBuscadaTexto,
        string $origenTexto,
        string $destinoTexto,
        array $sugerencias,
        bool $existeRuta
    ): string {
        $saludo = "{$tratamiento}" . (!empty($nombre) ? " " . explode(' ', $nombre)[0] : "");

        if (!empty($sugerencias)) {
            $fechasLista = [];
            foreach (array_slice($sugerencias, 0, 3) as $s) {
                $fechasLista[] = "<strong>{$s['fecha_formato']}</strong> (salida a las {$s['hora']} con {$s['cupos']} cupos disponibles a $" . number_format($s['precio'], 0, ',', '.') . " COP)";
            }
            $textoFechas = implode(', ', array_slice($fechasLista, 0, -1));
            if (count($fechasLista) > 1) {
                $textoFechas .= ' y ' . end($fechasLista);
            } else {
                $textoFechas = $fechasLista[0];
            }

            return "¡Buenas noticias, {$saludo}! Nuestro sistema de inteligencia fluvial ha verificado que esta ruta cuenta con salidas programadas en fechas cercanas. Le sugerimos amablemente viajar el {$textoFechas}. Puede reservar su cupo directamente seleccionando la fecha que mejor se adapte a su itinerario.";
        }

        if ($existeRuta) {
            return "{$saludo}, le informamos con todo gusto que la ruta fluvial entre {$origenTexto} y {$destinoTexto} está autorizada y activa, pero las embarcaciones aún no han abierto zarpes para estos próximos días. Le sugerimos consultar nuevamente en las próximas horas o verificar otros puertos cercanos de su departamento.";
        }

        return "{$saludo}, en este momento no encontramos itinerarios fluviales programados para los filtros seleccionados. Le sugerimos amablemente verificar las salidas generales de su departamento o seleccionar 'Todos los Muelles' para descubrir las opciones de viaje disponibles hoy.";
    }

    /**
     * Consulta a la API de Google Gemini
     */
    private function consultarGemini(
        string $tratamiento,
        string $nombre,
        string $fechaBuscadaTexto,
        string $origenTexto,
        string $destinoTexto,
        array $sugerencias,
        bool $existeRuta
    ): ?string {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . urlencode($this->apiKey);

            $fechasInfo = "";
            foreach ($sugerencias as $s) {
                $fechasInfo .= "- Fecha {$s['fecha_formato']} a las {$s['hora']} con {$s['cupos']} cupos a {$s['precio']} COP en embarcación {$s['embarcacion']}\n";
            }

            $prompt = "Eres el Asistente Inteligente de FluviApp, el sistema de transporte fluvial en Colombia.
El usuario buscó una ruta fluvial entre '{$origenTexto}' y '{$destinoTexto}' para la fecha: {$fechaBuscadaTexto}, pero NO hay viajes ese día.
El tratamiento formal del usuario es: '{$tratamiento}' y su nombre es '{$nombre}'.
Las fechas alternativas donde SÍ hay viajes disponibles para esa ruta son:
{$fechasInfo}

Instrucción:
Redacta un mensaje muy amable, respetuoso, empático y profesional en español colombiano (máximo 80 palabras).
Dirígete al usuario usando obligatoriamente '{$tratamiento}' (Señor o Señora según corresponda).
Infórmale con cortesía que para ese día no hay salidas, pero sugiérele con entusiasmo y amabilidad los días específicos en los que la ruta sí está disponible para que pueda viajar.
No inventes fechas que no estén en la lista.";

            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 250
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $json = json_decode($response, true);
                $candidates = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($candidates)) {
                    return trim($candidates);
                }
            }
        } catch (Throwable $t) {
            error_log("Error al consultar Gemini API: " . $t->getMessage());
        }

        return null;
    }

    /**
     * Formatea fecha Y-m-d a lenguaje natural en español
     */
    private function formatearFechaTexto(string $fecha): string {
        if (empty($fecha)) {
            return 'la fecha seleccionada';
        }
        $timestamp = strtotime($fecha);
        if (!$timestamp) {
            return $fecha;
        }

        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $diaSemana = $dias[(int)date('w', $timestamp)];
        $dia = date('j', $timestamp);
        $mes = $meses[(int)date('n', $timestamp)];
        $ano = date('Y', $timestamp);

        return "{$diaSemana} {$dia} de {$mes} de {$ano}";
    }

    /**
     * Responde preguntas generales del usuario mediante Chatbot Flotante (v2.0)
     */
    public function responderConsultaGeneral(string $pregunta, ?array $usuario = null): string {
        $genero = $usuario['genero'] ?? 'otro';
        $nombre = $usuario['nombre'] ?? '';
        $tratamiento = self::obtenerTratamiento($genero);
        $saludo = $tratamiento . (!empty($nombre) ? " " . explode(' ', $nombre)[0] : "");

        // Si hay API key de Gemini, consultar modelo
        if (!empty($this->apiKey)) {
            $prompt = "Eres el Asistente Fluvial Virtual de FluviApp (v2.0), el sistema líder de transporte de pasajeros y encomiendas fluviales en Colombia (Río Magdalena, Cauca, Atrato, Sinú).
El usuario se llama '{$saludo}'.
Pregunta del usuario: \"{$pregunta}\"

Información de FluviApp:
- Boletos: Se compran en línea con pasarela Wompi (PSE, Nequi, Tarjetas) o en taquilla. Incluyen código QR de abordaje y selección de asiento en mapa visual.
- Carga y Encomiendas: Se cotizan por peso real o volumétrico ((L x A x H) / 5000) a aprox $2.500 COP por kg. Se entregan con firma táctil digital.
- Rastreo Fluvial: Puedes ver la lancha moviéndose en tiempo real en el mapa con Leaflet y ver el tiempo estimado de llegada (ETA).

Instrucción:
Responde de forma concisa (máximo 70 palabras), empática, clara y muy colombiana/profesional. Si preguntan por horarios o rutas, invítalos a usar el buscador del portal.";

            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . urlencode($this->apiKey);
                $payload = [
                    'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 150]
                ];
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 4);
                $res = curl_exec($ch);
                curl_close($ch);
                if ($res) {
                    $json = json_decode($res, true);
                    $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($text)) return trim($text);
                }
            } catch (Throwable $e) {}
        }

        // Motor Heurístico de Conversación Local Autónomo
        $p = strtolower(trim($pregunta));

        if (str_contains($p, 'horario') || str_contains($p, 'salida') || str_contains($p, 'cuándo') || str_contains($p, 'viaje') || str_contains($p, 'ruta')) {
            return "¡Con gusto, {$saludo}! Puedes consultar todos los horarios de salida y disponibilidad en tiempo real ingresando a nuestro Explorador de Rutas en el Portal. Allí podrás filtrar por puerto de origen, destino y fecha.";
        }

        if (str_contains($p, 'precio') || str_contains($p, 'costo') || str_contains($p, 'cuánto vale') || str_contains($p, 'tarifa') || str_contains($p, 'pasaje')) {
            return "{$saludo}, las tarifas de pasaje varían según el trayecto fluvial (generalmente entre $20.000 y $60.000 COP). Al seleccionar tu ruta en el portal, verás el precio exacto con tasa portuaria y seguro fluvial incluidos.";
        }

        if (str_contains($p, 'encomienda') || str_contains($p, 'carga') || str_contains($p, 'paquete') || str_contains($p, 'flete') || str_contains($p, 'caja')) {
            return "{$saludo}, en FluviApp transportamos tu carga de forma segura. El flete se liquida según el peso en báscula o peso volumétrico (largo x ancho x alto). Puedes solicitar el envío desde 'Enviar Encomienda' en el menú.";
        }

        if (str_contains($p, 'rastreo') || str_contains($p, 'dónde') || str_contains($p, 'gps') || str_contains($p, 'vivo') || str_contains($p, 'llegada')) {
            return "{$saludo}, en FluviApp v2.0 puedes rastrear tu embarcación en vivo sobre el mapa interactivo desde 'Mis Boletos' o 'Rastreo Fluvial' y consultar el tiempo estimado de llegada (ETA) al muelle.";
        }

        if (str_contains($p, 'pago') || str_contains($p, 'wompi') || str_contains($p, 'nequi') || str_contains($p, 'tarjeta') || str_contains($p, 'pse')) {
            return "{$saludo}, aceptamos pagos en línea 100% seguros mediante nuestra pasarela Wompi con Nequi, PSE y tarjetas, así como pagos en taquilla antes del zarpe.";
        }

        return "¡Hola, {$saludo}! Soy el Asistente Fluvial Virtual de FluviApp. Estoy aquí para ayudarte con horarios, compra de boletos con QR, cotización de encomiendas y rastreo de viajes en vivo. ¿En qué te puedo colaborar hoy?";
    }
}
