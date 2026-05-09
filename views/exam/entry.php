<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-orange mb-6">
                <i class="fas fa-graduation-cap text-4xl text-primary-400"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">เข้าสู่ระบบการสอบ</h1>
            <p class="text-white/70">กรุณาระบุข้อมูลส่วนตัวตามที่ลงทะเบียนไว้</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden p-8">
            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-sm font-medium border border-red-100 flex items-center gap-3">
                    <i class="fas fa-circle-exclamation text-lg"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($projectCode) ?>" class="space-y-5" id="entry_form">
                <?= csrfField() ?>
                
                <!-- Title is now hidden and auto-filled -->
                <input type="hidden" name="title" id="hidden_title">

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">ค้นหาชื่อ-นามสกุลของคุณ</label>
                    <div class="relative">
                        <input type="text" id="name_search" list="participants_list" placeholder="พิมพ์ชื่อหรือนามสกุลเพื่อค้นหา..." class="w-full h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-sm" required autocomplete="off">
                        <datalist id="participants_list"></datalist>
                        <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Hidden fields for backend compatibility -->
                <input type="hidden" name="first_name" id="hidden_first_name">
                <input type="hidden" name="last_name" id="hidden_last_name">

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">รหัสผ่านเข้าสอบ (Token)</label>
                    <input type="text" name="access_token" placeholder="ระบุรหัสผ่าน 6 หลัก" class="w-full h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-sm font-bold tracking-widest text-center" required>
                </div>

                <input type="hidden" name="project_code" value="<?= e($projectCode) ?>">

                <button type="submit" class="w-full h-14 mt-4 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl shadow-orange transition-all flex items-center justify-center gap-3 group">
                    ยืนยันตัวตนเพื่อเข้าสอบ
                    <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <p class="text-white/70 text-sm">ระบบสอบออนไลน์พร้อมออกใบเกียรติบัตร สำหรับบุคลากรมหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
            <p class="text-white/40 text-xs mt-1">พัฒนาระบบโดย นายธนากร อินทพันธ์</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectId = <?= json_encode($project ? (int)$project['id'] : 0) ?>;
    const nameSearch = document.getElementById('name_search');
    const dataList = document.getElementById('participants_list');
    const hiddenTitle = document.getElementById('hidden_title');
    const hiddenFirstName = document.getElementById('hidden_first_name');
    const hiddenLastName = document.getElementById('hidden_last_name');
    const entryForm = document.getElementById('entry_form');

    let participantsData = [];

    // Fetch participants for this project
    if (projectId > 0) {
        console.log('Fetching participants for project ID:', projectId);
        fetch(`<?= e(BASE_URL) ?>/api/participant.php?action=list&project_id=${projectId}`)
            .then(res => res.json())
            .then(res => {
                console.log('API Response:', res);
                if (res.success && Array.isArray(res.data)) {
                    participantsData = res.data;
                    console.log(`Loaded ${participantsData.length} participants.`);
                    
                    // Clear existing options just in case
                    dataList.innerHTML = '';
                    
                    participantsData.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.full_name;
                        dataList.appendChild(option);
                    });
                } else {
                    console.error('API Error or no data:', res.message);
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                // Fallback attempt with relative path if BASE_URL has issues
                if (!err.alreadyRetried) {
                    console.log('Retrying with relative path...');
                    fetch(`../api/participant.php?action=list&project_id=${projectId}`)
                        .then(r => r.json())
                        .then(r => { /* repeat logic or just log */ });
                }
            });
    } else {
        console.warn('No project ID found, auto-complete disabled.');
    }

    // Handle name selection
    nameSearch.addEventListener('input', function() {
        const val = this.value;
        const match = participantsData.find(p => p.full_name === val);
        if (match) {
            hiddenFirstName.value = match.first_name;
            hiddenLastName.value = match.last_name;
            // Extract title if possible
            const parts = match.full_name.split(' ');
            if (parts.length >= 1) {
                const titles = ['นาย', 'นาง', 'นางสาว', 'ดร.', 'ผศ.', 'รศ.', 'ศ.'];
                if (titles.includes(parts[0])) {
                    hiddenTitle.value = parts[0];
                }
            }
        } else {
            hiddenFirstName.value = '';
            hiddenLastName.value = '';
            hiddenTitle.value = '';
        }
    });

    // Form validation
    entryForm.addEventListener('submit', function(e) {
        if (!hiddenFirstName.value || !hiddenLastName.value) {
            e.preventDefault();
            Swal.fire({
                title: 'กรุณาเลือกชื่อจากรายการ',
                text: 'โปรดเลือกชื่อ-นามสกุลที่ปรากฏในรายการค้นหาเพื่อให้ข้อมูลถูกต้อง',
                icon: 'warning',
                confirmButtonColor: '#E87722'
            });
        }
    });
});
</script>