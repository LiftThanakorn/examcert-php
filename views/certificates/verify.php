
<div class="h-screen w-full overflow-hidden flex flex-col items-center justify-center p-4 md:p-8 bg-mesh">
    <!-- Header (No-Scroll Version) -->
    <header class="w-full max-w-4xl flex items-center justify-between mb-8 fade-up no-print">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white rounded-2xl shadow-soft flex items-center justify-center text-primary-500">
                <i class="fas fa-certificate text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">ExamCert</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none">Verification System</p>
            </div>
        </div>
        
        <?php if ($certificate && (int) $certificate['is_revoked'] === 0): ?>
            <button id="btn-download" onclick="downloadPDF()" class="group flex items-center gap-3 px-6 py-3 bg-gray-900 text-white rounded-2xl hover:bg-black transition-all hover:-translate-y-1 shadow-xl active:scale-95 cursor-pointer border-none">
                <i class="fas fa-download text-sm group-hover:bounce"></i>
                <span class="text-xs font-bold uppercase tracking-widest">Download PDF</span>
            </button>
        <?php endif; ?>
    </header>

    <!-- Main Container (Fits within screen) -->
    <main class="w-full max-w-4xl grid md:grid-cols-5 gap-8 items-center flex-1 min-h-0">
        <!-- Certificate Card (Visual Preview) -->
        <div class="md:col-span-3 h-full flex flex-col justify-center min-h-0">
            <div id="certificate-visual" class="glass-card rounded-[2.5rem] overflow-hidden shadow-premium relative aspect-[1.414/1] w-full border border-white/50 fade-up">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-transparent"></div>
                
                <?php if (!$certificate): ?>
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-12 text-center">
                        <div class="w-20 h-20 bg-red-50 text-red-400 rounded-3xl flex items-center justify-center mb-6">
                            <i class="fas fa-search text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 mb-2">ไม่พบเกียรติบัตร</h2>
                        <p class="text-sm text-gray-400">กรุณาตรวจสอบความถูกต้องของลิงก์หรือ Token</p>
                    </div>
                <?php elseif ((int) $certificate['is_revoked'] === 1): ?>
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-12 text-center">
                        <div class="w-20 h-20 bg-orange-50 text-orange-400 rounded-3xl flex items-center justify-center mb-6">
                            <i class="fas fa-ban text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 mb-2">ใบเซอร์ถูกยกเลิก</h2>
                        <p class="text-sm text-gray-400">ใบเกียรติบัตรฉบับนี้ไม่สามารถนำมาใช้อ้างอิงได้</p>
                    </div>
                <?php else: ?>
                    <!-- Minimalistic Web Preview -->
                    <div class="absolute inset-0 flex flex-col p-12 justify-between">
                        <div class="flex justify-between items-start">
                            <div class="w-16 h-16 bg-white/50 backdrop-blur rounded-2xl flex items-center justify-center shadow-sm">
                                <i class="fas fa-award text-2xl text-primary-500"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Certificate ID</p>
                                <p class="text-sm font-bold text-gray-900"><?= e($certificate['cert_number']) ?></p>
                            </div>
                        </div>
                        
                        <div class="text-center space-y-4">
                            <p class="text-[10px] font-black text-primary-500 uppercase tracking-[0.3em]">Verified Achievement</p>
                            <h2 class="text-4xl font-black text-gray-900 leading-tight">
                                <?= e(($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']) ?>
                            </h2>
                            <p class="text-sm text-gray-500 font-medium max-w-sm mx-auto">
                                ได้ผ่านการทดสอบออนไลน์ในโครงการ <span class="text-gray-900 font-bold"><?= e($certificate['project_name']) ?></span>
                            </p>
                        </div>

                        <div class="flex justify-between items-end">
                            <div class="text-left">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Issued Date</p>
                                <p class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($certificate['issued_date'])) ?></p>
                            </div>
                            <div class="w-20 h-20 bg-white rounded-2xl p-2 shadow-sm border border-gray-50 flex items-center justify-center">
                                <i class="fas fa-qrcode text-3xl text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="md:col-span-2 space-y-6 fade-up-2 no-print h-full flex flex-col justify-center min-h-0">
            <div class="bg-white/50 backdrop-blur-xl border border-white rounded-3xl p-8 space-y-8 shadow-soft flex-1 overflow-y-auto custom-scrollbar">
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Verification Details</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100/50">
                            <span class="text-xs font-medium text-gray-500">Status</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100/50">
                            <span class="text-xs font-medium text-gray-500">Score</span>
                            <span class="text-sm font-black text-gray-900"><?= e((string) $certificate['percent']) ?>%</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-xs font-medium text-gray-500">Participant ID</span>
                            <span class="text-sm font-bold text-gray-900">#<?= e((string)$certificate['participant_id']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-primary-50 rounded-2xl border border-primary-100">
                    <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-3">Blockchain Hash</p>
                    <code class="text-[9px] font-mono text-primary-400 break-all leading-relaxed select-all"><?= e($certificate['verify_token']) ?></code>
                </div>

                <div class="pt-4">
                    <a href="<?= e(BASE_URL) ?>" class="flex items-center justify-center gap-2 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors no-underline">
                        <i class="fas fa-arrow-left"></i> Back to Homepage
                    </a>
                </div>
            </div>
            
            <p class="text-center text-[10px] font-bold text-gray-300 uppercase tracking-widest">
                 Roi Et Rajabhat University | 2026
            </p>
        </div>
    </main>
</div>

<!-- Invisible PDF Template -->
<?php if ($certificate && (int) $certificate['is_revoked'] === 0): 
    $template = getCertificateTemplate((int) ($certificate['template_id'] ?: 1));
    $layout = json_decode((string) ($template['layout_json'] ?? ''), true) ?: [];
    $orientation = ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait';
    $w = ($template['orientation'] ?? 'L') === 'L' ? '297mm' : '210mm';
    $h = ($template['orientation'] ?? 'L') === 'L' ? '210mm' : '297mm';
?>
    <div id="pdf-container" style="position: fixed; top: 0; left: 0; width: 0; height: 0; overflow: hidden; opacity: 0; pointer-events: none;">
        <div id="pdf-area" style="width: <?= $w ?>; height: <?= $h ?>; position: relative; background-color: white;">
            <?php if (!empty($template['bg_image'])): ?>
                <img src="<?= e(BASE_URL . '/' . $template['bg_image']) ?>" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: fill;">
            <?php endif; ?>

            <?php foreach ($layout as $field => $cfg): 
                $text = ''; $show = true;
                if ($field === 'name') { $text = ($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']; $show = !empty($template['show_name']); }
                elseif ($field === 'course') { $text = $certificate['project_name']; $show = !empty($template['show_course']); }
                elseif ($field === 'date') { $text = date('d/m/Y', strtotime($certificate['issued_date'])); $show = !empty($template['show_date']); }
                elseif ($field === 'certno') { $text = $certificate['cert_number']; $show = !empty($template['show_certno']); }
                
                if (!$text || !$show) continue;

                $style = "position: absolute; ";
                $style .= "left: " . ($cfg['x'] ?? 0) . "mm; ";
                $style .= "top: " . ($cfg['y'] ?? 0) . "mm; ";
                $style .= "font-size: " . ($cfg['size'] ?? 20) . "pt; ";
                $style .= "color: " . ($template['color_primary'] ?? '#E87722') . "; ";
                $style .= "font-family: 'Sarabun', sans-serif; ";
                $style .= "line-height: 1.2; ";
                if (!empty($cfg['bold'])) $style .= "font-weight: bold; ";
                if (($cfg['align'] ?? 'L') === 'C') $style .= "transform: translate(-50%, -50%); text-align: center;";
                elseif (($cfg['align'] ?? 'L') === 'R') $style .= "transform: translate(-100%, -50%); text-align: right;";
            ?>
                <div style="<?= $style ?> white-space: nowrap;"><?= e($text) ?></div>
            <?php endforeach; ?>
            
            <?php if (!empty($template['show_qr'])): ?>
                <div id="pdf-qr-render" style="position: absolute; bottom: 20mm; right: 20mm; width: 32mm; height: 32mm; background: white; padding: 2mm;"></div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    function downloadPDF() {
        const area = document.getElementById('pdf-area');
        const btn = document.getElementById('btn-download');
        if (!area || !btn) return;

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> Generating...';
        btn.disabled = true;

        const qrTarget = document.getElementById('pdf-qr-render');
        if (qrTarget && qrTarget.innerHTML.trim() === "") {
            new QRCode(qrTarget, {
                text: window.location.href,
                width: 120,
                height: 120,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        const opt = {
            margin: 0,
            filename: '<?= e($certificate['cert_number'] ?? 'certificate') ?>.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { scale: 3, useCORS: true, logging: false, scrollX: 0, scrollY: 0 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: '<?= ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait' ?>', compress: true }
        };

        html2pdf().set(opt).from(area).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            console.error(err);
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>

<style>
    .glass-card { background: white; }
    .shadow-premium { box-shadow: 0 40px 100px -20px rgba(0,0,0,0.15), 0 20px 40px -15px rgba(0,0,0,0.1); }
    .bg-mesh {
        background-color: #f8fafc;
        background-image: 
            radial-gradient(at 0% 0%, hsla(25,100%,93%,1) 0, transparent 50%), 
            radial-gradient(at 100% 100%, hsla(210,100%,93%,1) 0, transparent 50%);
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
    .group:hover .group-hover\:bounce { animation: bounce 0.6s infinite; }
</style>
