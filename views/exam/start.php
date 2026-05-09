<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ห้องสอบออนไลน์ | <?= e($project['name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/globals.css">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/custom.css">
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
    <style type="text/tailwindcss">
        @layer components {
            .glass {
                @apply bg-white/80 backdrop-blur-xl border border-white/20;
            }
            .shadow-premium {
                box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.1);
            }
            .shadow-soft {
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            }
            .card-premium {
                @apply bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-soft;
            }
            .btn-premium {
                @apply relative overflow-hidden px-6 py-3 bg-primary-500 text-white font-bold rounded-2xl transition-all duration-300 shadow-soft hover:shadow-premium hover:bg-primary-600 active:scale-95;
            }
            .option-input:checked + .option-label {
                @apply border-primary-500 bg-primary-50 ring-4 ring-primary-500/10;
            }
            .option-input:checked + .option-label .option-marker {
                @apply bg-primary-500 text-white border-primary-500;
            }
            .nav-item.active {
                @apply bg-primary-500 text-white border-primary-500 shadow-lg shadow-primary-500/30;
            }
            .nav-item.done {
                @apply bg-green-50 text-green-600 border-green-200;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased overflow-x-hidden">

    <!-- Header Area -->
    <header class="fixed top-0 left-0 right-0 z-50 glass border-b border-gray-100 shadow-sm px-6 py-3">
        <div class="max-w-[1600px] mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-primary-500/20">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-900 line-clamp-1 leading-none mb-1"><?= e($project['name']) ?></h1>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?= e($participant['first_name'] . ' ' . $participant['last_name']) ?></span>
                        <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
                        <span class="text-[10px] font-bold text-primary-500 uppercase tracking-widest">กำลังทำข้อสอบ</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">เวลาคงเหลือ</span>
                    <div id="timer" class="text-2xl font-mono font-bold text-gray-900 tabular-nums">--:--</div>
                </div>
                <button onclick="confirmSubmit()" class="btn-premium px-6 py-2.5 h-auto text-sm">
                    <i class="fas fa-paper-plane mr-2"></i> ส่งข้อสอบ
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto pt-28 pb-32 px-6 flex flex-col lg:flex-row gap-8 min-h-screen">
        
        <!-- Sidebar: Navigator -->
        <aside class="w-full lg:w-80 shrink-0 space-y-6">
            <div class="card-premium p-6 sticky top-28">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest">ข้อสอบทั้งหมด</h3>
                    <span id="progress-text" class="text-[10px] font-bold text-primary-500 bg-primary-50 px-2 py-1 rounded-md">0 / <?= count($questions) ?></span>
                </div>
                
                <!-- Progress Mini Bar -->
                <div class="h-1.5 w-full bg-gray-100 rounded-full mb-8 overflow-hidden">
                    <div id="progress-bar" class="h-full bg-primary-500 transition-all duration-500" style="width: 0%"></div>
                </div>

                <div class="grid grid-cols-5 sm:grid-cols-8 lg:grid-cols-4 gap-3" id="navigator-grid">
                    <?php foreach ($questions as $index => $q): ?>
                    <button onclick="showQuestion(<?= $index ?>)" 
                        id="nav-item-<?= $index ?>" 
                        class="nav-item aspect-square rounded-xl border border-gray-100 bg-white text-sm font-bold text-gray-400 flex items-center justify-center transition-all hover:border-primary-200 hover:text-primary-500 active:scale-90">
                        <?= $index + 1 ?>
                    </button>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-50 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-green-500"></div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">ทำแล้ว</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded bg-white border border-gray-200"></div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">ยังไม่ทำ</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Question Space -->
        <section class="flex-grow">
            <form id="exam-form" method="post" action="<?= e(BASE_URL) ?>/public/take-exam.php?session_id=<?= (int) $session['id'] ?>" class="max-w-3xl mx-auto lg:mx-0">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="submit">
                
                <div id="questions-container">
                    <?php foreach ($questions as $index => $q): ?>
                    <div class="question-slide space-y-8 <?= $index === 0 ? '' : 'hidden' ?>" 
                        data-index="<?= $index ?>" 
                        id="q-<?= (int) $q['id'] ?>">
                        
                        <!-- Question Card -->
                        <div class="card-premium">
                            <div class="flex items-start gap-6">
                                <div class="w-14 h-14 bg-primary-500 rounded-2xl flex items-center justify-center text-white text-xl font-black shrink-0 shadow-lg shadow-primary-500/20">
                                    <?= $index + 1 ?>
                                </div>
                                <div class="space-y-8 flex-grow">
                                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 leading-[1.4]">
                                        <?= e($q['question_text']) ?>
                                    </h2>

                                    <div class="grid grid-cols-1 gap-4">
                                        <?php 
                                        $choices = json_decode($q['choices'], true);
                                        if ($q['type'] === 'fill_blank'): 
                                        ?>
                                            <input type="text" name="answers[<?= (int) $q['id'] ?>]" 
                                                placeholder="พิมพ์คำตอบของคุณที่นี่..." 
                                                class="w-full h-16 px-6 rounded-2xl border-2 border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-500 focus:ring-8 focus:ring-primary-500/5 transition-all outline-none text-lg font-medium"
                                                oninput="markQuestionDone(<?= $index ?>, this.value !== '')">
                                        <?php else: ?>
                                            <?php foreach ($choices as $choice): ?>
                                            <div class="relative">
                                                <input type="radio" id="choice-<?= (int) $q['id'] ?>-<?= e($choice['key']) ?>" 
                                                    name="answers[<?= (int) $q['id'] ?>]" 
                                                    value="<?= e($choice['key']) ?>" 
                                                    class="option-input hidden"
                                                    onchange="markQuestionDone(<?= $index ?>, true)">
                                                <label for="choice-<?= (int) $q['id'] ?>-<?= e($choice['key']) ?>" 
                                                    class="option-label flex items-center p-6 rounded-3xl border-2 border-gray-100 bg-white hover:border-primary-200 cursor-pointer transition-all">
                                                    <span class="option-marker w-10 h-10 rounded-xl border-2 border-gray-100 bg-gray-50 flex items-center justify-center text-xs font-black text-gray-400 mr-5 transition-all">
                                                        <?= strtoupper($choice['key']) ?>
                                                    </span>
                                                    <span class="text-gray-700 font-bold text-lg"><?= e($choice['text']) ?></span>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </section>
    </main>

    <!-- Floating Navigation Bar -->
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-lg">
        <div class="glass shadow-premium rounded-[2.5rem] p-3 flex items-center justify-between border border-white/50">
            <button id="prev-btn" onclick="prevQuestion()" class="w-14 h-14 bg-white border border-gray-100 text-gray-400 rounded-[1.5rem] flex items-center justify-center hover:text-primary-500 disabled:opacity-30 transition-all active:scale-90">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="flex flex-col items-center">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">หน้าข้อสอบ</span>
                <span id="current-page-text" class="text-lg font-black text-gray-900">1 / <?= count($questions) ?></span>
            </div>

            <button id="next-btn" onclick="nextQuestion()" class="w-14 h-14 bg-primary-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-primary-500/30 hover:bg-primary-600 transition-all active:scale-90">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= e(BASE_URL) ?>/assets/js/exam.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initExam(<?= (int) $secondsLeft ?>, <?= (int) $session['id'] ?>);
            showQuestion(0);
        });

        function markQuestionDone(index, done) {
            const navItem = document.getElementById(`nav-item-${index}`);
            if (done) {
                navItem.classList.add('done');
            } else {
                navItem.classList.remove('done');
            }
            updateProgress();
        }

        function updateProgress() {
            const total = <?= count($questions) ?>;
            const doneCount = document.querySelectorAll('.nav-item.done').length;
            const percent = Math.round((doneCount / total) * 100);
            
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            
            if (progressBar) progressBar.style.width = percent + '%';
            if (progressText) progressText.innerText = `${doneCount} / ${total}`;
        }
    </script>
</body>
</html>
