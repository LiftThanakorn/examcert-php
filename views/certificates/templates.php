<?php
$templateMode = $templateMode ?? 'list';
$errors = $errors ?? [];
$template = array_merge(templateDefaults(), $template ?? []);
?>

<?php if ($templateMode === 'list'): ?>
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">คลังเทมเพลตเกียรติบัตร</h2>
            <p class="text-sm text-slate-400 mt-1 font-medium">จัดการรูปแบบและองค์ประกอบของใบประกาศเกียรติบัตร</p>
        </div>
        <a href="<?= e(BASE_URL) ?>/admin/certificates/template-create.php" class="inline-flex items-center px-6 py-3 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-2xl transition-all shadow-xl">
            <i class="fas fa-plus mr-2 text-orange-400"></i> สร้างเทมเพลตใหม่
        </a>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-400 tracking-widest uppercase">
                        <th class="px-8 py-6">ชื่อเทมเพลต</th>
                        <th class="px-8 py-6">การวางแนว</th>
                        <th class="px-8 py-6 text-center">สถานะ</th>
                        <th class="px-8 py-6 text-right">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    <?php foreach ($templates as $item): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5 font-bold text-slate-700"><?= e($item['name']) ?></td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase">
                                    <?= $item['orientation'] === 'L' ? 'แนวนอน (A4)' : 'แนวตั้ง (A4)' ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black <?= (int)$item['is_active'] === 1 ? 'bg-green-50 text-green-600' : 'bg-slate-50 text-slate-300' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= (int)$item['is_active'] === 1 ? 'bg-green-500' : 'bg-slate-300' ?>"></span>
                                    <?= (int)$item['is_active'] === 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน' ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= e(BASE_URL) ?>/admin/certificates/template-edit.php?id=<?= (int)$item['id'] ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-primary-500 hover:border-primary-200 transition-all">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?= (int)$item['id'] ?>, '<?= e($item['name']) ?>')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-red-500 hover:border-red-200 transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <!-- Fullscreen Studio (No-Scroll) -->
    <div id="designer-studio" class="h-[calc(100vh-140px)] flex flex-col gap-4 overflow-hidden relative opacity-0 transition-opacity duration-500">
        <!-- Studio Header (Thai) -->
        <div class="flex items-center justify-between px-2 flex-shrink-0">
            <div class="flex items-center gap-4">
                <a href="<?= e(BASE_URL) ?>/admin/certificates/templates.php" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-slate-600">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <div>
                    <h2 class="text-lg font-black text-slate-800 leading-none mb-1"><?= e($pageTitle) ?></h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Certificate Designer Studio</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="loadDefaultLayout()" class="px-4 py-2 text-xs font-black text-slate-400 hover:text-primary-500 transition-colors">
                    <i class="fas fa-undo mr-1.5 text-[10px]"></i> คืนค่าเริ่มต้น
                </button>
                <button type="submit" form="main-form" class="px-8 py-3 bg-slate-900 hover:bg-black text-white text-sm font-black rounded-xl transition-all shadow-xl active:scale-95">
                    บันทึกรูปแบบ
                </button>
            </div>
        </div>

        <div class="flex-1 flex gap-4 min-h-0">
            <!-- Sidebar: Config (Thai Labels) -->
            <aside class="w-72 flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-1 flex-shrink-0">
                <form id="main-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="contents">
                    <?= csrfField() ?>
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 space-y-5 shadow-sm">
                        <div class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em] mb-2">ข้อมูลพื้นฐาน</div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">ชื่อเทมเพลต</label>
                            <input name="name" required value="<?= e($template['name']) ?>" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-primary-500/20" placeholder="ระบุชื่อเทมเพลต...">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">ภาพพื้นหลัง (A4)</label>
                            <div class="relative group">
                                <div class="w-full aspect-video bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden relative transition-all group-hover:border-primary-300">
                                    <img id="sidebar-preview" src="<?= !empty($template['bg_image']) ? e(BASE_URL . '/' . $template['bg_image']) : '' ?>" class="w-full h-full object-cover <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                                    <div id="sidebar-placeholder" class="text-center <?= !empty($template['bg_image']) ? 'hidden' : '' ?>">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-slate-200 mb-2"></i>
                                        <p class="text-[9px] font-black text-slate-300 uppercase">Click to Upload</p>
                                    </div>
                                </div>
                                <input type="file" name="bg_image" id="bg-input" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 p-6 space-y-4 shadow-sm">
                        <div class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em] mb-2">การแสดงผล</div>
                        <?php 
                        $opts = [
                            'show_name' => 'ชื่อผู้สอบ',
                            'show_course' => 'ชื่อโครงการ / หลักสูตร',
                            'show_certno' => 'เลขที่ใบเซอร์',
                            'show_qr' => 'QR Code',
                            'show_date' => 'วันที่ออก',
                            'is_active' => 'เปิดใช้งาน'
                        ];
                        foreach($opts as $k => $v): ?>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors"><?= $v ?></span>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="<?= $k ?>" value="1" <?= !empty($template[$k]) ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-8 h-4.5 bg-slate-100 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3.5 after:w-3.5 after:transition-all peer-checked:bg-primary-500"></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <textarea name="layout_json" id="layout_json" class="hidden"><?= e($template['layout_json'] ?? '') ?></textarea>
                    <input type="hidden" name="orientation" value="<?= e($template['orientation']) ?>">
                </form>
            </aside>

            <!-- Main Canvas: Centralized & Accurate -->
            <div class="flex-1 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex items-center justify-center relative overflow-hidden p-10 shadow-inner">
                <div id="designer-container" class="relative bg-white shadow-[0_50px_100px_-20px_rgba(0,0,0,0.2)] transition-all overflow-hidden border border-slate-200" 
                     style="width: <?= $template['orientation'] === 'L' ? '800px' : '566px' ?>; aspect-ratio: <?= $template['orientation'] === 'L' ? '1.414/1' : '1/1.414' ?>;">
                    <img id="designer-bg" src="<?= !empty($template['bg_image']) ? e(BASE_URL . '/' . $template['bg_image']) : '' ?>" class="w-full h-full block select-none pointer-events-none <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                    <div id="designer-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-200 border-4 border-double border-slate-50 <?= !empty($template['bg_image']) ? 'hidden' : '' ?>">
                        <i class="fas fa-file-invoice text-5xl mb-4 opacity-20"></i>
                        <p class="font-black text-xs uppercase tracking-[0.3em] opacity-30">Certificate Canvas Area</p>
                    </div>

                    <!-- Draggable Tags -->
                    <div id="drag-name" class="designer-tag" data-id="name">ชื่อผู้เข้าสอบ</div>
                    <div id="drag-course" class="designer-tag tag-blue" data-id="course">ชื่อโครงการ / หลักสูตร</div>
                    <div id="drag-date" class="designer-tag tag-green" data-id="date">วันที่</div>
                    <div id="drag-certno" class="designer-tag tag-purple" data-id="certno">เลขที่ใบเซอร์</div>
                    <div id="drag-qrcode" class="designer-tag tag-dark" data-id="qrcode" style="width: 50px; height: 50px;">QR</div>
                </div>
            </div>

            <!-- Properties Sidebar (Thai) -->
            <aside class="w-72 flex flex-col gap-4 overflow-y-auto custom-scrollbar pl-1 flex-shrink-0">
                <div id="properties-panel" class="bg-white rounded-3xl border border-slate-100 p-6 space-y-6 shadow-sm">
                    <div class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em]">พารามิเตอร์การจัดวาง</div>
                    
                    <?php 
                    $elements = [
                        'name' => ['label' => 'ชื่อผู้สอบ', 'color' => 'bg-primary-500'],
                        'course' => ['label' => 'ชื่อหลักสูตร', 'color' => 'bg-blue-500'],
                        'date' => ['label' => 'วันที่ออก', 'color' => 'bg-green-500'],
                        'certno' => ['label' => 'เลขที่ใบเซอร์', 'color' => 'bg-purple-500'],
                        'qrcode' => ['label' => 'QR Code', 'color' => 'bg-slate-800'],
                    ];
                    foreach($elements as $id => $meta): ?>
                        <div class="space-y-4 pb-6 border-b border-slate-50 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-md <?= $meta['color'] ?> shadow-sm"></span>
                                <span class="text-xs font-black text-slate-700 uppercase"><?= $meta['label'] ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase">พิกัด X (มม.)</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-x" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[11px] font-mono font-bold text-slate-700">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase">พิกัด Y (มม.)</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-y" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[11px] font-mono font-bold text-slate-700">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase"><?= $id==='qrcode'?'ขนาด (มม.)':'ขนาดอักษร' ?></label>
                                    <input type="number" id="input-<?= $id ?>-size" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[11px] font-bold text-slate-700">
                                </div>
                                <?php if($id!=='qrcode'): ?>
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase">การจัดวาง</label>
                                    <select id="input-<?= $id ?>-align" onchange="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-2 py-2 text-[10px] font-bold text-slate-700 cursor-pointer">
                                        <option value="L">ชิดซ้าย</option>
                                        <option value="C">กึ่งกลาง</option>
                                        <option value="R">ชิดขวา</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </div>

    <!-- Designer Loading Shield -->
    <div id="designer-shield" class="fixed inset-0 bg-white z-[9999] flex flex-col items-center justify-center transition-opacity duration-700">
        <div class="w-16 h-16 border-4 border-slate-100 border-t-primary-500 rounded-full animate-spin mb-6"></div>
        <p class="text-sm font-black text-slate-400 uppercase tracking-[0.3em]">กำลังโหลด Designer...</p>
    </div>

    <style>
        .designer-tag {
            position: absolute; z-index: 100; cursor: grab; user-select: none;
            padding: 6px 12px; background: #E87722; color: white; border-radius: 8px;
            font-size: 11px; font-weight: 800; border: 2px solid white; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); white-space: nowrap;
            /* Anchoring Logic */
            transform-origin: center center;
            line-height: 1;
        }
        .designer-tag.tag-blue { background: #3B82F6; }
        .designer-tag.tag-green { background: #10B981; }
        .designer-tag.tag-purple { background: #8B5CF6; }
        .designer-tag.tag-dark { background: #1F2937; }
        .designer-tag:active { cursor: grabbing; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    <script>
    const studio = document.getElementById('designer-studio');
    const shield = document.getElementById('designer-shield');
    const container = document.getElementById('designer-container');
    const layoutText = document.getElementById('layout_json');
    const A4_W = <?= $template['orientation'] === 'L' ? '297' : '210' ?>;
    let layout = {};
    try { layout = JSON.parse(layoutText.value || '{}'); } catch(e) { layout = {}; }

    function sync() {
        const ratio = A4_W / container.offsetWidth;
        Object.keys(layout).forEach(id => {
            const el = document.getElementById('drag-' + id);
            if (!el) return;
            
            let xOffset = 0, yOffset = el.offsetHeight / 2;
            const align = layout[id].align || 'L';
            if (align === 'C') xOffset = el.offsetWidth / 2;
            else if (align === 'R') xOffset = el.offsetWidth;

            layout[id].x = parseFloat(((parseFloat(el.style.left) + xOffset) * ratio).toFixed(2));
            layout[id].y = parseFloat(((parseFloat(el.style.top) + yOffset) * ratio).toFixed(2));

            // Sync with Inputs
            ['x', 'y'].forEach(axis => {
                const input = document.getElementById(`input-${id}-${axis}`);
                if (input) input.value = layout[id][axis];
            });
            const sInput = document.getElementById(`input-${id}-size`);
            if (sInput) sInput.value = layout[id].size || layout[id].w || 20;
            const aInput = document.getElementById(`input-${id}-align`);
            if (aInput) aInput.value = layout[id].align || 'L';
        });
        layoutText.value = JSON.stringify(layout, null, 4);
    }

    function init() {
        const ratio = container.offsetWidth / A4_W;
        Object.keys(layout).forEach(id => {
            const el = document.getElementById('drag-' + id);
            if (!el) return;

            const align = layout[id].align || 'L';
            if (align === 'C') el.style.transform = 'translate(-50%, -50%)';
            else if (align === 'R') el.style.transform = 'translate(-100%, -50%)';
            else el.style.transform = 'translate(0, -50%)';

            el.style.left = (layout[id].x * ratio) + 'px';
            el.style.top = (layout[id].y * ratio) + 'px';
            
            if (id === 'qrcode') {
                el.style.width = (layout[id].w * ratio) + 'px';
                el.style.height = (layout[id].w * ratio) + 'px';
            }
            drag(el);
        });
        sync();
        
        // Hide Shield
        shield.style.opacity = '0';
        setTimeout(() => { shield.style.display = 'none'; studio.style.opacity = '1'; }, 700);
    }

    function updateFromInputs(id) {
        if (!layout[id]) return;
        layout[id].x = parseFloat(document.getElementById(`input-${id}-x`).value || 0);
        layout[id].y = parseFloat(document.getElementById(`input-${id}-y`).value || 0);
        
        if (id === 'qrcode') {
            layout[id].w = parseFloat(document.getElementById(`input-${id}-size`).value || 28);
        } else {
            layout[id].size = parseFloat(document.getElementById(`input-${id}-size`).value || 20);
            layout[id].align = document.getElementById(`input-${id}-align`).value;
        }
        init(); 
    }

    function drag(el) {
        let x1=0, y1=0, x2=0, y2=0;
        el.onmousedown = (e) => {
            x2 = e.clientX; y2 = e.clientY;
            document.onmousemove = (e) => {
                x1 = x2 - e.clientX; y1 = y2 - e.clientY;
                x2 = e.clientX; y2 = e.clientY;
                el.style.left = (el.offsetLeft - x1) + "px";
                el.style.top = (el.offsetTop - y1) + "px";
                sync();
            };
            document.onmouseup = () => { document.onmousemove = null; sync(); };
        };
    }

    function loadDefaultLayout() {
        Swal.fire({
            title: 'คืนค่าเริ่มต้น?',
            text: 'ตำแหน่งและขนาดทั้งหมดจะถูกรีเซ็ต',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            customClass: { popup: 'rounded-3xl', confirmButton: 'bg-slate-900 px-6 py-2 rounded-xl text-white font-bold', cancelButton: 'text-slate-400 font-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                layout = {
                    "name":   { "x": 148.5, "y": 80,  "align": "C", "size": 38 },
                    "course": { "x": 148.5, "y": 110, "align": "C", "size": 22 },
                    "date":   { "x": 148.5, "y": 130, "align": "C", "size": 16 },
                    "certno": { "x": 230,   "y": 170, "align": "R", "size": 11 },
                    "qrcode": { "x": 240,   "y": 140, "w": 28 }
                };
                init();
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'ลบเทมเพลต?',
            text: `คุณต้องการลบ "${name}" ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบข้อมูล',
            cancelButtonText: 'ยกเลิก',
            customClass: { popup: 'rounded-3xl', confirmButton: 'bg-red-500 px-6 py-2 rounded-xl text-white font-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= e(BASE_URL) ?>/admin/certificates/template-delete.php';
                form.innerHTML = `<?= csrfField() ?><input type="hidden" name="id" value="${id}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    window.onload = () => setTimeout(init, 500);
    </script>
<?php endif; ?>
