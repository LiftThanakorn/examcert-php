<?php
$templateMode = $templateMode ?? 'list';
$errors = $errors ?? [];
$template = array_merge(templateDefaults(), $template ?? []);
$signatures = json_decode((string)($template['signature_paths'] ?? '[]'), true);
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
    <!-- Fullscreen Studio -->
    <div id="designer-studio" class="h-[calc(100vh-140px)] flex flex-col gap-4 overflow-hidden relative opacity-0 transition-opacity duration-500">
        <!-- Studio Header -->
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
                <button type="submit" form="main-form" class="px-8 py-3 bg-slate-900 hover:bg-black text-white text-sm font-black rounded-xl transition-all shadow-xl">
                    บันทึกรูปแบบ
                </button>
            </div>
        </div>

        <div class="flex-1 flex gap-4 min-h-0">
            <!-- Left Sidebar -->
            <aside class="w-72 flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-1 flex-shrink-0 pb-10">
                <form id="main-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="contents">
                    <?= csrfField() ?>
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 space-y-5 shadow-sm">
                        <div class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em] mb-2">ข้อมูลพื้นฐาน</div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">ชื่อเทมเพลต</label>
                            <input name="name" required value="<?= e($template['name']) ?>" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-primary-500/20" placeholder="ระบุชื่อเทมเพลต...">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">การวางแนว</label>
                                <select name="orientation" id="orient-select" class="w-full bg-slate-50 border-none rounded-xl px-2 py-3 text-xs font-bold text-slate-700 cursor-pointer" onchange="updateOrientation()">
                                    <option value="L" <?= $template['orientation']==='L'?'selected':'' ?>>แนวนอน</option>
                                    <option value="P" <?= $template['orientation']==='P'?'selected':'' ?>>แนวตั้ง</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">สีหลัก</label>
                                <div class="flex items-center gap-2 bg-slate-50 rounded-xl px-2 py-2.5">
                                    <input type="color" name="color_primary" value="<?= e($template['color_primary'] ?? '#E87722') ?>" id="color-picker" oninput="updateColor()" class="w-8 h-8 border-none bg-transparent cursor-pointer rounded-lg overflow-hidden p-0">
                                    <input type="text" value="<?= e($template['color_primary'] ?? '#E87722') ?>" class="w-full bg-transparent border-none p-0 text-[10px] font-mono font-bold text-slate-500 uppercase pointer-events-none">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">ภาพพื้นหลัง (A4)</label>
                            <div class="relative group">
                                <div class="w-full aspect-video bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden relative">
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
                            'show_course' => 'ชื่อหลักสูตร',
                            'show_certno' => 'เลขที่ใบเซอร์',
                            'show_qr' => 'QR Code',
                            'show_date' => 'วันที่ออก',
                            'show_score' => 'คะแนนที่ได้',
                            'is_active' => 'เปิดใช้งาน'
                        ];
                        foreach($opts as $k => $v): ?>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs font-bold text-slate-500"><?= $v ?></span>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="<?= $k ?>" value="1" <?= !empty($template[$k]) ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-100 rounded-full peer-checked:bg-primary-500 transition-colors relative">
                                        <div class="absolute top-1 left-1 w-3 h-3 bg-white rounded-full transition-all peer-checked:left-6"></div>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Logo and Signatures Side UI -->
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 space-y-4 shadow-sm">
                        <div class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em] mb-2">โลโก้และลายเซ็น</div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2">โลโก้หน่วยงาน</label>
                            <input type="file" name="logo_image" class="w-full text-[10px] font-bold text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-slate-100 file:text-slate-500 hover:file:bg-slate-200">
                        </div>
                        <?php for($i=1;$i<=2;$i++): ?>
                        <div class="pt-2 border-t border-slate-50">
                            <label class="block text-[9px] font-black text-slate-400 uppercase mb-2">ลายเซ็น <?= $i ?></label>
                            <input type="file" name="sign_image_<?= $i ?>" class="w-full text-[10px] font-bold text-slate-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-slate-100 file:text-slate-500 hover:file:bg-slate-200 mb-2">
                            <input type="text" name="sign_name_<?= $i ?>" value="<?= e($signatures[$i-1]['name'] ?? '') ?>" placeholder="ชื่อ-นามสกุล (กำกับ)" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[10px] font-bold">
                        </div>
                        <?php endfor; ?>
                    </div>
                    
                    <textarea name="layout_json" id="layout_json" class="hidden"><?= e($template['layout_json'] ?? '') ?></textarea>
                </form>
            </aside>

            <!-- Main Canvas -->
            <div class="flex-1 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex items-center justify-center relative overflow-hidden p-10 shadow-inner">
                <div class="designer-wrapper">
                    <div id="designer-container" class="relative bg-white shadow-[0_50px_100px_-20px_rgba(0,0,0,0.2)] overflow-hidden" 
                         style="width: <?= $template['orientation'] === 'L' ? '1123px' : '794px' ?>; height: <?= $template['orientation'] === 'L' ? '794px' : '1123px' ?>;">
                        <img id="designer-bg" src="<?= !empty($template['bg_image']) ? e(BASE_URL . '/' . $template['bg_image']) : '' ?>" class="absolute inset-0 w-full h-full block select-none pointer-events-none object-fill <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                        <div id="designer-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-200 border-4 border-double border-slate-50 <?= !empty($template['bg_image']) ? 'hidden' : '' ?>">
                            <p class="font-black text-xs uppercase tracking-[0.3em] opacity-30">Certificate Canvas Area</p>
                        </div>

                        <!-- Draggable Elements -->
                        <div id="drag-name" class="designer-tag" data-id="name" style="color: <?= e($template['color_primary']) ?>">ชื่อผู้เข้าสอบ</div>
                        <div id="drag-course" class="designer-tag tag-blue" data-id="course" style="color: <?= e($template['color_primary']) ?>">ชื่อโครงการ / หลักสูตร</div>
                        <div id="drag-date" class="designer-tag tag-green" data-id="date" style="color: <?= e($template['color_primary']) ?>">วันที่</div>
                        <div id="drag-certno" class="designer-tag tag-purple" data-id="certno" style="color: <?= e($template['color_primary']) ?>">เลขที่ใบเซอร์</div>
                        <div id="drag-score" class="designer-tag tag-orange" data-id="score" style="color: <?= e($template['color_primary']) ?>">คะแนน: 90%</div>
                        <div id="drag-qrcode" class="designer-tag tag-dark" data-id="qrcode" style="width: 100px; height: 100px;">QR</div>

                        <?php if(!empty($template['logo_path'])): ?>
                            <div id="drag-logo" class="designer-tag tag-white" data-id="logo" style="width: 80px; height: 80px; padding: 0;">
                                <img src="<?= e(BASE_URL . '/' . $template['logo_path']) ?>" class="w-full h-full object-contain">
                            </div>
                        <?php endif; ?>

                        <?php for($i=1;$i<=2;$i++): 
                            $sPath = $signatures[$i-1]['path'] ?? null;
                            if($sPath): ?>
                            <div id="drag-sign<?= $i ?>" class="designer-tag tag-white flex flex-col items-center justify-center gap-1" data-id="sign<?= $i ?>" style="width: 120px; min-height: 60px;">
                                <img src="<?= e(BASE_URL . '/' . $sPath) ?>" class="h-10 object-contain pointer-events-none">
                                <span class="text-[8px] text-slate-400 font-bold"><?= e($signatures[$i-1]['name'] ?? '') ?></span>
                            </div>
                        <?php endif; endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Properties Sidebar -->
            <aside class="w-72 flex flex-col gap-4 overflow-y-auto custom-scrollbar pl-1 flex-shrink-0">
                <div id="properties-panel" class="bg-white rounded-3xl border border-slate-100 p-6 space-y-6 shadow-sm">
                    <div class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em]">Properties</div>
                    
                    <?php 
                    $elements = [
                        'name' => ['label' => 'ชื่อผู้สอบ', 'color' => 'bg-primary-500'],
                        'course' => ['label' => 'ชื่อหลักสูตร', 'color' => 'bg-blue-500'],
                        'date' => ['label' => 'วันที่ออก', 'color' => 'bg-green-500'],
                        'certno' => ['label' => 'เลขที่ใบเซอร์', 'color' => 'bg-purple-500'],
                        'score' => ['label' => 'คะแนน', 'color' => 'bg-orange-500'],
                        'qrcode' => ['label' => 'QR Code', 'color' => 'bg-slate-800'],
                        'logo' => ['label' => 'โลโก้', 'color' => 'bg-white border'],
                        'sign1' => ['label' => 'ลายเซ็น 1', 'color' => 'bg-white border'],
                        'sign2' => ['label' => 'ลายเซ็น 2', 'color' => 'bg-white border'],
                    ];
                    foreach($elements as $id => $meta): ?>
                        <div id="prop-<?= $id ?>" class="space-y-4 pb-6 border-b border-slate-50 last:border-0 last:pb-0 hidden">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-md <?= $meta['color'] ?> shadow-sm"></span>
                                <span class="text-xs font-black text-slate-700 uppercase"><?= $meta['label'] ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase">X (มม.)</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-x" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[11px] font-mono font-bold text-slate-700">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase">Y (มม.)</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-y" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[11px] font-mono font-bold text-slate-700">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase"><?= in_array($id,['qrcode','logo','sign1','sign2'])?'ขนาด (มม.)':'ขนาดอักษร (pt)' ?></label>
                                    <input type="number" id="input-<?= $id ?>-size" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-3 py-2 text-[11px] font-bold text-slate-700">
                                </div>
                                <?php if(!in_array($id,['qrcode','logo','sign1','sign2'])): ?>
                                <div class="space-y-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase">จัดวาง</label>
                                    <select id="input-<?= $id ?>-align" onchange="updateFromInputs('<?= $id ?>')" class="w-full bg-slate-50 border-none rounded-xl px-2 py-2 text-[10px] font-bold text-slate-700 cursor-pointer">
                                        <option value="L">ชิดซ้าย</option>
                                        <option value="C" selected>กึ่งกลาง</option>
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
        .designer-wrapper { transform: scale(0.6); transform-origin: center center; display: flex; align-items: center; justify-content: center; }
        .designer-tag {
            position: absolute; z-index: 100; cursor: grab; user-select: none;
            padding: 4px 12px; background: #E87722; color: white; border-radius: 6px;
            font-weight: 800; border: 2.5px solid white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); white-space: nowrap;
            line-height: 1.1; text-align: center;
        }
        .designer-tag.tag-blue { background: #3B82F6; }
        .designer-tag.tag-green { background: #10B981; }
        .designer-tag.tag-purple { background: #8B5CF6; }
        .designer-tag.tag-orange { background: #f97316; }
        .designer-tag.tag-dark { background: #1F2937; }
        .designer-tag.tag-white { background: white; color: black; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .peer:checked ~ div .absolute { left: 1.5rem !important; }
        .peer:checked ~ div { background-color: #E87722 !important; }
    </style>

    <script>
    const studio = document.getElementById('designer-studio');
    const shield = document.getElementById('designer-shield');
    const container = document.getElementById('designer-container');
    const layoutText = document.getElementById('layout_json');
    const colorPicker = document.getElementById('color-picker');
    
    const MM_TO_PX = 3.7795275591;
    const DESIGN_SCALE = 0.6; 
    const PT_TO_PX = 1.333333;

    let layout = {};
    try { layout = JSON.parse(layoutText.value || '{}'); } catch(e) { layout = {}; }

    function updateColor() {
        const color = colorPicker.value;
        const tags = document.querySelectorAll('.designer-tag:not(.tag-dark):not(.tag-white)');
        tags.forEach(t => t.style.color = color);
    }

    function updateOrientation() {
        const orient = document.getElementById('orient-select').value;
        if (orient === 'L') {
            container.style.width = '1123px';
            container.style.height = '794px';
        } else {
            container.style.width = '794px';
            container.style.height = '1123px';
        }
    }

    function sync() {
        const currentLayout = {};
        document.querySelectorAll('.designer-tag').forEach(el => {
            const id = el.dataset.id;
            const x = parseFloat(document.getElementById(`input-${id}-x`).value || 0);
            const y = parseFloat(document.getElementById(`input-${id}-y`).value || 0);
            const size = parseFloat(document.getElementById(`input-${id}-size`).value || 20);
            
            currentLayout[id] = { x, y };
            if (['qrcode', 'logo', 'sign1', 'sign2'].includes(id)) {
                currentLayout[id].w = size;
                currentLayout[id].h = size;
            } else {
                currentLayout[id].size = size;
                currentLayout[id].align = document.getElementById(`input-${id}-align`).value;
            }
        });
        layoutText.value = JSON.stringify(currentLayout, null, 4);
    }

    function init() {
        document.querySelectorAll('.designer-tag').forEach(el => {
            const id = el.dataset.id;
            if (!layout[id]) {
                layout[id] = { x: 50, y: 50, size: 20, align: 'C' };
                if (['qrcode','logo','sign1','sign2'].includes(id)) { layout[id].w = 28; layout[id].h = 28; }
            }

            const align = layout[id].align || 'C';
            if (align === 'C') el.style.transform = 'translate(-50%, -50%)';
            else if (align === 'R') el.style.transform = 'translate(-100%, -50%)';
            else el.style.transform = 'translate(0, -50%)';

            el.style.left = (layout[id].x * MM_TO_PX) + 'px';
            el.style.top = (layout[id].y * MM_TO_PX) + 'px';
            
            const sizeInput = document.getElementById(`input-${id}-size`);
            const alignInput = document.getElementById(`input-${id}-align`);
            
            if (['qrcode', 'logo', 'sign1', 'sign2'].includes(id)) {
                el.style.width = (layout[id].w * MM_TO_PX) + 'px';
                if(id !== 'sign1' && id !== 'sign2') el.style.height = (layout[id].w * MM_TO_PX) + 'px';
                sizeInput.value = layout[id].w;
            } else {
                el.style.fontSize = (layout[id].size * PT_TO_PX) + 'px';
                sizeInput.value = layout[id].size;
                if(alignInput) alignInput.value = layout[id].align || 'C';
            }
            
            document.getElementById(`input-${id}-x`).value = layout[id].x;
            document.getElementById(`input-${id}-y`).value = layout[id].y;
            document.getElementById(`prop-${id}`).classList.remove('hidden');
                
            drag(el, id);
        });
        sync();
        updateColor();
        updateOrientation();
        
        shield.style.opacity = '0';
        setTimeout(() => { shield.style.display = 'none'; studio.style.opacity = '1'; }, 500);
    }

    function updateFromInputs(id) {
        const el = document.getElementById('drag-' + id);
        const x = parseFloat(document.getElementById(`input-${id}-x`).value || 0);
        const y = parseFloat(document.getElementById(`input-${id}-y`).value || 0);
        const size = parseFloat(document.getElementById(`input-${id}-size`).value || 20);
        const alignInput = document.getElementById(`input-${id}-align`);
        
        el.style.left = (x * MM_TO_PX) + 'px';
        el.style.top = (y * MM_TO_PX) + 'px';
        
        if (['qrcode', 'logo', 'sign1', 'sign2'].includes(id)) {
            el.style.width = (size * MM_TO_PX) + 'px';
            if(id !== 'sign1' && id !== 'sign2') el.style.height = (size * MM_TO_PX) + 'px';
        } else {
            el.style.fontSize = (size * PT_TO_PX) + 'px';
            const align = alignInput.value;
            if (align === 'C') el.style.transform = 'translate(-50%, -50%)';
            else if (align === 'R') el.style.transform = 'translate(-100%, -50%)';
            else el.style.transform = 'translate(0, -50%)';
        }
        sync();
    }

    function drag(el, id) {
        let x2 = 0, y2 = 0;
        el.onmousedown = (e) => {
            e.preventDefault();
            x2 = e.clientX; y2 = e.clientY;
            document.onmousemove = (ev) => {
                let dx = ev.clientX - x2; let dy = ev.clientY - y2;
                let moveX = dx / DESIGN_SCALE; let moveY = dy / DESIGN_SCALE;
                let currentLeft = parseFloat(el.style.left) || 0;
                let currentTop = parseFloat(el.style.top) || 0;
                let newLeft = currentLeft + moveX;
                let newTop = currentTop + moveY;
                el.style.left = newLeft + "px"; el.style.top = newTop + "px";
                x2 = ev.clientX; y2 = ev.clientY;
                document.getElementById(`input-${id}-x`).value = (newLeft / MM_TO_PX).toFixed(1);
                document.getElementById(`input-${id}-y`).value = (newTop / MM_TO_PX).toFixed(1);
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
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                layout = {
                    "name":   { "x": 148.5, "y": 80,  "align": "C", "size": 38 },
                    "course": { "x": 148.5, "y": 110, "align": "C", "size": 22 },
                    "date":   { "x": 148.5, "y": 130, "align": "C", "size": 16 },
                    "certno": { "x": 230,   "y": 170, "align": "R", "size": 11 },
                    "score":  { "x": 148.5, "y": 120, "align": "C", "size": 18 },
                    "qrcode": { "x": 240,   "y": 140, "w": 28 }
                };
                init();
            }
        });
    }

    window.onload = () => setTimeout(init, 500);
    </script>
<?php endif; ?>
