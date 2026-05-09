
/**
 * ExamCert Exam Engine
 */
let currentQuestion = 0;
const totalQuestions = document.querySelectorAll('.question-slide').length;
let timeRemaining = 0;
let timerInterval = null;

let syncInterval = null;
let sessionId = 0;

function initExam(remainingSeconds, id) {
    timeRemaining = remainingSeconds;
    sessionId = id;
    updateTimerDisplay();
    startTimer();
    updateProgress();
    
    // Sync with server every 30 seconds
    syncInterval = setInterval(syncWithServer, 30000);
}

function getBaseUrl() {
    return document.querySelector('meta[name="base-url"]')?.content || '';
}

function syncWithServer() {
    $.ajax({
        url: getBaseUrl() + '/api/exam.php?action=check_time',
        data: { session_id: sessionId },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                if (res.data.should_submit) {
                    autoSubmit();
                } else if (res.data.seconds_left !== null) {
                    // Update client time to match server
                    timeRemaining = res.data.seconds_left;
                    updateTimerDisplay();
                }
            }
        }
    });
}

function startTimer() {
    timerInterval = setInterval(() => {
        timeRemaining--;
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            timeRemaining = 0;
            updateTimerDisplay();
            autoSubmit();
        } else {
            updateTimerDisplay();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const timerEl = document.getElementById('timer');
    if (!timerEl) return;

    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    if (timeRemaining < 300) { // Less than 5 mins
        timerEl.classList.add('text-red-500', 'animate-pulse');
    }
}

function updateProgress() {
    const answered = document.querySelectorAll('input:checked, textarea').filter(el => {
        if (el.tagName === 'TEXTAREA' || el.type === 'text') return el.value.trim() !== '';
        return el.checked;
    }).length;
    
    const progressPercent = Math.round((answered / totalQuestions) * 100);
    document.getElementById('progress-text').textContent = `${answered}/${totalQuestions} ข้อ (${progressPercent}%)`;
    document.getElementById('progress-bar').style.width = `${progressPercent}%`;
}

function showQuestion(index) {
    document.querySelectorAll('.question-slide').forEach(el => el.classList.add('hidden'));
    document.querySelector(`.question-slide[data-index="${index}"]`).classList.remove('hidden');
    
    document.getElementById('current-page-text').textContent = `${index + 1} / ${totalQuestions}`;
    document.getElementById('prev-btn').disabled = (index === 0);
    document.getElementById('next-btn').disabled = (index === totalQuestions - 1);
    
    currentQuestion = index;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        showQuestion(currentQuestion + 1);
    }
}

function prevQuestion() {
    if (currentQuestion > 0) {
        showQuestion(currentQuestion - 1);
    }
}

function confirmSubmit() {
    Swal.fire({
        title: 'ยืนยันการส่งข้อสอบ?',
        text: 'เมื่อส่งแล้วจะไม่สามารถกลับมาแก้ไขได้อีก',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันส่งข้อสอบ',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'rounded-2xl font-sans',
            confirmButton: '!bg-primary-400 !px-8 !py-3 !rounded-xl !text-sm !font-bold',
            cancelButton: '!bg-white !text-gray-500 !px-8 !py-3 !rounded-xl !text-sm !font-bold'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('exam-form').submit();
        }
    });
}

function autoSubmit() {
    Swal.fire({
        title: 'หมดเวลาสอบ!',
        text: 'ระบบกำลังส่งคำตอบให้อัตโนมัติ...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
            setTimeout(() => {
                document.getElementById('exam-form').submit();
            }, 2000);
        }
    });
}

// Update progress and save answer on any input change
document.addEventListener('change', (e) => {
    if (e.target.closest('#exam-form')) {
        updateProgress();
        
        const input = e.target;
        if (input.name.startsWith('answers[')) {
            const questionId = input.name.match(/\[(\d+)\]/)[1];
            const answer = input.value;
            saveAnswer(questionId, answer);
        }
    }
});

function saveAnswer(questionId, answer) {
    $.ajax({
        url: getBaseUrl() + '/api/exam.php?action=save_answer',
        type: 'POST',
        data: {
            session_id: sessionId,
            question_id: questionId,
            answer: answer,
            csrf_token: document.querySelector('input[name="csrf_token"]')?.value
        },
        dataType: 'json',
        success: function(res) {
            if (!res.success) console.error('Auto-save failed:', res.message);
        }
    });
}
