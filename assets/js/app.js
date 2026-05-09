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

window.showAlert = (title, msg, icon = 'info') => {
  let html = msg;
  if (Array.isArray(msg)) {
    html = '<ul class="text-left list-disc list-inside space-y-1">' + 
           msg.map(m => `<li>${m}</li>`).join('') + 
           '</ul>';
  }
  
  return Swal.fire({
    title: title,
    html: html,
    icon: icon,
    confirmButtonText: 'ตกลง',
    customClass: {
      popup: 'rounded-2xl font-sans',
      confirmButton: '!bg-primary-400 !px-8 !py-2 !rounded-lg !text-sm !font-medium'
    },
    buttonsStyling: false
  });
};

// ========================
// EXCEL HELPERS (SheetJS)
// ========================
window.excel = {
  // Export table or data to .xlsx
  export: (data, filename = 'export.xlsx', isTable = true) => {
    let ws;
    if (isTable) {
      const table = document.getElementById(data);
      if (!table) return console.error('Table not found');
      ws = XLSX.utils.table_to_sheet(table);
    } else {
      ws = XLSX.utils.json_to_sheet(data);
    }
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, filename);
  },
  
  // Parse Excel file to JSON
  parse: (file) => {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const json = XLSX.utils.sheet_to_json(worksheet);
        resolve(json);
      };
      reader.onerror = (err) => reject(err);
      reader.readAsArrayBuffer(file);
    });
  }
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
