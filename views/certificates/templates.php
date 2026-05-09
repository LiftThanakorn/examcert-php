<?php
$templateMode = $templateMode ?? 'list';
$errors = $errors ?? [];
$template = array_merge(templateDefaults(), $template ?? []);
?>

<?php if ($templateMode === 'list'): ?>
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">คลังเทมเพลตเกียรติบัตร</h2>
            <p class="text-sm text-gray-400 mt-1">จัดการเลย์เอาต์เกียรติบัตรสำหรับโครงการของคุณ</p>
        </div>
        <a href="<?= e(BASE_URL) ?>/admin/certificates/template-create.php" class="inline-flex items-center px-6 py-3 bg-primary-400 hover:bg-primary-500 text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-orange-100">
            <i class="fas fa-plus mr-2"></i> สร้างเทมเพลตใหม่
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] font-bold text-gray-400 tracking-widest uppercase">
                        <th class="px-8 py-5">ชื่อเทมเพลต</th>
                        <th class="px-8 py-5">แนววาง</th>
                        <th class="px-8 py-5 text-center">สถานะ</th>
                        <th class="px-8 py-5 text-right">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    <?php foreach ($templates as $item): ?>
                        <tr class="group hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5 font-bold text-gray-700"><?= e($item['name']) ?></td>
                            <td class="px-8 py-5">
                                <span class="text-xs text-gray-500"><?= $item['orientation'] === 'L' ? 'แนวนอน' : 'แนวตั้ง' ?></span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold <?= (int)$item['is_active'] === 1 ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-400' ?>">
                                    <?= (int)$item['is_active'] === 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน' ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= e(BASE_URL) ?>/admin/certificates/template-edit.php?id=<?= (int)$item['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-100 shadow-sm text-gray-400 hover:text-primary-400 transition-all" title="แก้ไข">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?= (int)$item['id'] ?>, '<?= e($item['name']) ?>')" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-100 shadow-sm text-gray-400 hover:text-red-500 transition-all" title="ลบ">
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
    <!-- Fullscreen Designer (No-Scroll) -->
    <div class="h-[calc(100vh-140px)] flex flex-col gap-4 overflow-hidden">
        <!-- Studio Header -->
        <div class="flex items-center justify-between px-2 flex-shrink-0">
            <div class="flex items-center gap-4">
                <a href="<?= e(BASE_URL) ?>/admin/certificates/templates.php" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <h2 class="text-lg font-bold text-gray-800"><?= e($pageTitle) ?></h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="loadDefaultLayout()" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-primary-400 transition-colors">
                    <i class="fas fa-undo mr-1.5"></i> คืนค่าเริ่มต้น
                </button>
                <button type="submit" form="main-form" class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all shadow-xl">
                    บันทึกเทมเพลต
                </button>
            </div>
        </div>

        <div class="flex-1 flex gap-4 min-h-0">
            <!-- Left Sidebar -->
            <aside class="w-64 flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-1 flex-shrink-0">
                <form id="main-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="contents">
                    <?= csrfField() ?>
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4 shadow-sm">
                        <div class="text-[10px] font-bold text-primary-400 uppercase tracking-widest">การตั้งค่าหลัก</div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">ชื่อเทมเพลต</label>
                            <input name="name" required value="<?= e($template['name']) ?>" class="w-full bg-gray-50 border-none rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-400/20" placeholder="Template Name">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">รูปพื้นหลัง</label>
                            <div class="relative group">
                                <div class="w-full aspect-video bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative">
                                    <img id="sidebar-preview" src="<?= !empty($template['bg_image']) ? e(BASE_URL . '/' . $template['bg_image']) : '' ?>" class="w-full h-full object-cover <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                                    <i id="sidebar-placeholder" class="fas fa-image text-xl text-gray-200 <?= !empty($template['bg_image']) ? 'hidden' : '' ?>"></i>
                                </div>
                                <input type="file" name="bg_image" id="bg-input" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-3 shadow-sm">
                        <div class="text-[10px] font-bold text-primary-400 uppercase tracking-widest">Visibility</div>
                        <?php 
                        $opts = [
                            'show_name' => ['label' => 'ชื่อผู้สอบ'],
                            'show_course' => ['label' => 'ชื่อหลักสูตร'],
                            'show_certno' => ['label' => 'เลขที่ใบเซอร์'],
                            'show_qr' => ['label' => 'QR Code'],
                            'show_date' => ['label' => 'วันที่'],
                            'is_active' => ['label' => 'เปิดใช้งาน']
                        ];
                        foreach($opts as $k => $v): ?>
                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-xs font-bold text-gray-400"><?= $v['label'] ?></span>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="<?= $k ?>" value="1" <?= !empty($template[$k]) ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-7 h-4 bg-gray-100 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-primary-400"></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <textarea name="layout_json" id="layout_json" class="hidden"><?= e($template['layout_json'] ?? '') ?></textarea>
                    <input type="hidden" name="orientation" value="<?= e($template['orientation']) ?>">
                </form>
            </aside>

            <!-- Center Canvas (Responsive and No-Scroll) -->
            <div class="flex-1 bg-gray-50 rounded-3xl border border-gray-100 flex items-center justify-center relative overflow-hidden p-8 shadow-inner">
                <div id="designer-container" class="relative bg-white shadow-2xl transition-all" 
                     style="width: <?= $template['orientation'] === 'L' ? '800px' : '566px' ?>; aspect-ratio: <?= $template['orientation'] === 'L' ? '1.414/1' : '1/1.414' ?>; max-width: 100%; max-height: 100%;">
                    <img id="designer-bg" src="<?= !empty($template['bg_image']) ? e(BASE_URL . '/' . $template['bg_image']) : '' ?>" class="w-full h-full block select-none pointer-events-none <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                    <div id="designer-placeholder" class="w-full h-full flex items-center justify-center text-gray-200 border-2 border-dashed border-gray-100 <?= !empty($template['bg_image']) ? 'hidden' : '' ?>">
                        <p class="font-bold text-xs uppercase tracking-widest italic">Template Background</p>
                    </div>

                    <!-- Draggable Elements -->
                    <div id="drag-name" class="designer-tag" data-id="name">ชื่อผู้เข้าสอบ</div>
                    <div id="drag-course" class="designer-tag tag-blue" data-id="course">ชื่อโครงการ / หลักสูตร</div>
                    <div id="drag-date" class="designer-tag tag-green" data-id="date">วันที่</div>
                    <div id="drag-certno" class="designer-tag tag-purple" data-id="certno">เลขที่ใบเซอร์</div>
                    <div id="drag-qrcode" class="designer-tag tag-dark" data-id="qrcode" style="width: 50px; height: 50px;">QR</div>
                </div>
            </div>

            <!-- Right Sidebar: Properties -->
            <aside class="w-64 flex flex-col gap-4 overflow-y-auto custom-scrollbar pl-1 flex-shrink-0">
                <div id="properties-panel" class="bg-white rounded-2xl border border-gray-100 p-5 space-y-6 shadow-sm">
                    <div class="text-[10px] font-bold text-primary-400 uppercase tracking-widest">Properties</div>
                    
                    <?php 
                    $elements = [
                        'name' => ['label' => 'ชื่อผู้สอบ', 'color' => 'bg-primary-400'],
                        'course' => ['label' => 'ชื่อโครงการ', 'color' => 'bg-blue-400'],
                        'date' => ['label' => 'วันที่', 'color' => 'bg-green-400'],
                        'certno' => ['label' => 'เลขที่', 'color' => 'bg-purple-400'],
                        'qrcode' => ['label' => 'QR Code', 'color' => 'bg-gray-800'],
                    ];
                    foreach($elements as $id => $meta): ?>
                        <div class="space-y-3 pb-5 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full <?= $meta['color'] ?>"></span>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-wide"><?= $meta['label'] ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">X Pos (mm)</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-x" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded-lg px-2 py-2 text-[10px] font-mono">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Y Pos (mm)</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-y" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded-lg px-2 py-2 text-[10px] font-mono">
                                </div>
                            </div>
                            <?php if ($id !== 'qrcode'): ?>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Font Size</label>
                                    <input type="number" id="input-<?= $id ?>-size" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded-lg px-2 py-2 text-[10px]">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Align</label>
                                    <select id="input-<?= $id ?>-align" onchange="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded-lg px-1 py-2 text-[10px]">
                                        <option value="L">ชิดซ้าย</option>
                                        <option value="C">กึ่งกลาง</option>
                                        <option value="R">ชิดขวา</option>
                                    </select>
                                </div>
                            </div>
                            <?php else: ?>
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">QR Width (mm)</label>
                                    <input type="number" id="input-<?= $id ?>-size" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded-lg px-2 py-2 text-[10px]">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </div>

    <style>
        .designer-tag {
            position: absolute; z-index: 100; cursor: grab; user-select: none;
            padding: 4px 8px; background: #E87722; color: white; border-radius: 4px;
            font-size: 10px; font-weight: bold; border: 1px solid rgba(255,255,255,0.5); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); white-space: nowrap;
            /* Key fix: Transform for accurate anchoring */
            transform-origin: center center;
        }
        .designer-tag.tag-blue { background: #3B82F6; }
        .designer-tag.tag-green { background: #10B981; }
        .designer-tag.tag-purple { background: #8B5CF6; }
        .designer-tag.tag-dark { background: #1F2937; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    </style>

    <script>
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
            
            // X and Y based on anchor point (Center of the element if 'C')
            let xOffset = 0, yOffset = el.offsetHeight / 2;
            const align = layout[id].align || 'L';
            if (align === 'C') xOffset = el.offsetWidth / 2;
            else if (align === 'R') xOffset = el.offsetWidth;

            layout[id].x = parseFloat(((parseFloat(el.style.left) + xOffset) * ratio).toFixed(2));
            layout[id].y = parseFloat(((parseFloat(el.style.top) + yOffset) * ratio).toFixed(2));

            // Update inputs
            if (document.getElementById(`input-${id}-x`)) {
                document.getElementById(`input-${id}-x`).value = layout[id].x;
                document.getElementById(`input-${id}-y`).value = layout[id].y;
                document.getElementById(`input-${id}-size`).value = layout[id].size || layout[id].w || 20;
                if (document.getElementById(`input-${id}-align`)) 
                    document.getElementById(`input-${id}-align`).value = layout[id].align || 'L';
            }
        });
        layoutText.value = JSON.stringify(layout, null, 4);
    }

    function init() {
        const ratio = container.offsetWidth / A4_W;
        Object.keys(layout).forEach(id => {
            const el = document.getElementById('drag-' + id);
            if (!el) return;

            // Apply transformations
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
        init(); // Re-render
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

    window.onload = () => setTimeout(init, 300);
    </script>
<?php endif; ?>
