<style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800;900&display=swap');

    body {
        font-family: 'Prompt', sans-serif !important;
        background: #fdfbf9;
        margin: 0;
        overflow: hidden;
    }

    .entry-container {
        height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
        background-image: 
            radial-gradient(at 0% 0%, hsla(25,100%,96%,1) 0, transparent 50%), 
            radial-gradient(at 100% 100%, hsla(210,100%,96%,1) 0, transparent 50%);
    }

    .split-layout {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 2.5rem;
        box-shadow: 0 40px 80px -15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        max-width: 1100px;
        width: 95%;
        max-height: 88vh;
        animation: layoutAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes layoutAppear {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Left Side */
    .info-panel {
        padding: 3rem;
        background: linear-gradient(165deg, #fffcf9 0%, #fff8f2 100%);
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
    }

    .project-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.8rem;
        background: white;
        border: 1.25px solid #ffe4d1;
        border-radius: 0.75rem;
        color: #e87722;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        width: fit-content;
    }

    .info-title {
        font-size: 1.85rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 1rem;
        letter-spacing: -0.01em;
    }

    .info-desc {
        color: #64748b;
        font-size: 0.85rem;
        line-height: 1.6;
        margin-bottom: 2rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1rem;
        border-radius: 1.25rem;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: transform 0.3s;
    }
    .stat-card:hover { transform: translateY(-3px); }

    .stat-icon {
        width: 2rem;
        height: 2rem;
        background: #fff7ed;
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ea580c;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: 0.6rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
    .stat-value { font-size: 1.1rem; font-weight: 800; color: #1e293b; }

    .rules-list { display: flex; flex-direction: column; gap: 0.6rem; }
    .rule-item { display: flex; align-items: center; gap: 0.6rem; font-size: 0.8rem; font-weight: 600; color: #475569; }
    .rule-bullet { width: 1.1rem; height: 1.1rem; background: #f1f5f9; border-radius: 0.35rem; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.55rem; flex-shrink: 0; }

    /* Right Side */
    .login-panel {
        padding: 3rem;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .login-header { margin-bottom: 2rem; }
    .login-icon-box { width: 3.5rem; height: 3.5rem; background: #fff7ed; border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; color: #ea580c; font-size: 1.5rem; margin-bottom: 1rem; }
    .login-title { font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0; }
    .login-subtitle { font-size: 0.8rem; font-weight: 600; color: #94a3b8; margin-top: 0.35rem; }

    .input-group { margin-bottom: 1.25rem; }
    .input-label { display: block; font-size: 0.75rem; font-weight: 800; color: #475569; margin-bottom: 0.4rem; }
    .input-wrapper { position: relative; }
    .input-icon { position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: 1rem; }
    
    .input-premium {
        width: 100%;
        height: 3rem;
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 1rem;
        padding: 0 1.1rem 0 3rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: all 0.2s;
    }
    .input-premium:focus { background: white; border-color: #ea580c; box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.05); }

    .btn-premium {
        width: 100%;
        height: 3.25rem;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        border: none;
        border-radius: 1rem;
        font-size: 1.1rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 8px 16px -4px rgba(234, 88, 12, 0.25);
        margin-top: 1rem;
    }
    .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 12px 20px -4px rgba(234, 88, 12, 0.3); }

    .status-banner { border-radius: 1.25rem; padding: 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.85rem; }
    .status-icon-circle { width: 2rem; height: 2rem; border-radius: 0.6rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.9rem; }
    .status-banner.scheduled { background: #eff6ff; border: 1px solid #dbeafe; }
    .status-banner.closed { background: #fef2f2; border: 1px solid #fee2e2; }
    
    .search-panel { display: none; position: absolute; left: 0; right: 0; top: calc(100% + 0.35rem); z-index: 50; max-height: 180px; overflow-y: auto; background: white; border-radius: 0.85rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); padding: 0.4rem; }
    .search-option { width: 100%; padding: 0.6rem 0.85rem; border: none; background: transparent; border-radius: 0.6rem; text-align: left; display: flex; align-items: center; gap: 0.6rem; cursor: pointer; color: #334155; font-weight: 600; font-size: 0.85rem; }
    .search-option:hover { background: #fff7ed; color: #ea580c; }

    @media (max-width: 900px) {
        body { overflow: auto; }
        .entry-container { height: auto; min-height: 100vh; padding: 1.5rem; }
        .split-layout { grid-template-columns: 1fr; max-width: 520px; max-height: none; }
        .info-panel { padding: 2.5rem; border-right: none; border-bottom: 1px solid #f1f5f9; }
        .login-panel { padding: 2.5rem; }
    }
</style>

<div class="entry-container">
    <div class="split-layout">
        <!-- LEFT -->
        <div class="info-panel">
            <div class="project-badge"><i class="fas fa-shield-halved"></i> Exam Portal</div>
            <h1 class="info-title"><?= e($project['name']) ?></h1>
            <p class="info-desc"><?= e($project['description'] ?: 'ระบบสอบวัดความรู้ออนไลน์และออกเกียรติบัตรอัตโนมัติ') ?></p>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-lines"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">ข้อสอบ</span>
                        <span class="stat-value"><?= (int)($project['question_count_total'] ?? $project['question_count'] ?? 0) ?> ข้อ</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-stopwatch"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">เวลาสอบ</span>
                        <span class="stat-value"><?= (int)($project['time_limit_min'] ?? 60) ?> นาที</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">เกณฑ์ผ่าน</span>
                        <span class="stat-value"><?= number_format((float)($project['pass_score'] ?? 70), 0) ?>%</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-id-card-clip"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">สิทธิ์สอบ</span>
                        <span class="stat-value"><?= (int)($project['max_attempts'] ?? 1) ?> ครั้ง</span>
                    </div>
                </div>
            </div>

            <div class="rules-list">
                <div class="rule-item"><div class="rule-bullet"><i class="fas fa-check"></i></div> ทำข้อสอบให้ครบทุกข้อก่อนกดส่ง</div>
                <div class="rule-item"><div class="rule-bullet"><i class="fas fa-check"></i></div> ส่งคำตอบอัตโนมัติเมื่อหมดเวลา</div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="login-panel">
            <?php
            $runtimeStatus = $runtimeStatus ?? getProjectRuntimeStatus($project);
            $examStatus = (string) ($runtimeStatus['status'] ?? 'draft');
            $examAllowed = (bool) ($runtimeStatus['allowed'] ?? false);
            ?>

            <?php if (!$examAllowed): ?>
                <div class="status-banner <?= $examStatus === 'scheduled' ? 'scheduled' : 'closed' ?>">
                    <div class="status-icon-circle"><i class="fas fa-circle-info"></i></div>
                    <div>
                        <h3 class="font-extrabold text-[10px] m-0"><?= $examStatus === 'scheduled' ? 'ยังไม่เปิดสอบ' : 'ปิดระบบแล้ว' ?></h3>
                        <?php if (!empty($runtimeStatus['message'])): ?>
                            <p class="mt-0.5 text-[10px] font-semibold opacity-75"><?= e((string) $runtimeStatus['message']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="<?= $examAllowed ? '' : 'opacity-40 pointer-events-none' ?>">
                <div class="login-header">
                    <div class="login-icon-box"><i class="fa-solid fa-fingerprint"></i></div>
                    <h2 class="login-title">ยืนยันตัวตน</h2>
                    <p class="login-subtitle">กรอกข้อมูลเพื่อเริ่มต้นการทำข้อสอบ</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg mb-4 font-bold text-[10px] border border-red-100"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= e(BASE_URL) ?>/public/entry.php?project=<?= e($projectCode) ?>" class="space-y-4">
                    <?= csrfField() ?>
                    <input type="hidden" name="project_code" value="<?= e($projectCode) ?>">
                    
                    <div class="input-group">
                        <label class="input-label">ชื่อ-นามสกุล</label>
                        <div class="input-wrapper">
                            <input type="text" id="participant_search" class="input-premium" placeholder="ค้นหาชื่อ..." required>
                            <i class="fa-solid fa-user-tie input-icon"></i>
                            <div id="search_results" class="search-panel"></div>
                        </div>
                        <input type="hidden" name="first_name" id="selected_first_name">
                        <input type="hidden" name="last_name" id="selected_last_name">
                    </div>

                    <div class="input-group">
                        <label class="input-label">Access Token</label>
                        <div class="input-wrapper">
                            <input type="password" name="access_token" class="input-premium" placeholder="••••••" maxlength="6" required>
                            <i class="fa-solid fa-lock input-icon"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-premium">เริ่มทำข้อสอบ <i class="fa-solid fa-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const $input = $('#participant_search'), $results = $('#search_results'), pCode = '<?= e($projectCode) ?>';
    let timer;

    $input.on('input', function() {
        clearTimeout(timer);
        const q = $(this).val().trim();
        if (q.length < 2) { $results.hide(); return; }
        timer = setTimeout(() => {
            $.ajax({
                url: '<?= BASE_URL ?>/api/exam.php?action=search_participants',
                data: { project_code: pCode, query: q },
                success: function(res) {
                    if (res.success && res.data.length > 0) {
                        let h = '';
                        res.data.forEach(p => h += `<button type="button" class="search-option" data-first="${p.first_name}" data-last="${p.last_name}"><i class="fa-solid fa-user"></i><span>${p.title}${p.first_name} ${p.last_name}</span></button>`);
                        $results.html(h).show();
                    } else { $results.html('<div class="p-2 text-[10px] text-center">ไม่พบข้อมูล</div>').show(); }
                }
            });
        }, 200);
    });

    $(document).on('click', '.search-option', function() {
        $('#selected_first_name').val($(this).data('first'));
        $('#selected_last_name').val($(this).data('last'));
        $input.val($(this).find('span').text());
        $results.hide();
    });

    $(document).on('click', e => { if (!$(e.target).closest('.input-wrapper').length) $results.hide(); });
});
</script>
