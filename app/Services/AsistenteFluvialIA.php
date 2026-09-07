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
}
