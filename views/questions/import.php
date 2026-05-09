<div class="mb-6 fade-up">
    <a href="<?= e(BASE_URL) ?>/admin/questions/?project_id=<?= (int)$projectId ?>" class="inline-flex items-center text-xs text-gray-400 hover:text-primary-400 transition-colors mb-2">
        <i class="fas fa-arrow-left mr-1.5"></i> กลับคลังข้อสอบ
    </a>
    <h2 class="text-xl font-semibold text-gray-800">นำเข้าข้อสอบ (CSV)</h2>
    <p class="text-sm text-gray-400 mt-0.5"><?= e($project['name']) ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 fade-up fade-up-1">
    <!-- Left: Upload Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 md:p-8">
            <form method="post" enctype="multipart/form-data" action="<?= e(BASE_URL) ?>/admin/questions/import.php?project_id=<?= (int)$projectId ?>">
                <?= csrfField() ?>
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-10 flex flex-col items-center justify-center bg-gray-50/30 hover:bg-primary-50/30 hover:border-primary-200 transition-all cursor-pointer group relative">
                    <input type="file" name="csv_file" required accept=".csv" class="absolute inset-0 opacity-0 cursor-pointer" onchange="updateFileName(this)">
                    <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-csv text-3xl text-primary-400"></i>
                    </div>
                    <p class="text-gray-600 font-medium mb-1" id="file-label">คลิกเพื่อเลือกไฟล์ CSV หรือลากไฟล์มาวางที่นี่</p>
                    <p class="text-gray-400 text-xs">รองรับไฟล์ .csv ขนาดไม่เกิน 5MB</p>
                </div>
                
                <div class="mt-8 flex items-center justify-between p-4 bg-primary-50/50 rounded-xl border border-primary-100">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-info-circle text-primary-400"></i>
                        <p class="text-xs text-primary-700 leading-relaxed">
                            ระบบจะตรวจสอบความถูกต้องของข้อมูลก่อนนำเข้า หากพบข้อผิดพลาดจะแจ้งให้ทราบ
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-orange-100 flex items-center gap-2">
                        <i class="fas fa-upload"></i> เริ่มการนำเข้าข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Instructions -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-list-ol text-primary-400"></i> รูปแบบไฟล์ CSV
            </h3>
            <div class="space-y-4 text-xs text-gray-500 leading-relaxed">
                <p>ไฟล์ CSV ต้องประกอบด้วยคอลัมน์ดังนี้ (ตามลำดับ):</p>
                <ol class="list-decimal list-inside space-y-2 ml-1">
                    <li><strong class="text-gray-700">Question Text:</strong> โจทย์ข้อสอบ</li>
                    <li><strong class="text-gray-700">Type:</strong> multiple_choice, true_false, fill_blank</li>
                    <li><strong class="text-gray-700">Choices:</strong> JSON หรือ ข้อความ (ถ้ามี)</li>
                    <li><strong class="text-gray-700">Correct Answer:</strong> คำตอบที่ถูกต้อง</li>
                    <li><strong class="text-gray-700">Difficulty:</strong> easy, medium, hard</li>
                    <li><strong class="text-gray-700">Category:</strong> หมวดหมู่ (เว้นว่างได้)</li>
                </ol>
                <div class="pt-4 mt-4 border-t border-gray-50">
                    <a href="#" class="text-primary-400 font-bold hover:underline flex items-center gap-1">
                        <i class="fas fa-download"></i> ดาวน์โหลดเทมเพลต CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-6">
            <h3 class="text-sm font-bold text-blue-800 mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb"></i> คำแนะนำ
            </h3>
            <ul class="text-xs text-blue-700 space-y-2 list-disc list-inside">
                <li>ใช้ UTF-8 Encoding เท่านั้นสำหรับภาษาไทย</li>
                <li>สำหรับ True/False ให้ใส่ Choices เป็น <code class="bg-white px-1 rounded">true,false</code></li>
                <li>สำหรับ Multiple Choice ให้คั่นตัวเลือกด้วยเครื่องหมาย <code class="bg-white px-1 rounded">|</code> (Pipe)</li>
            </ul>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const label = document.getElementById('file-label');
    if (input.files && input.files[0]) {
        label.textContent = "เลือกไฟล์แล้ว: " + input.files[0].name;
        label.classList.add('text-primary-600');
    }
}
</script>
