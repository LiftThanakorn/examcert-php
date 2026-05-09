
<div class="h-screen w-full overflow-hidden flex flex-col items-center justify-center p-6 bg-premium-mesh relative">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 via-orange-400 to-primary-500"></div>
    
    <!-- Top Navigation (Thai) -->
    <header class="w-full max-w-6xl flex items-center justify-between mb-8 fade-in-down no-print z-10">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-white/90 backdrop-blur shadow-2xl rounded-2xl flex items-center justify-center border border-white">
                <img src="<?= e(BASE_URL) ?>/assets/img/logo-reru.png" class="w-10 h-10 object-contain" onerror="this.src='https://ui-avatars.com/api/?name=RERU&background=E87722&color=fff'">
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight">ระบบตรวจสอบเกียรติบัตร</h1>
                <p class="text-xs text-primary-600 font-bold uppercase tracking-[0.2em]">Roi Et Rajabhat University</p>
            </div>
        </div>
        
        <?php if ($certificate && (int) $certificate['is_revoked'] === 0): ?>
            <button id="btn-download" onclick="downloadPDF()" class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl hover:bg-black transition-all hover:-translate-y-1 shadow-2xl active:scale-95 cursor-pointer border-none group">
                <i class="fas fa-file-pdf text-lg text-orange-400 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold tracking-wide">ดาวน์โหลดเกียรติบัตร (PDF)</span>
            </button>
        <?php endif; ?>
    </header>

    <!-- Content Area -->
    <main class="w-full max-w-6xl grid lg:grid-cols-12 gap-10 items-stretch flex-1 min-h-0">
        <!-- Certificate Showcase -->
        <div class="lg:col-span-8 flex flex-col justify-center min-h-0 py-4">
            <div class="relative group h-full flex items-center justify-center">
                <!-- Frame Decoration -->
                <div class="absolute -inset-4 bg-white/30 backdrop-blur-md rounded-[3rem] -z-10 border border-white/50 shadow-inner"></div>
                
                <div id="certificate-visual" class="certificate-frame rounded-2xl overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] relative aspect-[1.414/1] w-full border-8 border-white fade-in-scale">
                    <?php if (!$certificate): ?>
                        <div class="absolute inset-0 bg-slate-50 flex flex-col items-center justify-center p-12 text-center">
                            <div class="w-24 h-24 bg-red-100 text-red-500 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                <i class="fas fa-exclamation-triangle text-4xl"></i>
                            </div>
                            <h2 class="text-3xl font-black text-slate-900 mb-3">ไม่พบข้อมูลเกียรติบัตร</h2>
                            <p class="text-slate-500 max-w-sm mx-auto">รหัสตรวจสอบไม่ถูกต้อง หรือเกียรติบัตรนี้อาจยังไม่ได้ถูกออกในระบบ</p>
                        </div>
                    <?php elseif ((int) $certificate['is_revoked'] === 1): ?>
                        <div class="absolute inset-0 bg-orange-50 flex flex-col items-center justify-center p-12 text-center">
                            <div class="w-24 h-24 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                <i class="fas fa-shield-alt text-4xl"></i>
                            </div>
                            <h2 class="text-3xl font-black text-slate-900 mb-3">ใบเกียรติบัตรถูกยกเลิก</h2>
                            <p class="text-orange-700/70 max-w-sm mx-auto">ขออภัย ใบเกียรติบัตรฉบับนี้ถูกยกเลิกการใช้งานโดยผู้ดูแลระบบแล้ว</p>
                        </div>
                    <?php else: ?>
                        <!-- Modern Professional Preview -->
                        <div class="absolute inset-0 bg-white flex flex-col p-16 justify-between border-[12px] border-double border-slate-100">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">เลขที่ใบประกาศ</p>
                                    <p class="text-lg font-black text-slate-900"><?= e($certificate['cert_number']) ?></p>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <i class="fas fa-stamp text-3xl text-primary-500/30"></i>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-xs font-bold text-primary-600 uppercase tracking-[0.4em] mb-6">CERTIFICATE OF ACHIEVEMENT</p>
                                <h2 class="text-5xl font-black text-slate-900 mb-4 tracking-tight">
                                    <?= e(($certificate['title'] ? $certificate['title'] : '') . $certificate['first_name'] . ' ' . $certificate['last_name']) ?>
                                </h2>
                                <div class="w-24 h-1 bg-primary-500 mx-auto mb-8 rounded-full"></div>
                                <p class="text-lg text-slate-600 leading-relaxed max-w-xl mx-auto">
                                    ได้รับเกียรติบัตรฉบับนี้เพื่อแสดงว่าเป็นผู้ผ่านการทดสอบในโครงการ <br>
                                    <span class="text-slate-900 font-black">"<?= e($certificate['project_name']) ?>"</span>
                                </p>
                            </div>

                            <div class="flex justify-between items-end border-t border-slate-50 pt-8">
                                <div class="text-left">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">วันที่ออกประกาศ</p>
                                    <p class="text-base font-bold text-slate-900"><?= date('d/m/Y', strtotime($certificate['issued_date'])) ?></p>
                                </div>
                                <div class="w-24 h-24 bg-white rounded-2xl p-2 shadow-inner border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-qrcode text-4xl text-slate-200"></i>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Details Sidebar -->
        <div class="lg:col-span-4 flex flex-col justify-center min-h-0 py-4 no-print">
            <div class="bg-white/80 backdrop-blur-2xl border border-white rounded-[2.5rem] p-10 shadow-2xl flex flex-col h-full overflow-hidden fade-in-right">
                <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] mb-8 border-b border-slate-100 pb-4">รายละเอียดความถูกต้อง</h3>
                
                <div class="flex-1 overflow-y-auto custom-scrollbar space-y-8 pr-2">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">สถานะใบประกาศ</p>
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 text-green-600 rounded-full text-[11px] font-black uppercase">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> ใช้งานได้
                            </span>
                        </div>
                        <div class="space-y-1 text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">คะแนนที่ได้</p>
                            <p class="text-2xl font-black text-slate-900"><?= e((string) $certificate['percent']) ?><span class="text-xs ml-0.5">%</span></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 group transition-all hover:bg-white hover:shadow-xl">
                            <p class="text-[10px] font-black text-primary-500 uppercase tracking-widest mb-2">รหัสตรวจสอบ (Verify Token)</p>
                            <code class="text-[11px] font-mono text-slate-500 break-all leading-relaxed select-all cursor-copy"><?= e($certificate['verify_token']) ?></code>
                        </div>
                        
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700">
                            <i class="fas fa-info-circle text-xl"></i>
                            <p class="text-[11px] font-bold leading-relaxed">
                                ข้อมูลนี้ถูกรับรองโดยระบบบริหารจัดการเกียรติบัตรออนไลน์ มหาวิทยาลัยราชภัฏร้อยเอ็ด
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ข้อมูลผู้รับ</h4>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold">
                                <?= mb_substr($certificate['first_name'], 0, 1) ?>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 leading-none mb-1"><?= e($certificate['first_name'] . ' ' . $certificate['last_name']) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic">Participant verified</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-100">
                    <a href="<?= e(BASE_URL) ?>" class="flex items-center justify-center gap-3 py-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-sm font-bold text-slate-600 transition-all no-underline group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        กลับสู่หน้าหลัก
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Hidden PDF Core (Thai Optimized) -->
<?php if ($certificate && (int) $certificate['is_revoked'] === 0): 
    $template = getCertificateTemplate((int) ($certificate['template_id'] ?: 1));
    $layout = json_decode((string) ($template['layout_json'] ?? ''), true) ?: [];
    $orientation = ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait';
    $w = ($template['orientation'] ?? 'L') === 'L' ? '297mm' : '210mm';
    $h = ($template['orientation'] ?? 'L') === 'L' ? '210mm' : '297mm';
