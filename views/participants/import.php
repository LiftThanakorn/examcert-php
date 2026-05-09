<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">นำเข้ารายชื่อผู้มีสิทธิ์สอบ</h2>
        <p class="text-sm text-gray-400 mt-0.5">โครงการ: <?= e($project['name']) ?></p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-xl transition-colors shadow-sm">
        <i class="fas fa-arrow-left mr-2 text-gray-400"></i> ย้อนกลับ
    </a>
</div>

<div class="grid gap-8 lg:grid-cols-3">
    <!-- Import Form -->
    <div class="lg:col-span-1">
        <section class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-import mr-2 text-primary-400"></i> เลือกไฟล์ข้อมูล
            </h3>
            <div class="space-y-6">
                <div>
                    <label class="mb-2 block text-xs font-semibold text-gray-500 uppercase tracking-wider">ไฟล์ CSV / Excel</label>
                    <input type="file" id="participant-file" accept=".csv,.xlsx,.xls" 
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary-400 focus:ring-4 focus:ring-orange-100 transition-all outline-none bg-gray-50/50">
                    
                    <div class="mt-4 p-4 rounded-xl bg-orange-50/50 border border-orange-100 text-xxs text-orange-700 leading-relaxed">
                        <p class="font-bold mb-1">รูปแบบไฟล์ที่รองรับ:</p>
                        <p>• ไฟล์ CSV, Excel (.xlsx, .xls)</p>
                        <p class="mt-2 font-bold mb-1">ลำดับคอลัมน์ (หรือใช้หัวตาราง):</p>
                        <p>ชื่อ, นามสกุล, อีเมล, องค์กร, ตำแหน่ง, โทรศัพท์, เลขบัตรประชาชน, หมายเหตุ</p>
                    </div>
                </div>
                <button onclick="handleImport()" id="btn-import" class="w-full py-3 bg-primary-400 hover:bg-primary-500 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-orange-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i> เริ่มการนำเข้าข้อมูล
                </button>
            </div>
        </section>
    </div>

    <!-- Results Area -->
    <div class="lg:col-span-2">
        <div id="import-result-placeholder" class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                <i class="fas fa-cloud-upload-alt text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">รอการนำเข้าข้อมูล</h3>
            <p class="text-sm text-gray-400 max-w-sm mx-auto">อัปโหลดไฟล์ Excel เพื่อตรวจสอบและนำเข้ารายชื่อเข้าสู่ระบบ</p>
        </div>

        <div id="import-result-content" class="hidden">
            <section class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 mb-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4">สรุปผลการนำเข้า</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-green-100 bg-green-50 p-4">
                        <p class="text-xxs font-bold text-green-600 uppercase mb-1">สำเร็จ</p>
                        <p class="text-2xl font-black text-green-700" id="stat-created">0</p>
                    </div>
                    <div class="rounded-xl border border-yellow-100 bg-yellow-50 p-4">
                        <p class="text-xxs font-bold text-yellow-600 uppercase mb-1">ข้าม / ผิดพลาด</p>
                        <p class="text-2xl font-black text-yellow-700" id="stat-skipped">0</p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h4 class="text-xs font-bold text-gray-500 mb-3">รายละเอียดรายบรรทัด</h4>
                    <div class="max-h-[400px] overflow-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">บรรทัด</th>
                                    <th class="px-4 py-3 font-semibold">สถานะ</th>
                                    <th class="px-4 py-3 font-semibold">รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody id="result-rows" class="divide-y divide-gray-50">
                                <!-- JS items here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
async function handleImport() {
    const fileInput = document.getElementById('participant-file');
    if (!fileInput.files.length) {
        return toast.warning('กรุณาเลือกไฟล์ก่อน');
    }

    const btn = document.getElementById('btn-import');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังนำเข้า...';

    try {
        const data = await excel.parse(fileInput.files[0]);
        if (!data || data.length === 0) {
            throw new Error('ไม่พบข้อมูลในไฟล์ หรือไฟล์ไม่ถูกต้อง');
        }

        const res = await ajax('/api/participant.php?action=import', {
            project_id: <?= (int) $project['id'] ?>,
            data: data
        });

        if (res.success) {
            toast.success('นำเข้าข้อมูลเรียบร้อยแล้ว');
            showResult(res.data);
        } else {
            toast.error(res.message || 'เกิดข้อผิดพลาดในการนำเข้า');
        }
    } catch (err) {
        console.error(err);
        toast.error(err.message || 'เกิดข้อผิดพลาดในการประมวลผลไฟล์');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-upload mr-2"></i> เริ่มการนำเข้าข้อมูล';
    }
}

function showResult(data) {
    document.getElementById('import-result-placeholder').classList.add('hidden');
    document.getElementById('import-result-content').classList.remove('hidden');
    
    document.getElementById('stat-created').textContent = data.created;
    document.getElementById('stat-skipped').textContent = data.skipped;
    
    const tbody = document.getElementById('result-rows');
    tbody.innerHTML = '';
    
    data.rows.forEach(row => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50 transition-colors';
        tr.innerHTML = `
            <td class="px-4 py-3 text-gray-400 font-mono">${row.row}</td>
            <td class="px-4 py-3">
                ${row.status === 'created' 
                    ? '<span class="text-green-600 font-bold"><i class="fas fa-check mr-1"></i> สำเร็จ</span>'
                    : '<span class="text-yellow-600 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> ข้าม</span>'
                }
            </td>
            <td class="px-4 py-3 text-gray-600">${row.message}</td>
        `;
        tbody.appendChild(tr);
    });
}
</script>
