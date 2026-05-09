
<div class="max-w-xl w-full">
    <!-- Logo & Header -->
    <div class="text-center mb-10 fade-up">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-soft mb-6 border border-orange-50">
            <i class="fas fa-certificate text-4xl text-primary-500"></i>
        </div>
        <h1 class="text-3xl font-black text-gray-900 font-outfit tracking-tight mb-2">ExamCert Verification</h1>
        <p class="text-sm text-gray-400 font-medium tracking-wide uppercase">Digital Achievement Verification System</p>
    </div>

    <!-- Main Card (This will be the PDF Content) -->
    <main id="certificate-content" class="glass-card rounded-[3rem] overflow-hidden fade-up relative">
        <!-- Decorative Element -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-primary-500/5 rounded-bl-[10rem] -mr-10 -mt-10"></div>

        <div class="p-10 md:p-14 relative z-10">
            <?php if (!$certificate): ?>
                <div class="text-center py-10">
                    <div class="w-24 h-24 bg-red-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-sm">
                        <i class="fas fa-search text-4xl text-red-400"></i>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-4">ไม่พบข้อมูล</h2>
                    <p class="text-gray-400 leading-relaxed max-w-sm mx-auto mb-10">
                        ขออภัย ไม่พบเกียรติบัตรที่ระบุในระบบ กรุณาตรวจสอบลิงก์ หรือสแกน QR Code ใหม่อีกครั้ง
                    </p>
                    <a href="<?= e(BASE_URL) ?>" class="inline-flex items-center gap-3 px-10 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition-all hover:-translate-y-1 shadow-lg active:scale-95 no-underline">
                        <i class="fas fa-home text-sm"></i>
                        กลับหน้าหลัก
                    </a>
                </div>
            <?php elseif ((int) $certificate['is_revoked'] === 1): ?>
                <div class="text-center py-10">
                    <div class="w-24 h-24 bg-orange-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-sm">
                        <i class="fas fa-ban text-4xl text-orange-400"></i>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-4">สถานะ: ยกเลิก</h2>
                    <p class="text-gray-400 leading-relaxed max-w-sm mx-auto mb-8">
                        ใบเกียรติบัตรฉบับนี้ถูกยกเลิกโดยผู้ดูแลระบบและไม่สามารถนำไปใช้อ้างอิงได้
                    </p>
                    <div class="p-6 bg-orange-50/50 rounded-3xl border border-orange-100 text-left">
                        <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest block mb-2">เหตุผลการยกเลิก</span>
                        <p class="text-sm text-gray-700 font-medium italic">"<?= e($certificate['revoke_reason'] ?: 'ไม่ระบุเหตุผล') ?>"</p>
                    </div>
                </div>
            <?php else: ?>
                <?php $fullName = ($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']; ?>
                
                <!-- Success Header -->
                <div class="flex flex-col items-center mb-12">
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-green-50 text-green-600 rounded-full border border-green-100 mb-8 shadow-sm">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs font-black tracking-widest uppercase">Verified Success</span>
                    </div>
                    
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mb-6">Official Achievement</p>
                    <h3 class="text-4xl font-black text-gray-900 text-center leading-[1.2]"><?= e($fullName) ?></h3>
                </div>

                <div class="space-y-8">
                    <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
                    
                    <div class="grid gap-8">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">โครงการ / หลักสูตร</span>
                            <p class="text-xl font-bold text-gray-800 leading-relaxed"><?= e($certificate['project_name']) ?></p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-8 pt-4">
                            <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">เลขที่ใบประกาศ</span>
                                <p class="text-lg font-black text-gray-900 font-outfit"><?= e($certificate['cert_number']) ?></p>
                            </div>
                            <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100 text-right">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">วันที่ออกใบประกาศ</span>
                                <p class="text-lg font-black text-gray-900 font-outfit"><?= date('d/m/Y', strtotime($certificate['issued_date'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 p-8 bg-primary-50 rounded-[2.5rem] border border-primary-100 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm border border-primary-100">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-1">สถานะการสอบ</p>
                                <p class="text-sm font-bold text-gray-900">ผ่านการทดสอบออนไลน์</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-primary-400 uppercase tracking-widest mb-1">คะแนนสอบ</p>
                            <p class="text-lg font-black text-primary-600 font-outfit"><?= e((string) $certificate['percent']) ?><span class="text-xs ml-0.5">%</span></p>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="mt-14 pt-8 border-t border-gray-100 flex flex-col items-center gap-6">
                    <div class="text-center">
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-[0.2em] mb-3">Verification Token (Blockchain Signature)</p>
                        <code class="text-[10px] font-mono bg-gray-50 text-gray-400 px-4 py-2 rounded-xl border border-gray-100 select-all"><?= e($certificate['verify_token']) ?></code>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bottom Branding -->
        <div class="bg-gray-50/50 py-6 text-center border-t border-gray-100">
            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">ExamCert Digital Verification System v1.0</p>
        </div>
    </main>

    <!-- Download Button (Outside of PDF Content) -->
    <?php if ($certificate && (int) $certificate['is_revoked'] === 0): ?>
        <div class="mt-8 pt-6 fade-up">
            <button id="btn-download" onclick="downloadPDF()" class="inline-flex items-center justify-center gap-3 w-full h-16 bg-gray-900 hover:bg-black text-white font-black rounded-3xl transition-all shadow-xl active:scale-95 border-none cursor-pointer">
                <i class="fas fa-file-pdf text-xl"></i>
                ดาวน์โหลดใบประกาศนียบัตร (JS PDF)
            </button>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="mt-12 text-center fade-up">
        <div class="flex items-center justify-center gap-4 mb-4 text-gray-300">
            <i class="fas fa-shield-halved text-sm"></i>
            <div class="h-4 w-px bg-gray-200"></div>
            <span class="text-[10px] font-bold uppercase tracking-widest">Secured & Verified Achievement</span>
        </div>
        <p class="text-[10px] text-gray-400 font-medium">Roi Et Rajabhat University | 2026</p>
    </footer>
</div>

<!-- Hidden Certificate Preview for PDF Generation -->
<?php if ($certificate && (int) $certificate['is_revoked'] === 0): 
    $template = getCertificateTemplate((int) ($certificate['template_id'] ?: 1));
    $layout = json_decode((string) ($template['layout_json'] ?? ''), true) ?: [
        "name"   => ["x" => 148.5, "y" => 100, "align" => "C", "size" => 38, "bold" => true],
        "course" => ["x" => 148.5, "y" => 125, "align" => "C", "size" => 22, "bold" => false],
        "date"   => ["x" => 148.5, "y" => 145, "align" => "C", "size" => 16, "bold" => false],
        "certno" => ["x" => 250,   "y" => 185, "align" => "R", "size" => 11, "bold" => false]
    ];
    $orientation = ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait';
    $w = ($template['orientation'] ?? 'L') === 'L' ? '297mm' : '210mm';
    $h = ($template['orientation'] ?? 'L') === 'L' ? '210mm' : '297mm';
?>
    <div id="cert-pdf-template" style="position: absolute; top: 0; left: 0; z-index: -1; visibility: hidden;">
        <div id="cert-capture-area" style="width: <?= $w ?>; height: <?= $h ?>; position: relative; background-color: white; overflow: hidden; margin: 0; padding: 0;">
            <!-- Background -->
            <?php if (!empty($template['bg_image'])): ?>
                <img src="<?= e(BASE_URL . '/' . $template['bg_image']) ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: fill; border: none; margin: 0; padding: 0;">
            <?php endif; ?>

            <!-- Dynamic Content based on Layout JSON -->
            <?php foreach ($layout as $field => $cfg): 
                $text = '';
                if ($field === 'name') $text = $fullName;
                elseif ($field === 'course') $text = $certificate['project_name'];
                elseif ($field === 'date') $text = date('d/m/Y', strtotime($certificate['issued_date']));
                elseif ($field === 'certno') $text = $certificate['cert_number'];
                
                if (!$text) continue;

                $style = "position: absolute; ";
                $style .= "left: " . ($cfg['x'] ?? 0) . "mm; ";
                $style .= "top: " . ($cfg['y'] ?? 0) . "mm; ";
                $style .= "font-size: " . ($cfg['size'] ?? 20) . "pt; ";
                $style .= "color: " . ($template['color_primary'] ?? '#E87722') . "; ";
                $style .= "font-family: 'Sarabun', 'Noto Sans Thai', sans-serif; ";
                $style .= "line-height: 1; ";
                if (!empty($cfg['bold'])) $style .= "font-weight: bold; ";
                if (($cfg['align'] ?? 'L') === 'C') $style .= "transform: translateX(-50%); text-align: center;";
                elseif (($cfg['align'] ?? 'L') === 'R') $style .= "transform: translateX(-100%); text-align: right;";
            ?>
                <div style="<?= $style ?> white-space: nowrap;"><?= e($text) ?></div>
            <?php endforeach; ?>
            
            <!-- QR Code Placeholder -->
            <?php if (!empty($template['show_qr'])): ?>
                <div id="pdf-qr-target" style="position: absolute; bottom: 20mm; right: 20mm; width: 32mm; height: 32mm; background: white; padding: 2mm;"></div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    function downloadPDF() {
        const container = document.getElementById('cert-pdf-template');
        const captureArea = document.getElementById('cert-capture-area');
        const btn = document.getElementById('btn-download');
        
        if (!container || !captureArea) return;

        // Change button state
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xl"></i> กำลังสร้างไฟล์...';
        btn.disabled = true;

        // Show temporarily for capture but keep it invisible
        container.style.visibility = 'visible';
        container.style.display = 'block';

        // Inject QR Code if not already there
        const qrTarget = document.getElementById('pdf-qr-target');
        if (qrTarget && qrTarget.innerHTML.trim() === "") {
            new QRCode(qrTarget, {
                text: window.location.href,
                width: 120,
                height: 120,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        const opt = {
            margin:       0,
            filename:     '<?= e($certificate['cert_number'] ?? 'certificate') ?>.pdf',
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  { 
                scale: 3, 
                useCORS: true, 
                logging: false,
                letterRendering: true,
                scrollX: 0,
                scrollY: 0,
                windowWidth: document.documentElement.offsetWidth,
                windowHeight: document.documentElement.offsetHeight
            },
            jsPDF:        { 
                unit: 'mm', 
                format: 'a4', 
                orientation: '<?= ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait' ?>',
                compress: true
            }
        };

        // Run html2pdf
        html2pdf().set(opt).from(captureArea).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            container.style.visibility = 'hidden';
        }).catch(err => {
            console.error('PDF Error:', err);
            btn.innerHTML = originalText;
            btn.disabled = false;
            container.style.visibility = 'hidden';
        });
    }
</script>

<style>
    .glass-card {
        @apply bg-white/90 backdrop-blur-2xl border border-white/50 shadow-premium;
    }
</style>