?>
    <div id="pdf-render-box" style="position: absolute; top: 0; left: 0; width: 297mm; height: 210mm; z-index: -100; visibility: hidden; pointer-events: none;">
        <div id="pdf-area" style="width: 297mm; height: 210mm; position: relative; background-color: white; margin: 0; padding: 0; border: none;">
            <?php if (!empty($template['bg_image'])): ?>
                <img src="<?= e(BASE_URL . '/' . $template['bg_image']) ?>" style="position: absolute; top: 0; left: 0; width: 297mm; height: 210mm; object-fit: fill; border: none; display: block;">
            <?php endif; ?>

            <?php foreach ($layout as $field => $cfg): 
                $text = ''; $show = true;
                if ($field === 'name') { 
                    $text = ($certificate['title'] ? $certificate['title'] : '') . $certificate['first_name'] . ' ' . $certificate['last_name']; 
                    $show = !empty($template['show_name']); 
                }
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
                $style .= "line-height: 1.1; ";
                if (!empty($cfg['bold'])) $style .= "font-weight: bold; ";
                if (($cfg['align'] ?? 'L') === 'C') $style .= "transform: translate(-50%, -50%); text-align: center;";
                elseif (($cfg['align'] ?? 'L') === 'R') $style .= "transform: translate(-100%, -50%); text-align: right;";
            ?>
                <div style="<?= $style ?> white-space: nowrap;"><?= e($text) ?></div>
            <?php endforeach; ?>
            
            <?php if (!empty($template['show_qr'])): ?>
                <div id="pdf-qr-box" style="position: absolute; bottom: 20mm; right: 20mm; width: 32mm; height: 32mm; background: white; padding: 2mm;"></div>
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
        const orientation = '<?= ($template['orientation'] ?? 'L') === 'L' ? 'landscape' : 'portrait' ?>';
        
        if (!area || !btn) return;

        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังสร้างไฟล์ PDF...';
        btn.disabled = true;

        const qrTarget = document.getElementById('pdf-qr-box');
        if (qrTarget && qrTarget.innerHTML.trim() === "") {
            new QRCode(qrTarget, {
                text: window.location.href,
                width: 120, height: 120,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        const opt = {
            margin: 0,
            filename: '<?= e($certificate['cert_number']) ?>.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { 
                scale: 2, 
                useCORS: true, 
                logging: false, 
                scrollX: 0, 
                scrollY: 0,
                windowWidth: orientation === 'landscape' ? 1123 : 794
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: orientation, 
                compress: true 
            }
        };

        html2pdf().set(opt).from(area).save().then(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }).catch(err => {
            console.error(err);
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        });
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700;800&display=swap');
    
    body { font-family: 'Sarabun', sans-serif; }
    
    .bg-premium-mesh {
        background-color: #f1f5f9;
        background-image: 
            radial-gradient(at 0% 0%, hsla(25,100%,92%,1) 0, transparent 50%), 
            radial-gradient(at 100% 0%, hsla(210,100%,92%,1) 0, transparent 50%),
            radial-gradient(at 50% 100%, hsla(25,100%,95%,1) 0, transparent 50%);
    }

    .certificate-frame {
        box-shadow: 0 40px 100px -20px rgba(0,0,0,0.3);
        transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .certificate-frame:hover { transform: scale(1.02) rotate(0.5deg); }

    .fade-in-down { animation: fadeInDown 0.8s ease-out; }
    .fade-in-scale { animation: fadeInScale 1s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .fade-in-right { animation: fadeInRight 0.8s ease-out 0.2s both; }

    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInScale { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    @keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
