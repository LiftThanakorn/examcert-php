<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบการสอบ | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/globals.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/custom.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6 antialiased">

    <div class="max-w-xl w-full space-y-8 animate-float">
        <!-- Logo & Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-[2rem] shadow-premium mb-2">
                <i class="fas fa-user-check text-4xl text-primary-500"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">ยืนยันตัวตน</h1>
            <p class="text-gray-500 font-medium">เข้าสู่ระบบการสอบออนไลน์ มรภ.ร้อยเอ็ด</p>
        </div>

        <!-- Form Card -->
        <div class="card-premium">
            <?php if (!empty($error)): ?>
                <div class="mb-8 p-4 rounded-2xl bg-danger/10 border border-danger/20 text-danger text-sm font-bold flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($projectCode) ?>" class="space-y-6" id="entry_form">
                <?= csrfField() ?>
                
                <input type="hidden" name="title" id="hidden_title">
                <input type="hidden" name="first_name" id="hidden_first_name">
                <input type="hidden" name="last_name" id="hidden_last_name">
                <input type="hidden" name="project_code" value="<?= e($projectCode) ?>">

                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-widest ml-1">ค้นหารายชื่อผู้เข้าสอบ</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" id="name_search" list="participants_list" placeholder="พิมพ์ชื่อหรือนามสกุลเพื่อค้นหา..." 
                            class="w-full h-14 pl-12 pr-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[6px] focus:ring-primary-500/10 transition-all outline-none text-base font-medium" 
                            required autocomplete="off">
                        <datalist id="participants_list"></datalist>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-widest ml-1">รหัสผ่านเข้าสอบ (TOKEN)</label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors">
                            <i class="fas fa-key"></i>
                        </div>
                        <input type="text" name="access_token" placeholder="ระบุรหัส 6 หลัก" 
                            class="w-full h-14 pl-12 pr-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[6px] focus:ring-primary-500/10 transition-all outline-none text-xl font-bold tracking-[0.5em] text-center" 
                            maxlength="6" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-premium w-full h-14 text-lg flex items-center justify-center gap-3">
                        เข้าสู่ห้องสอบ <i class="fas fa-sign-in-alt text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Footer -->
        <div class="text-center space-y-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">โครงการ: <?= e($project ? $project['name'] : '-') ?></p>
            <div class="h-px w-12 bg-gray-200 mx-auto"></div>
            <p class="text-[10px] text-gray-300 font-medium leading-relaxed">
                กรณีไม่พบรายชื่อหรือรหัสผ่านผิดพลาด<br>กรุณาติดต่อเจ้าหน้าที่ผู้ดูแลโครงการ
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        if (projectId > 0) {
            fetch(`<?= e(BASE_URL) ?>/api/participant.php?action=list&project_id=${projectId}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success && Array.isArray(res.data)) {
                        participantsData = res.data;
                        dataList.innerHTML = '';
                        participantsData.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.full_name;
                            dataList.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error('Fetch failed:', err));
        }

        nameSearch.addEventListener('input', function() {
            const val = this.value;
            const match = participantsData.find(p => p.full_name === val);
            if (match) {
                hiddenFirstName.value = match.first_name;
                hiddenLastName.value = match.last_name;
                const parts = match.full_name.split(' ');
                if (parts.length >= 1) {
                    const titles = ['นาย', 'นาง', 'นางสาว', 'ดร.', 'ผศ.', 'รศ.', 'ศ.'];
                    if (titles.includes(parts[0])) hiddenTitle.value = parts[0];
                }
            } else {
                hiddenFirstName.value = '';
                hiddenLastName.value = '';
                hiddenTitle.value = '';
            }
        });

        entryForm.addEventListener('submit', function(e) {
            if (!hiddenFirstName.value || !hiddenLastName.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'กรุณาเลือกชื่อจากรายการ',
                    text: 'โปรดเลือกชื่อ-นามสกุลที่ปรากฏในรายการค้นหาเพื่อให้ข้อมูลถูกต้อง',
                    icon: 'warning',
                    confirmButtonColor: '#E87722',
                    customClass: {
                        popup: 'rounded-[2rem]',
                        confirmButton: 'rounded-xl'
                    }
                });
            }
        });
    });
    </script>
</body>
</html>