<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate a crisp, scalable vector SVG string for the given content.
     */
    public static function generateSvg(string $content, int $size = 140): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size, 1),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            return $writer->writeString($content);
        } catch (\Throwable $e) {
            Log::warning("QrCodeService SVG generation fallback: " . $e->getMessage());
            return self::fallbackSvg($content, $size);
        }
    }

    /**
     * Generate an inline Data URI suitable for `<img src="...">` in HTML and printable cards.
     */
    public static function generateDataUri(string $content, int $size = 140): string
    {
        $svg = self::generateSvg($content, $size);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Fallback minimal inline SVG if BaconQrCode is unavailable.
     */
    protected static function fallbackSvg(string $content, int $size): string
    {
        $encoded = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">
            <rect width="100%" height="100%" fill="#ffffff" stroke="#cbd5e1"/>
            <text x="50%" y="45%" text-anchor="middle" font-size="10" font-family="monospace" fill="#0f172a">QR SCANNER</text>
            <text x="50%" y="60%" text-anchor="middle" font-size="8" font-family="monospace" fill="#64748b">' . substr($encoded, 0, 20) . '...</text>
        </svg>';
    }
}
