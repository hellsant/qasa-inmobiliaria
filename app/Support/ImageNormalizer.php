<?php

namespace App\Support;

class ImageNormalizer
{
    /**
     * Normaliza una imagen subida: recorte centrado a la proporción dada,
     * redimensionado a un ancho máximo y re-guardado como JPEG.
     * Devuelve la ruta relativa final (puede cambiar la extensión a .jpg).
     */
    public static function normalizeStored(string $relative, float $ratio = 4 / 3, int $maxWidth = 1600, int $quality = 85): string
    {
        if (!function_exists('imagecreatefromjpeg')) {
            return $relative; // GD no disponible: se deja como está
        }

        $full = storage_path('app/public/' . $relative);
        if (!is_file($full)) {
            return $relative;
        }

        $info = @getimagesize($full);
        if (!$info) {
            return $relative;
        }
        [$w, $h, $type] = $info;
        if ($w <= 0 || $h <= 0) {
            return $relative;
        }

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($full),
            IMAGETYPE_PNG  => @imagecreatefrompng($full),
            IMAGETYPE_WEBP => @imagecreatefromwebp($full),
            IMAGETYPE_BMP  => @imagecreatefrombmp($full),
            default        => null,
        };
        if (!$src) {
            return $relative;
        }

        /* 1) Recorte CENTRADO a la proporción objetivo (nada de estirar) */
        $srcRatio = $w / $h;
        if (abs($srcRatio - $ratio) > 0.01) {
            if ($srcRatio > $ratio) {
                $newW = (int) round($h * $ratio);
                $cropped = imagecrop($src, ['x' => intdiv($w - $newW, 2), 'y' => 0, 'width' => $newW, 'height' => $h]);
            } else {
                $newH = (int) round($w / $ratio);
                $cropped = imagecrop($src, ['x' => 0, 'y' => intdiv($h - $newH, 2), 'width' => $w, 'height' => $newH]);
            }
            if ($cropped) {
                imagedestroy($src);
                $src = $cropped;
            }
        }

        /* 2) Reducir si es gigante */
        $cw = imagesx($src);
        if ($cw > $maxWidth) {
            $scaled = imagescale($src, $maxWidth, (int) round(imagesy($src) * $maxWidth / $cw));
            if ($scaled) {
                imagedestroy($src);
                $src = $scaled;
            }
        }

        /* 3) Fondo blanco (por PNG/WebP con transparencia) y salida JPEG */
        $final = imagecreatetruecolor(imagesx($src), imagesy($src));
        imagefill($final, 0, 0, imagecolorallocate($final, 255, 255, 255));
        imagecopy($final, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));

        $ext = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        $newRelative = in_array($ext, ['jpg', 'jpeg']) ? $relative : preg_replace('/\.\w+$/', '.jpg', $relative);
        $out = storage_path('app/public/' . $newRelative);

        $ok = imagejpeg($final, $out, $quality);
        imagedestroy($src);
        imagedestroy($final);

        if ($ok && $out !== $full) {
            @unlink($full);
        }

        return $ok ? $newRelative : $relative;
    }
}