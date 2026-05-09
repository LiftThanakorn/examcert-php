<?php
// views/certificates/render.php
//Standalone page for high-precision certificate rendering

// Debugging (Remove in production)
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

if (!isset($certificate) || !isset($template)) {
    die("Error: ข้อมูลเกียรติบัตรหรือเทมเพลตไม่ถูกต้อง");
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเกียรติบัตร - <?= e($certificate['cert_number']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Sarabun', sans-serif; 
            background: #f0f0f0; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            min-height: 100vh;
        }
        
        #cert-container {
            width: <?= ($template['orientation'] ?? 'L') === 'L' ? '297mm' : '210mm' ?>;
            height: <?= ($template['orientation'] ?? 'L') === 'L' ? '210mm' : '297mm' ?>;
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .bg-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: fill;
            z-index: 1;
        }

        .content-layer {
            position: absolute;
            inset: 0;
            z-index: 10;
        }

        .field {
            position: absolute;
            white-space: nowrap;
            line-height: 1.1;
        }

        @media print {
            body { background: white; padding: 0; }
            #cert-container { box-shadow: none; }
        }
    </style>
</head>
<body>

    <div id="cert-container">
        <?php if (!empty($template['bg_image'])): ?>
            <img src="<?= e(BASE_URL . '/' . $template['bg_image']) ?>" class="bg-layer" crossorigin="anonymous">
        <?php endif; ?>

        <div class="content-layer">
            <?php 
            $layout = json_decode((string) ($template['layout_json'] ?? ''), true) ?: [];
            foreach ($layout as $field => $cfg): 
                $text = ''; $show = true;
                if ($field === 'name') { 
                    $text = ($certificate['title'] ? $certificate['title'] : '') . $certificate['first_name'] . ' ' . $certificate['last_name']; 
                    $show = !empty($template['show_name']); 
                }
                elseif ($field === 'course') { $text = $certificate['project_name']; $show = !empty($template['show_course']); }
                elseif ($field === 'date') { $text = date('d/m/Y', strtotime($certificate['issued_date'])); $show = !empty($template['show_date']); }
                elseif ($field === 'certno') { $text = $certificate['cert_number']; $show = !empty($template['show_certno']); }
                
                if (!$text || !$show) continue;

                $style = "left: " . ($cfg['x'] ?? 0) . "mm; ";
                $style .= "top: " . ($cfg['y'] ?? 0) . "mm; ";
                $style .= "font-size: " . ($cfg['size'] ?? 20) . "pt; ";
                $style .= "color: " . ($template['color_primary'] ?? '#E87722') . "; ";
                if (!empty($cfg['bold'])) $style .= "font-weight: bold; ";
                
                $align = $cfg['align'] ?? 'C';
                if ($align === 'C') $style .= "transform: translate(-50%, -50%); text-align: center;";
                elseif ($align === 'R') $style .= "transform: translate(-100%, -50%); text-align: right;";
                else $style .= "transform: translate(0, -50%);";
            ?>
                <div class="field" style="<?= $style ?>"><?= e($text) ?></div>
            <?php endforeach; ?>

            <?php if (!empty($template['show_qr'])): ?>
                <div id="qrcode" style="position: absolute; left: <?= $layout['qrcode']['x'] ?? 240 ?>mm; top: <?= $layout['qrcode']['y'] ?? 140 ?>mm; transform: translate(-50%, -50%); width: <?= $layout['qrcode']['w'] ?? 28 ?>mm; height: <?= $layout['qrcode']['w'] ?? 28 ?>mm; background: white; padding: 1.5mm; display: flex; align-items: center; justify-content: center;"></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- UI Overlay for direct access (Will be hidden in PDF) -->
    <div id="ui-overlay" style="position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: white; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; font-weight: bold;">พิมพ์</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        const qrBox = document.getElementById('qrcode');
        if (qrBox) {
            new QRCode(qrBox, {
                text: "<?= e(BASE_URL . '/verify/' . $certificate['verify_token']) ?>",
                width: 120,
                height: 120,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        const isDownload = window.location.search.includes('download=1');
        
        function generatePDF() {
            const element = document.getElementById('cert-container');
            const opt = {
                margin: 0,
                filename: '<?= e($certificate['cert_number']) ?>.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 3, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: '<?= ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait' ?>' }
            };
            
            html2pdf().set(opt).from(element).save().then(() => {
                if (window.name === 'cert_iframe') {
                    window.parent.postMessage('download_complete', '*');
                }
            }).catch(err => {
                console.error("PDF Error:", err);
                if (window.name === 'cert_iframe') {
                    window.parent.postMessage('download_complete', '*');
                }
            });
        }

        if (isDownload) {
            // Wait for images and fonts
            window.onload = () => {
                setTimeout(generatePDF, 500); 
            };
        }
    </script>
    <style>
        @media print { .no-print { display: none !important; } }
    </style>
</body>
</html>
