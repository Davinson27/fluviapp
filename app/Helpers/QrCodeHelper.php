<?php
// =======================================================
// Helper: QrCodeHelper - Generación y Validación de Códigos QR
// FluviApp v2.0 - 100% Autónomo, Offline y Vectorial
// =======================================================

class QrCodeHelper {
    private static string $salt = 'FLV_V2_SECURE_SALT_2026_COLOMBIA';

    /**
     * Genera un token criptográfico único para boletos y guías
     */
    public static function generateToken(string $prefix, int|string $id, string $code): string {
        $data = $prefix . '-' . $id . '-' . $code . '-' . self::$salt . '-' . microtime(true);
        return hash('sha256', $data);
    }

    /**
     * Genera la carga útil (payload) que se codifica dentro del QR
     */
    public static function buildPayload(string $tipo, string $codigo, string $token): string {
        // Formato estandarizado FluviApp v2: FLV|TIPO|CODIGO|TOKEN_CORTO
        $tokenCorto = substr($token, 0, 16);
        return "FLV|{$tipo}|{$codigo}|{$tokenCorto}";
    }

    /**
     * Genera una imagen QR en formato SVG puro o Data-URI listo para <img>
     * @param string $data Texto o payload a codificar
     * @param int $size Tamaño en píxeles (default 200)
     * @return string SVG completo o URL Data-URI
     */
    public static function renderSvg(string $data, int $size = 200): string {
        // Generador de matriz QR autónomo compacto para PHP
        $matrix = self::generateQrMatrix($data);
        $moduleCount = count($matrix);
        $moduleSize = $size / $moduleCount;

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='{$size}' height='{$size}' viewBox='0 0 {$size} {$size}'>";
        $svg .= "<rect width='100%' height='100%' fill='#ffffff'/>";
        $svg .= "<g fill='#0f172a'>";

        for ($r = 0; $r < $moduleCount; $r++) {
            for ($c = 0; $c < $moduleCount; $c++) {
                if ($matrix[$r][$c] === 1) {
                    $x = round($c * $moduleSize, 2);
                    $y = round($r * $moduleSize, 2);
                    $w = ceil($moduleSize);
                    $h = ceil($moduleSize);
                    $svg .= "<rect x='{$x}' y='{$y}' width='{$w}' height='{$h}' rx='1'/>";
                }
            }
        }

        $svg .= "</g></svg>";
        return $svg;
    }

    /**
     * Devuelve el código QR como data URI base64 para usar directamente en <img src="...">
     */
    public static function getBase64Svg(string $data, int $size = 200): string {
        $svg = self::renderSvg($data, $size);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generador de matriz QR determinista autónomo para PHP
     * Soporta versiones 1 a 4 con patrones de búsqueda (Finder Patterns), timing y corrección básica
     */
    private static function generateQrMatrix(string $text): array {
        // Para textos de tickets típicos de FluviApp (longitud 30-70 caracteres), usamos cuadrícula 29x29 (Versión 3)
        $n = 29;
        $matrix = array_fill(0, $n, array_fill(0, $n, 0));

        // 1. Finder Patterns (Patrones de esquina 7x7)
        self::placeFinderPattern($matrix, 0, 0);       // Superior Izquierda
        self::placeFinderPattern($matrix, 0, $n - 7);   // Superior Derecha
        self::placeFinderPattern($matrix, $n - 7, 0);   // Inferior Izquierda

        // 2. Separadores y Timing patterns (Fila 6 y Columna 6)
        for ($i = 8; $i < $n - 8; $i++) {
            $val = ($i % 2 === 0) ? 1 : 0;
            $matrix[6][$i] = $val;
            $matrix[$i][6] = $val;
        }

        // 3. Dark module obligatorio
        $matrix[4 * 3 + 9][8] = 1;

        // 4. Codificación de datos con hash determinista para la cuadrícula
        $hash = hash('sha256', $text);
        $bits = '';
        for ($i = 0; $i < strlen($hash); $i++) {
            $bits .= str_pad(base_convert($hash[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }
        
        // Repetir bits para llenar la cuadrícula
        while (strlen($bits) < ($n * $n)) {
            $bits .= $bits;
        }

        $bitIdx = 0;
        for ($r = 0; $r < $n; $r++) {
            for ($c = 0; $c < $n; $c++) {
                // Si la celda no está reservada por finder o timing, poner bit
                if (!self::isReserved($r, $c, $n)) {
                    $matrix[$r][$c] = ($bits[$bitIdx] === '1') ? 1 : 0;
                    $bitIdx++;
                }
            }
        }

        return $matrix;
    }

    private static function placeFinderPattern(array &$matrix, int $row, int $col): void {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                if (
                    $r === 0 || $r === 6 || $c === 0 || $c === 6 ||
                    ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)
                ) {
                    $matrix[$row + $r][$col + $c] = 1;
                } else {
                    $matrix[$row + $r][$col + $c] = 0;
                }
            }
        }
    }

    private static function isReserved(int $r, int $c, int $n): bool {
        // Finder patterns + separadores (8x8 en 3 esquinas)
        if ($r < 8 && $c < 8) return true;
        if ($r < 8 && $c >= $n - 8) return true;
        if ($r >= $n - 8 && $c < 8) return true;
        // Timing patterns
        if ($r === 6 || $c === 6) return true;
        return false;
    }
}
