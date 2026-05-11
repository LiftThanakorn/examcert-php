<?php
// lib/tcpdf_helper.php
define('TCPDF_PATH', __DIR__ . '/tcpdf/');

if (!class_exists('TCPDF')) {
    require_once TCPDF_PATH . 'tcpdf.php';
}

class ExamCertPDF extends TCPDF {
    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4') {
        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);
        $this->SetCreator('ExamCert System');
        $this->SetAuthor('ExamCert');
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(false, 0);
    }

    public function useThaiFont($size = 16, $style = '')
    {
        $fontname = 'thsarabunnew';
        $style = strtoupper($style);
        if (strpos($style, 'B') !== false && strpos($style, 'I') !== false) {
            $fontname = 'thsarabunnewbi';
        } elseif (strpos($style, 'B') !== false) {
            $fontname = 'thsarabunnewb';
        } elseif (strpos($style, 'I') !== false) {
            $fontname = 'thsarabunnewi';
        }
        
        try {
            $this->SetFont($fontname, $style, $size);
        } catch (Exception $e) {
            // Fallback to freeserif if something goes wrong
            $this->SetFont('freeserif', $style, $size);
        }
    }
}

// ── Render Helpers ───────────────────────────────────────────────

function renderCertificateBackground(TCPDF $pdf, array $data, string $orient): void {
    $w = $orient === 'L' ? 297 : 210;
    $h = $orient === 'L' ? 210 : 297;

    if (($data['bg_type'] ?? 'color') === 'image' && !empty($data['bg_image'])) {
        $bgPath = ROOT_PATH . '/' . $data['bg_image'];
        if (file_exists($bgPath)) {
            // Implementation of "Background Size: Cover" logic for TCPDF
            list($imgW, $imgH) = getimagesize($bgPath);
            $imgRatio = $imgW / $imgH;
            $pageRatio = $w / $h;

            if ($imgRatio > $pageRatio) {
                // Image is wider than page ratio - match height and crop sides
                $renderH = $h;
                $renderW = $h * $imgRatio;
                $offsetX = ($w - $renderW) / 2;
                $offsetY = 0;
            } else {
                // Image is taller than page ratio - match width and crop top/bottom
                $renderW = $w;
                $renderH = $w / $imgRatio;
                $offsetX = 0;
                $offsetY = ($h - $renderH) / 2;
            }

            $pdf->Image($bgPath, $offsetX, $offsetY, $renderW, $renderH, '', '', '', false, 300, '', false, false, 0, false);
        }
    } else {
        $bgHex = ltrim($data['bg_color'] ?? '#FFFFFF', '#');
        if (strlen($bgHex) === 6) {
            $r = hexdec(substr($bgHex,0,2)); $g = hexdec(substr($bgHex,2,2)); $b = hexdec(substr($bgHex,4,2));
            $pdf->SetFillColor($r, $g, $b);
            $pdf->Rect(0, 0, $w, $h, 'F');
        }
    }
}

function renderCertificateElements(TCPDF $pdf, array $elements, array $data): void {
    foreach ($elements as $el) {
        $x = (float)($el['x'] ?? 0);
        $y = (float)($el['y'] ?? 0);
        $w = (float)($el['w'] ?? 10);
        $h = (float)($el['h'] ?? 5);
        $s = $el['style'] ?? [];

        if (($el['anchor'] ?? 'topleft') === 'center') $x -= $w / 2;

        switch ($el['type'] ?? '') {
            case 'text':
                $content = resolveVars($el['content'] ?? '', $data);
                $size    = (float)($s['size'] ?? 16);
                if ($size > 300) $size = 300;
                $bold    = !empty($s['bold']);
                $italic  = !empty($s['italic']);
                $color   = ltrim($s['color'] ?? '#000000', '#');
                $align   = $s['align'] ?? 'C';
                $style   = ($bold?'B':'') . ($italic?'I':'');

                if ($pdf instanceof ExamCertPDF) {
                    $pdf->useThaiFont($size, $style);
                } else {
                    $pdf->SetFont('thsarabunnew', $style, $size);
                }

                if (strlen($color) === 6) {
                    $r = hexdec(substr($color,0,2)); $g = hexdec(substr($color,2,2)); $b = hexdec(substr($color,4,2));
                    $pdf->SetTextColor($r, $g, $b);
                }
                
                $pdf->SetXY($x, $y);
                // Set maxh to 0 (no limit) if text is too big, or use a very large number
                // and allow the text to be visible even if it exceeds the box height slightly
                $pdf->MultiCell($w, 0, $content, 0, $align, false, 0, $x, $y, true, 0, false, true, 0, 'M');
                break;

            case 'image':
                $imgPath = ROOT_PATH . '/' . ($el['content'] ?? '');
                if ($el['content'] && file_exists($imgPath)) {
                    $pdf->Image($imgPath, $x, $y, $w, $h > 0 ? $h : 0);
                }
                break;

            case 'qrcode':
                $qrContent = resolveVars($el['content'] ?? BASE_URL, $data);
                $style2D   = ['border'=>false,'padding'=>0,'fgcolor'=>[0,0,0],'bgcolor'=>false];
                $pdf->write2DBarcode($qrContent, 'QRCODE,H', $x, $y, $w, $h, $style2D, 'N');
                break;

            case 'line':
                $lineW = (float)($s['lineWidth'] ?? 0.3);
                $color = ltrim($s['color'] ?? '#999999', '#');
                if (strlen($color) === 6) {
                    $r = hexdec(substr($color,0,2)); $g = hexdec(substr($color,2,2)); $b = hexdec(substr($color,4,2));
                    $pdf->SetDrawColor($r, $g, $b);
                }
                $pdf->SetLineWidth($lineW);
                $pdf->Line($x, $y, $x + $w, $y);
                break;
        }
    }
}
