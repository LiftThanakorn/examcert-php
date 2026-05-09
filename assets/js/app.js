// ========================
// GLOBAL CONFIG
// ========================
const BASE_URL   = document.querySelector('meta[name="base-url"]')?.content ?? '';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ========================
// AJAX HELPER
// ========================
function ajax(url, data = {}, method = 'POST') {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: BASE_URL + url,
      type: method,
      data: { ...data, csrf_token: CSRF_TOKEN },
      dataType: 'json',
      success: res => resolve(res),
      error: (xhr) => reject(xhr),
    });
  });
}

// ========================
// TOAST SHORTHAND
// ========================
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3500,
  timerProgressBar: true,
  customClass: { popup: 'rounded-xl font-sans text-sm shadow-card-hover' },
});

window.toast = {
  success: (msg) => Toast.fire({ icon: 'success', title: msg }),
  error:   (msg) => Toast.fire({ icon: 'error',   title: msg }),
  info:    (msg) => Toast.fire({ icon: 'info',     title: msg }),
  warning: (msg) => Toast.fire({ icon: 'warning',  title: msg }),
};

// ========================
// CONFIRM DIALOG
// ========================
function confirmDelete(message, onConfirm) {
  Swal.fire({
    title: 'ยืนยันการลบ',
    text: message ?? 'ข้อมูลจะถูกลบถาวรและไม่สามารถกู้คืนได้',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบข้อมูล',
    cancelButtonText: 'ยกเลิก',
    customClass: {
      popup:          'rounded-2xl font-sans',
      title:          'text-lg font-semibold text-gray-800',
      htmlContainer:  'text-sm text-gray-500',
      confirmButton:  '!bg-red-600 hover:!bg-red-700 !text-white !rounded-lg !text-sm !px-5 !py-2 !font-medium',
      cancelButton:   '!bg-white !text-gray-600 !border !border-gray-200 !rounded-lg !text-sm !px-5 !py-2 !font-medium',
      actions:        'gap-2',
    },
    buttonsStyling: false,
  }).then((result) => {
    if (result.isConfirmed && typeof onConfirm === 'function') {
      onConfirm();
    }
  });
}
