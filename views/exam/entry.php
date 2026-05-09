<style type="text/css">
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
    .search-panel {
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 0.5rem);
        z-index: 30;
        max-height: 16rem;
        overflow-y: auto;
        border: 1px solid #FFE4D1;
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 0.5rem;
    }
    .search-option {
        width: 100%;
        border: 0;
        background: transparent;
        border-radius: 0.9rem;
        padding: 0.75rem 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-align: left;
        cursor: pointer;
        color: #334155;
        font-weight: 700;
    }
    .search-option:hover,
    .search-option:focus {
        background: #FFF3EB;
        color: #C76118;
        outline: none;
    }
    .search-empty {
        padding: 0.85rem 1rem;
        color: #94A3B8;
        font-size: 0.85rem;
        font-weight: 700;
    }
</style>

<div class="card-premium fade-up">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-50 rounded-3xl mb-6 text-orange-500 shadow-sm border border-orange-100">
            <i class="fa-solid fa-user-shield text-3xl"></i>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-2 leading-tight">เข้าสู่ระบบการสอบ</h1>
        <p class="text-slate-400 font-bold text-lg"><?= e($project['name']) ?></p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border-2 border-red-100 text-red-600 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-shake font-bold">
            <i class="fa-solid fa-circle-exclamation text-lg"></i>
            <span><?= e($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e(BASE_URL) ?>/public/entry.php?project=<?= e($projectCode) ?>" class="space-y-6">
        <?= csrfField() ?>
        <input type="hidden" name="project_code" value="<?= e($projectCode) ?>">

        <!-- Participant Search Field -->
        <div class="relative group">
            <label class="block text-slate-500 font-bold text-sm mb-2 ml-1">ชื่อ-นามสกุล ผู้เข้าสอบ</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 transition-colors group-focus-within:text-orange-400">
                    <i class="fa-solid fa-user text-lg"></i>
                </div>
                <input type="text" id="participant_search" class="input-premium" placeholder="พิมพ์ชื่อเพื่อค้นหา..." autocomplete="off">
            </div>
            
            <!-- Hidden inputs for selected participant -->
            <input type="hidden" name="first_name" id="selected_first_name">
            <input type="hidden" name="last_name" id="selected_last_name">

            <!-- Search Results Dropdown -->
            <div id="search_results" class="search-panel">
                <!-- Results will be injected here -->
            </div>
        </div>

        <div class="relative group">
            <label class="block text-slate-500 font-bold text-sm mb-2 ml-1">รหัสผ่าน / Access Token</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 transition-colors group-focus-within:text-orange-400">
                    <i class="fa-solid fa-key text-lg"></i>
                </div>
                <input type="password" name="access_token" class="input-premium" placeholder="กรอกรหัสผ่านเพื่อเข้าสอบ" required>
            </div>
        </div>

        <button type="submit" class="btn-premium group mt-4">
            <span>เริ่มทำข้อสอบตอนนี้</span>
            <i class="fa-solid fa-arrow-right transition-transform group-hover:translate-x-1"></i>
        </button>
    </form>

    <div class="mt-10 pt-8 border-t border-slate-50 text-center">
        <p class="text-slate-400 text-sm font-bold flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock text-slate-300"></i>
            ระบบความปลอดภัยขั้นสูงโดย <?= e(APP_NAME) ?>
        </p>
    </div>
</div>

<script>
$(document).ready(function() {
    const $input = $('#participant_search');
    const $results = $('#search_results');
    const projectCode = '<?= e($projectCode) ?>';
    let debounceTimer;

    $input.on('input', function() {
        clearTimeout(debounceTimer);
        const query = $(this).val().trim();

        if (query.length < 2) {
            $results.hide();
            return;
        }

        debounceTimer = setTimeout(() => {
            $.ajax({
                url: '<?= BASE_URL ?>/api/exam.php?action=search_participants',
                method: 'GET',
                data: {
                    project_code: projectCode,
                    query: query
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(p => {
                            html += `
                                <button type="button" class="search-option" 
                                    data-first="${p.first_name}" 
                                    data-last="${p.last_name}">
                                    <i class="fa-solid fa-circle-user text-slate-200"></i>
                                    <span>${p.title}${p.first_name} ${p.last_name}</span>
                                </button>`;
                        });
                        $results.html(html).show();
                    } else {
                        $results.html('<div class="search-empty">ไม่พบข้อมูลผู้เข้าสอบ</div>').show();
                    }
                }
            });
        }, 300);
    });

    $(document).on('click', '.search-option', function() {
        const first = $(this).data('first');
        const last = $(this).data('last');
        const full = $(this).find('span').text();

        $('#selected_first_name').val(first);
        $('#selected_last_name').val(last);
        $input.val(full);
        $results.hide();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.relative').length) {
            $results.hide();
        }
    });
});
</script>
