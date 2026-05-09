<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบการสอบ | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Thai:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Thai', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#FFF3EB', 100: '#FFE4D1', 200: '#FFC8A3', 300: '#FFA56E',
                            400: '#FF813A', 500: '#E87722', 600: '#C76118', 700: '#A34D10',
                        }
                    }
                }
            }
        }
    </script>
    
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #F8FAFC !important; /* Force Light Background */
            background-image: 
                radial-gradient(at 0% 0%, hsla(25,100%,93%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(25,100%,93%,1) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-premium {
            background-color: white !important;
            border-radius: 2.5rem;
            padding: 3rem;
            border: 1px solid #F1F5F9;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            width: 100%;
            max-width: 560px;
        }
        .btn-premium {
            background-color: #E87722;
            color: white;
            font-weight: 800;
            border-radius: 1.25rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s;
            box-shadow: 0 10px 15px -3px rgba(232, 119, 34, 0.3);
            width: 100%;
            border: none;
            cursor: pointer;
        }
        .btn-premium:hover {
            background-color: #C76118;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(232, 119, 34, 0.4);
        }
        .input-premium {
            width: 100%;
            height: 3.5rem;
            padding: 0 1.5rem;
            padding-left: 3.5rem;
            border-radius: 1.25rem;
            border: 2px solid #F1F5F9;
            background-color: #F8FAFC;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }
        .input-premium:focus {
            border-color: #FFA56E;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(232, 119, 34, 0.1);
        }
    </style>
</head>
<body>

    <div class="w-full max-w-[560px] px-6 py-12">
        <!-- Logo & Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-[2.5rem] shadow-xl mb-6">
                <i class="fas fa-user-shield text-4xl text-primary-500"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">ยืนยันตัวตน</h1>
            <p class="text-gray-500 font-medium">เพื่อเข้าสู่ระบบการสอบออนไลน์ มรภ.ร้อยเอ็ด</p>
        </div>

        <!-- Form Card -->
        <div class="card-premium">
            <?php if (!empty($error)): ?>
                <div class="mb-8 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-sm font-bold flex items-center gap-3">
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
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">ค้นหารายชื่อผู้เข้าสอบ</label>
                    <div class="relative">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" id="name_search" list="participants_list" placeholder="พิมพ์ชื่อหรือนามสกุล..." 
                            class="input-premium" required autocomplete="off">
                        <datalist id="participants_list"></datalist>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">รหัสผ่านเข้าสอบ (TOKEN)</label>
                    <div class="relative">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-key"></i>
                        </div>
                        <input type="text" name="access_token" placeholder="ระบุรหัส 6 หลัก" 
                            class="input-premium text-center font-bold tracking-[0.3em]" maxlength="6" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-premium">
                        เข้าสู่ห้องสอบ <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Footer -->
        <div class="text-center mt-10 space-y-4">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                โครงการ: <span class="text-gray-600"><?= e($project ? $project['name'] : '-') ?></span>
            </p>
            <div class="flex items-center justify-center gap-2 text-[10px] text-gray-300 font-bold uppercase tracking-widest">
                <span>RERU EXAM SYSTEM</span>
                <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                <span>SECURED ACCESS</span>
            </div>
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
                    confirmButtonColor: '#E87722'
                });
            }
        });
    });
    </script>
</body>
</html>