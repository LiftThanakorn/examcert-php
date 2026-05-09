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

    <form id="delete-form" method="post" action="<?= e(BASE_URL) ?>/admin/certificates/template-delete.php" class="hidden">
        <?= csrfField() ?>
        <input type="hidden" name="id" id="delete-id">
    </form>

    <script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'ยืนยันการลบเทมเพลต?',
            text: `คุณต้องการลบเทมเพลต "${name}" ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยันการลบ',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                popup: 'rounded-2xl font-sans',
                confirmButton: '!bg-red-500 !px-8 !py-3 !rounded-xl !text-sm !font-bold',
                cancelButton: '!bg-white !text-gray-500 !px-8 !py-3 !rounded-xl !text-sm !font-bold'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-id').value = id;
                document.getElementById('delete-form').submit();
            }
        });
    }
    </script>

<?php else: ?>
    <div class="h-[calc(100vh-120px)] flex flex-col gap-4">
        <!-- Studio Header -->
        <div class="flex items-center justify-between px-2">
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
                <button type="submit" form="main-form" class="px-6 py-2.5 bg-primary-400 hover:bg-primary-500 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-orange-100">
                    บันทึกเทมเพลต
                </button>
            </div>
        </div>

        <div class="flex-1 flex gap-4 min-h-0">
            <!-- Left Sidebar: Assets & Global -->
            <div class="w-64 flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-1">
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
                                    <button type="button" id="remove-img-btn" onclick="clearImage()" class="absolute top-1.5 right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[9px] opacity-0 group-hover:opacity-100 transition-opacity shadow-lg <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="file" name="bg_image" id="bg-input" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                                <input type="hidden" name="remove_bg" id="remove-bg-input" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4 shadow-sm">
                        <div class="text-[10px] font-bold text-primary-400 uppercase tracking-widest">รูปแบบ</div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="orientation" value="L" <?= $template['orientation'] === 'L' ? 'checked' : '' ?> class="sr-only peer">
                                <div class="py-2 border-2 border-gray-50 rounded-lg bg-gray-50 text-center peer-checked:border-primary-400 peer-checked:bg-primary-50/50 text-[10px] font-bold text-gray-400 peer-checked:text-primary-600 transition-all">แนวนอน</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="orientation" value="P" <?= $template['orientation'] === 'P' ? 'checked' : '' ?> class="sr-only peer">
                                <div class="py-2 border-2 border-gray-50 rounded-lg bg-gray-50 text-center peer-checked:border-primary-400 peer-checked:bg-primary-50/50 text-[10px] font-bold text-gray-400 peer-checked:text-primary-600 transition-all">แนวตั้ง</div>
                            </label>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">ฟอนต์หลัก</label>
                            <select name="font_name" id="font-selector" class="w-full bg-gray-50 border-none rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-400/20" onchange="updateDesignerFont()">
                                <option value="Sarabun">Sarabun (ทางการ)</option>
                                <option value="Noto Sans Thai">Noto Sans Thai</option>
                                <option value="Kanit">Kanit</option>
                                <option value="Prompt">Prompt</option>
                                <option value="Mitr">Mitr</option>
                                <option value="Chakra Petch">Chakra Petch</option>
                                <option value="Srisakdi">Srisakdi (หรูหรา)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">สีหลัก</span>
                            <input type="color" name="color_primary" value="<?= e($template['color_primary']) ?>" class="w-7 h-7 rounded border-none bg-transparent cursor-pointer">
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-3 shadow-sm">
                        <?php 
                        $opts = [
                            'show_qr' => ['label' => 'QR Code', 'icon' => 'fa-qrcode'],
                            'show_date' => ['label' => 'วันที่', 'icon' => 'fa-calendar'],
                            'show_score' => ['label' => 'คะแนน', 'icon' => 'fa-star'],
                            'is_active' => ['label' => 'เปิดใช้งาน', 'icon' => 'fa-check']
                        ];
                        foreach($opts as $k => $v): ?>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="text-xs font-bold text-gray-400"><?= $v['label'] ?></span>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="<?= $k ?>" value="1" <?= !empty($template[$k]) ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-7 h-4 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-primary-400"></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Additional Assets (Logo & Signs) -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-5 shadow-sm">
                        <div class="text-[10px] font-bold text-primary-400 uppercase tracking-widest">องค์ประกอบภาพ</div>
                        
                        <!-- Agency Logo -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase">โลโก้หน่วยงาน</label>
                            <?php if (!empty($template['logo_path'])): ?>
                                <div class="relative group w-20 h-20 bg-gray-50 rounded-lg border border-gray-100 p-2 flex items-center justify-center overflow-hidden mb-2">
                                    <img src="<?= e(BASE_URL . '/' . $template['logo_path']) ?>" class="max-w-full max-h-full object-contain">
                                    <button type="button" onclick="removeImage('logo')" class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[9px] opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo_image" accept="image/*" class="w-full text-xxs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 cursor-pointer">
                            <input type="hidden" name="remove_logo" id="remove-logo-input" value="0">
                        </div>

                        <!-- Signatures -->
                        <?php 
                        $signatures = json_decode((string)($template['signature_paths'] ?? '[]'), true);
                        for ($i = 1; $i <= 2; $i++): 
                            $sign = $signatures[$i-1] ?? ['path' => '', 'name' => ''];
                        ?>
                        <div class="space-y-2 pt-2 border-t border-gray-50">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase">ลายเซ็นที่ <?= $i ?></label>
                            <?php if (!empty($sign['path'])): ?>
                                <div class="relative group w-full h-14 bg-gray-50 rounded-lg border border-gray-100 p-2 flex items-center justify-center overflow-hidden mb-2">
                                    <img src="<?= e(BASE_URL . '/' . $sign['path']) ?>" class="max-w-full max-h-full object-contain">
                                    <button type="button" onclick="removeImage('sign_<?= $i ?>')" class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[9px] opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <div class="space-y-2">
                                <input type="file" name="sign_image_<?= $i ?>" accept="image/*" class="w-full text-xxs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 cursor-pointer">
                                <input type="text" name="sign_name_<?= $i ?>" value="<?= e($sign['name']) ?>" placeholder="ชื่อ-นามสกุล ผู้ลงนาม" class="w-full h-8 px-3 text-[10px] bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-primary-400/20 outline-none">
                            </div>
                            <input type="hidden" name="remove_sign_<?= $i ?>" id="remove-sign-<?= $i ?>-input" value="0">
                        </div>
                        <?php endfor; ?>
                    </div>
                    <textarea name="layout_json" id="layout_json" class="hidden"><?= e($template['layout_json'] ?? '') ?></textarea>
                </form>
            </div>

            <!-- Center: Designer Canvas -->
            <div class="flex-1 bg-gray-50 rounded-3xl border border-gray-200/50 flex items-center justify-center relative overflow-hidden p-6">
                <div id="designer-container" class="relative bg-white shadow-xl transition-all" 
                     style="width: <?= $template['orientation'] === 'L' ? '700px' : '495px' ?>; aspect-ratio: <?= $template['orientation'] === 'L' ? '1.414/1' : '1/1.414' ?>;">
                    <img id="designer-bg" src="<?= !empty($template['bg_image']) ? e(BASE_URL . '/' . $template['bg_image']) : '' ?>" class="w-full h-full block select-none pointer-events-none rounded-sm <?= empty($template['bg_image']) ? 'hidden' : '' ?>">
                    <div id="designer-placeholder" class="w-full h-full flex items-center justify-center text-gray-200 border-2 border-dashed border-gray-100 <?= !empty($template['bg_image']) ? 'hidden' : '' ?>">
                        <p class="font-bold text-xs uppercase tracking-widest">Canvas Area</p>
                    </div>

                    <div id="drag-name" class="designer-tag">ชื่อผู้เข้าสอบ</div>
                    <div id="drag-course" class="designer-tag tag-blue">ชื่อโครงการ / หลักสูตร</div>
                    <div id="drag-date" class="designer-tag tag-green">วันที่</div>
                    <div id="drag-certno" class="designer-tag tag-purple">เลขที่ใบเซอร์</div>
                    <div id="drag-qrcode" class="designer-tag tag-dark" style="width: 50px; height: 50px;">QR</div>
                    
                    <!-- New Draggable Assets -->
                    <div id="drag-logo" class="designer-tag tag-orange flex items-center justify-center overflow-hidden p-0" style="width: 60px; height: 60px;">
                        <?php if (!empty($template['logo_path'])): ?>
                            <img src="<?= e(BASE_URL . '/' . $template['logo_path']) ?>" class="max-w-full max-h-full object-contain">
                        <?php else: ?>
                            <span class="text-[8px]">LOGO</span>
                        <?php endif; ?>
                    </div>

                    <?php for($i=1;$i<=2;$i++): ?>
                        <div id="drag-sign<?= $i ?>" class="designer-tag tag-orange flex items-center justify-center overflow-hidden p-0" style="width: 80px; height: 40px;">
                            <?php 
                            $sign = $signatures[$i-1] ?? ['path' => ''];
                            if (!empty($sign['path'])): 
                            ?>
                                <img src="<?= e(BASE_URL . '/' . $sign['path']) ?>" class="max-w-full max-h-full object-contain">
                            <?php else: ?>
                                <span class="text-[8px]">SIGN <?= $i ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Right Sidebar: Properties Panel -->
            <div class="w-64 flex flex-col gap-4 overflow-y-auto custom-scrollbar pl-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-6 shadow-sm">
                    <div class="text-[10px] font-bold text-primary-400 uppercase tracking-widest">Properties</div>
                    
                    <?php 
                    $elements = [
                        'name' => ['label' => 'ชื่อผู้สอบ', 'color' => 'bg-primary-400'],
                        'course' => ['label' => 'ชื่อโครงการ', 'color' => 'bg-blue-400'],
                        'date' => ['label' => 'วันที่', 'color' => 'bg-green-400'],
                        'certno' => ['label' => 'เลขที่', 'color' => 'bg-purple-400'],
                        'qrcode' => ['label' => 'QR Code', 'color' => 'bg-gray-800'],
                        'sign1' => ['label' => 'ลายเซ็น 1', 'color' => 'bg-orange-400'],
                        'sign2' => ['label' => 'ลายเซ็น 2', 'color' => 'bg-orange-500'],
                    ];
                    foreach($elements as $id => $meta): ?>
                        <div class="space-y-2 pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full <?= $meta['color'] ?>"></span>
                                <span class="text-[10px] font-bold text-gray-600 uppercase"><?= $meta['label'] ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">X Pos</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-x" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded px-2 py-1.5 text-[10px] font-mono focus:ring-1 focus:ring-primary-400/20">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">Y Pos</label>
                                    <input type="number" step="0.1" id="input-<?= $id ?>-y" oninput="updateFromInputs('<?= $id ?>')" class="w-full bg-gray-50 border-none rounded px-2 py-1.5 text-[10px] font-mono focus:ring-1 focus:ring-primary-400/20">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[8px] font-bold text-gray-400 uppercase mb-1">
                                        <?= $id === 'qrcode' ? 'Width (mm)' : (str_contains($id, 'sign') ? 'Label Y (mm)' : 'Font Size (pt)') ?>
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" id="input-<?= $id ?>-size" oninput="updateFromInputs('<?= $id ?>')" class="flex-1 bg-gray-50 border-none rounded px-2 py-1.5 text-[10px] font-mono focus:ring-1 focus:ring-primary-400/20">
                                        <?php if (!str_contains($id, 'sign')): ?>
                                        <div class="flex gap-1">
                                            <button type="button" onclick="adjustSize('<?= $id ?>', -1)" class="w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center text-[10px]">-</button>
                                            <button type="button" onclick="adjustSize('<?= $id ?>', 1)" class="w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center text-[10px]">+</button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
        .designer-tag {
            position: absolute; z-index: 100; cursor: grab; user-select: none;
            padding: 0; background: #E87722; color: white; border-radius: 4px;
            font-size: 10px; font-weight: bold; border: 1px solid white; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            line-height: 1.2; display: flex; items-center; justify-content: center;
            box-sizing: content-box; /* Maintain text size regardless of border */
            white-space: nowrap;
        }
        .designer-tag:active { cursor: grabbing; border-color: #3B82F6; }
        .designer-tag.tag-blue { background: #3B82F6; }
        .designer-tag.tag-green { background: #10B981; }
        .designer-tag.tag-purple { background: #8B5CF6; }
        .designer-tag.tag-dark { background: #1F2937; }
        .designer-tag.tag-orange { background: #F59E0B; }
        
        /* Make text transparent-ish background when active to see the certificate under it */
        .designer-tag { opacity: 0.9; }
        .designer-tag:hover { opacity: 1; transform: scale(1.02); transition: all 0.2s; }
    </style>

    <script>
    const container = document.getElementById('designer-container');
    const layoutTextarea = document.getElementById('layout_json');
    const A4_W = <?= $template['orientation'] === 'L' ? '297' : '210' ?>;

    let layout = {};
    try { layout = JSON.parse(layoutTextarea.value || '{}'); } catch(e) { layout = {}; }

    function removeImage(type) {
        if (type === 'logo') {
            document.getElementById('remove-logo-input').value = "1";
        } else if (type.startsWith('sign_')) {
            const num = type.split('_')[1];
            document.getElementById(`remove-sign-${num}-input`).value = "1";
        } else if (type === 'bg') {
            clearImage();
        }
        Swal.fire({
            icon: 'info',
            title: 'เตรียมลบรูปภาพ',
            text: 'รูปจะถูกลบหลังจากคุณกด "บันทึกเทมเพลต"',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function clearImage() {
        document.getElementById('sidebar-preview').classList.add('hidden');
        document.getElementById('sidebar-placeholder').classList.remove('hidden');
        document.getElementById('designer-bg').classList.add('hidden');
        document.getElementById('designer-placeholder').classList.remove('hidden');
        document.getElementById('bg-input').value = '';
        document.getElementById('remove-bg-input').value = "1";
        document.getElementById('remove-img-btn').classList.add('hidden');
    }

    document.getElementById('bg-input').onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                document.getElementById('sidebar-preview').src = ev.target.result;
                document.getElementById('sidebar-preview').classList.remove('hidden');
                document.getElementById('sidebar-placeholder').classList.add('hidden');
                document.getElementById('designer-bg').src = ev.target.result;
                document.getElementById('designer-bg').classList.remove('hidden');
                document.getElementById('designer-placeholder').classList.add('hidden');
                document.getElementById('remove-bg-input').value = "0";
                document.getElementById('remove-img-btn').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    };

    function updateDesignerFont() {
        const font = document.getElementById('font-selector').value;
        ['name', 'course', 'date', 'certno'].forEach(id => {
            const el = document.getElementById('drag-' + id);
            if (el) el.style.fontFamily = `'${font}', sans-serif`;
        });
    }

    function sync() {
        const elements = ['name', 'course', 'date', 'certno', 'qrcode', 'logo', 'sign1', 'sign2'];
        const ratio = A4_W / container.offsetWidth;
        elements.forEach(id => {
            const el = document.getElementById('drag-' + id);
            if (!el) return;
            if (!layout[id]) layout[id] = { size: (id==='name'?38:18), align: 'C', bold: (id==='name') };
            
            const xMm = parseFloat((parseFloat(el.style.left) * ratio).toFixed(2));
            const yMm = parseFloat((parseFloat(el.style.top) * ratio).toFixed(2));
            
            layout[id].x = xMm;
            layout[id].y = yMm;

            if (document.getElementById(`input-${id}-x`)) {
                document.getElementById(`input-${id}-x`).value = xMm;
                document.getElementById(`input-${id}-y`).value = yMm;
                document.getElementById(`input-${id}-size`).value = (id === 'qrcode' || id === 'logo') ? (layout[id].w || 28) : (id.startsWith('sign') ? (layout[id].label_y || 20) : (layout[id].size || 20));
            }
        });
        layoutTextarea.value = JSON.stringify(layout, null, 4);
    }

    function updateFromInputs(id) {
        const ratio = container.offsetWidth / A4_W;
        // Accurate Font Scaling: points to pixels based on canvas width
        // A4 width in points is approx 842pt (landscape) or 595pt (portrait)
        const A4_PT_W = <?= $template['orientation'] === 'L' ? '842' : '595' ?>;
        const fontRatio = container.offsetWidth / A4_PT_W;

        const el = document.getElementById('drag-' + id);
        const x = parseFloat(document.getElementById(`input-${id}-x`).value || 0);
        const y = parseFloat(document.getElementById(`input-${id}-y`).value || 0);
        const size = parseFloat(document.getElementById(`input-${id}-size`).value || 20);

        if (!layout[id]) layout[id] = {};
        layout[id].x = x;
        layout[id].y = y;
        
        if (id === 'qrcode' || id === 'logo') {
            layout[id].w = size; layout[id].h = size;
            el.style.width = (size * ratio) + 'px';
            el.style.height = (size * ratio) + 'px';
        } else if (id.startsWith('sign')) {
            layout[id].label_y = size;
        } else {
            layout[id].size = size;
            el.style.fontSize = (size * fontRatio) + 'px'; // Fixed accurate font scaling
        }

        el.style.left = (x * ratio) + 'px';
        el.style.top = (y * ratio) + 'px';
        layoutTextarea.value = JSON.stringify(layout, null, 4);
    }

    function init() {
        updateDesignerFont();
        const ratio = container.offsetWidth / A4_W;
        const A4_PT_W = <?= $template['orientation'] === 'L' ? '842' : '595' ?>;
        const fontRatio = container.offsetWidth / A4_PT_W;

        ['name', 'course', 'date', 'certno', 'qrcode', 'logo', 'sign1', 'sign2'].forEach(id => {
            const el = document.getElementById('drag-' + id);
            if (!el || !layout[id]) return;
            el.style.left = (layout[id].x * ratio) + 'px';
            el.style.top = (layout[id].y * ratio) + 'px';
            if (id === 'qrcode' || id === 'logo') {
                el.style.width = ((layout[id].w || (id==='logo'?40:28)) * ratio) + 'px';
                el.style.height = ((layout[id].h || (id==='logo'?40:28)) * ratio) + 'px';
            } else if (!id.startsWith('sign')) {
                el.style.fontSize = ((layout[id].size || 20) * fontRatio) + 'px'; // Fixed initial font size
            }
            drag(el);
        });
        sync();
    }

    function drag(el) {
        let x1=0, y1=0, x2=0, y2=0;
        el.onmousedown = (e) => {
            e.preventDefault();
            x2 = e.clientX; y2 = e.clientY;
            document.onmouseup = () => { document.onmouseup = document.onmousemove = null; sync(); };
            document.onmousemove = (e) => {
                x1 = x2 - e.clientX; y1 = y2 - e.clientY;
                x2 = e.clientX; y2 = e.clientY;
                let nx = el.offsetLeft - x1, ny = el.offsetTop - y1;
                nx = Math.max(0, Math.min(nx, container.offsetWidth - el.offsetWidth));
                ny = Math.max(0, Math.min(ny, container.offsetHeight - el.offsetHeight));
                el.style.left = nx + "px"; el.style.top = ny + "px";
                sync();
            };
        };
    }

    function adjustSize(id, delta) {
        const input = document.getElementById(`input-${id}-size`);
        input.value = parseFloat(input.value || 0) + delta;
        updateFromInputs(id);
    }

    function loadDefaultLayout() {
        Swal.fire({
            title: 'ยืนยันการคืนค่าเริ่มต้น?',
            text: 'ตำแหน่งและขนาดของทุกองค์ประกอบจะถูกรีเซ็ต',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                layout = {
                    "name":   { "x": 148.5, "y": 80,  "align": "C", "size": 38, "bold": true },
                    "course": { "x": 148.5, "y": 110, "align": "C", "size": 22, "bold": false },
                    "date":   { "x": 148.5, "y": 130, "align": "C", "size": 16, "bold": false },
                    "certno": { "x": 230,   "y": 170, "align": "R", "size": 11, "bold": false },
                    "qrcode": { "x": 240,   "y": 140, "w": 28, "h": 28 },
                    "logo":   { "x": 20,    "y": 20,  "w": 30, "h": 30 },
                    "sign1":  { "x": 100,   "y": 160, "label_y": 20 },
                    "sign2":  { "x": 180,   "y": 160, "label_y": 20 }
                };
                layoutTextarea.value = JSON.stringify(layout, null, 4);
                init();
                Swal.fire({ icon: 'success', title: 'รีเซ็ตเรียบร้อย', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            }
        });
    }

    window.onload = () => { setTimeout(init, 100); };
    window.onresize = init;
    </script>
<?php endif; ?>
