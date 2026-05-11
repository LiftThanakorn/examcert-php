<?php
/** @var array $templates */
/** @var string $pageTitle */
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">คลังเทมเพลตเกียรติบัตร</h2>
        <p class="text-sm text-slate-400 mt-1 font-medium">จัดการรูปแบบและองค์ประกอบของใบประกาศเกียรติบัตร</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/certificates/template/create" class="inline-flex items-center px-6 py-3 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-2xl transition-all shadow-xl">
        <i class="fas fa-plus mr-2 text-orange-400"></i> สร้างเทมเพลตด้วย Builder
    </a>
</div>

<div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl overflow-hidden fade-up-1">
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
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?= e(BASE_URL) ?>/admin/certificates/template/edit?id=<?= (int)$item['id'] ?>" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-primary-500 hover:border-primary-200 transition-all"
                                   title="แก้ไขใน Builder">
                                    <i class="fas fa-magic text-xs"></i>
                                </a>
                                <button type="button" 
                                        onclick="confirmDeleteTemplate(<?= (int)$item['id'] ?>, '<?= e($item['name']) ?>')" 
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-red-500 hover:border-red-200 transition-all"
                                        title="ลบเทมเพลต">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$templates): ?>
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-slate-400 font-medium">ยังไม่มีเทมเพลตในคลัง</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDeleteTemplate(id, name) {
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: `คุณต้องการลบเทมเพลต "${name}" ใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ลบข้อมูล',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // มักจะส่งไปที่ delete route หรือ API
            window.location.href = `<?= e(BASE_URL) ?>/admin/certificates/template/delete?id=${id}`;
        }
    });
}
</script>
