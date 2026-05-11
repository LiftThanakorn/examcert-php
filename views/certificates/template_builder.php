<?php
requireLogin();

$templateId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$template = $templateId > 0 ? getCertificateTemplate($templateId) : null;
if ($templateId > 0 && !$template) {
    http_response_code(404);
    echo 'Template not found.';
    exit;
}

$elements = $template ? (json_decode((string) ($template['elements'] ?? '[]'), true) ?? []) : [];
$bgColor = $template['bg_color'] ?? '#FFFFFF';
$bgImage = $template['bg_image'] ?? '';
$bgType = $template['bg_type'] ?? 'color';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Template Builder - ExamCert</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: { 400:'#E87722', 500:'#C4601A', 50:'#FFF3E8', 100:'#FAEEDA', 600:'#A94F12' }
      },
      fontFamily: { sans:['Sarabun','sans-serif'] },
      fontSize: { xxs:'0.65rem' }
    }
  }
}
</script>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  * { box-sizing: border-box; }
  body { font-family: 'Sarabun', sans-serif; }
  #canvas-wrap {
    position: relative;
    width: 1123px;
    height: 794px;
    flex-shrink: 0;
    overflow: hidden;
    box-shadow: 0 4px 32px rgba(0,0,0,0.18);
    background: #fff;
  }
  #canvas-wrap.portrait {
    width: 794px;
    height: 1123px;
  }
  #bg-layer {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    z-index: 0;
  }
  .cert-el {
    position: absolute;
    cursor: move;
    user-select: none;
    z-index: 10;
    border: 1.5px solid transparent;
    border-radius: 2px;
  }
  .cert-el:hover { border-color: rgba(232,119,34,0.5); }
  .cert-el.selected { border-color: #E87722; box-shadow: 0 0 0 2px rgba(232,119,34,0.25); }
  .resize-handle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #E87722;
    border: 1.5px solid #fff;
    border-radius: 2px;
    z-index: 20;
  }
  .resize-handle.se { bottom: -4px; right: -4px; cursor: se-resize; }
  .resize-handle.sw { bottom: -4px; left: -4px; cursor: sw-resize; }
  .resize-handle.ne { top: -4px; right: -4px; cursor: ne-resize; }
  .resize-handle.nw { top: -4px; left: -4px; cursor: nw-resize; }
  .prop-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
    font-family: 'Sarabun', sans-serif;
  }
  .prop-input:focus { outline: none; border-color: #E87722; box-shadow: 0 0 0 2px rgba(232,119,34,0.15); }
  .prop-label { font-size: 11px; color: #6b7280; font-weight: 500; margin-bottom: 3px; }
  ::-webkit-scrollbar { width: 4px; height: 4px; }
  ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
</style>
</head>
<body class="bg-gray-100 h-screen flex flex-col overflow-hidden">

<div class="h-12 bg-white border-b border-gray-200 flex items-center justify-between px-4 flex-shrink-0 z-30">
  <div class="flex items-center gap-3">
    <a href="<?= e(BASE_URL) ?>/admin/certificates/templates" class="text-gray-400 hover:text-gray-600 transition-colors">
      <i class="fas fa-arrow-left text-sm"></i>
    </a>
    <div class="w-7 h-7 bg-primary-400 rounded-lg flex items-center justify-center">
      <i class="fas fa-award text-white text-xs"></i>
    </div>
    <input id="tpl-name" type="text" value="<?= e($template['name'] ?? 'เทมเพลตใหม่') ?>"
      class="font-semibold text-sm text-gray-800 border-none outline-none bg-transparent w-48">
  </div>
  <div class="flex items-center gap-2">
    <select id="orientation-select" onchange="changeOrientation()"
      class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-primary-400">
      <option value="L" <?= ($template['orientation'] ?? 'L') === 'L' ? 'selected' : '' ?>>Landscape A4</option>
      <option value="P" <?= ($template['orientation'] ?? 'L') === 'P' ? 'selected' : '' ?>>Portrait A4</option>
    </select>
    <button onclick="previewPDF()"
      class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
      <i class="fas fa-eye text-gray-500"></i> Preview
    </button>
    <button onclick="saveTemplate()"
      class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-primary-400 hover:bg-primary-500 text-white rounded-lg transition-colors">
      <i class="fas fa-save"></i> บันทึก
    </button>
  </div>
</div>

<div class="flex flex-1 overflow-hidden">
  <div class="w-44 bg-white border-r border-gray-100 flex flex-col overflow-y-auto flex-shrink-0">
    <div class="p-3 border-b border-gray-100">
      <p class="text-xxs text-gray-400 uppercase tracking-widest mb-2">เพิ่ม Element</p>
      <div class="space-y-1.5">
        <button onclick="addElement('text')" class="w-full flex items-center gap-2 px-2.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors border border-gray-100">
          <i class="fas fa-font text-primary-400 w-4 text-center"></i> ข้อความ
        </button>
        <button onclick="addElement('image')" class="w-full flex items-center gap-2 px-2.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors border border-gray-100">
          <i class="fas fa-image text-primary-400 w-4 text-center"></i> รูปภาพ
        </button>
        <button onclick="addElement('qrcode')" class="w-full flex items-center gap-2 px-2.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors border border-gray-100">
          <i class="fas fa-qrcode text-primary-400 w-4 text-center"></i> QR Code
        </button>
        <button onclick="addElement('line')" class="w-full flex items-center gap-2 px-2.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors border border-gray-100">
          <i class="fas fa-minus text-primary-400 w-4 text-center"></i> เส้น
        </button>
      </div>
    </div>

    <div class="p-3">
      <p class="text-xxs text-gray-400 uppercase tracking-widest mb-2">พื้นหลัง</p>
      <div class="space-y-2">
        <div>
          <p class="prop-label">สี</p>
          <input type="color" id="bg-color-input" value="<?= e($bgColor) ?>" onchange="setBgColor(this.value)"
            class="w-full h-8 rounded-lg border border-gray-200 cursor-pointer px-1">
        </div>
        <div>
          <p class="prop-label">รูปภาพ</p>
          <label class="w-full flex items-center gap-1.5 px-2 py-1.5 text-xs text-gray-600 bg-gray-50 hover:bg-primary-50 border border-gray-200 rounded-lg cursor-pointer transition-colors">
            <i class="fas fa-upload text-primary-400"></i> อัปโหลด
            <input type="file" id="bg-upload" accept="image/*" class="hidden" onchange="uploadBg(this)">
          </label>
        </div>
        <button onclick="clearBg()" class="w-full text-xxs text-red-500 hover:text-red-600 text-left px-1">
          <i class="fas fa-trash mr-1"></i> ลบรูปพื้นหลัง
        </button>
      </div>

      <p class="text-xxs text-gray-400 uppercase tracking-widest mt-4 mb-2">Variables</p>
      <div class="space-y-1">
        <?php foreach (['{{participant_name}}','{{first_name}}','{{last_name}}','{{project_name}}','{{organizer}}','{{issued_date}}','{{score}}','{{cert_number}}','{{verify_url}}'] as $v): ?>
        <button onclick="copyVar('<?= e($v) ?>')" title="คลิกเพื่อ copy"
          class="w-full text-left text-xxs font-mono text-gray-500 hover:text-primary-500 hover:bg-primary-50 px-1.5 py-0.5 rounded transition-colors truncate">
          <?= e($v) ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="flex-1 flex items-center justify-center overflow-auto bg-gray-300 p-8" id="canvas-area">
    <div id="canvas-wrap">
      <div id="bg-layer" style="background-color:<?= e($bgColor) ?>;<?= $bgImage ? 'background-image:url(' . e(BASE_URL . '/' . $bgImage) . ')' : '' ?>"></div>
    </div>
  </div>

  <div class="w-56 bg-white border-l border-gray-100 overflow-y-auto flex-shrink-0" id="props-panel">
    <div class="p-3">
      <p class="text-xxs text-gray-400 uppercase tracking-widest mb-3">Properties</p>
      <div id="props-empty" class="text-xs text-gray-400 text-center py-8">
        <i class="fas fa-hand-pointer text-2xl text-gray-200 block mb-2"></i>
        คลิก element เพื่อแก้ไข
      </div>
      <div id="props-form" class="hidden space-y-3"></div>
    </div>
  </div>
</div>

<input type="hidden" id="tpl-id" value="<?= (int) $templateId ?>">
<input type="hidden" id="tpl-orientation" value="<?= e($template['orientation'] ?? 'L') ?>">
<input type="hidden" id="csrf-token" value="<?= e(generateCsrfToken()) ?>">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= e(BASE_URL) ?>';
const CSRF_TOKEN = document.getElementById('csrf-token').value;
const CANVAS_W_PX = 1123; // A4 Landscape at 96 DPI
const CANVAS_H_PX = 794;
const MM_W = 297;
const MM_H = 210;

// Use a single scale factor for both axes to prevent distortion
const PX_PER_MM = CANVAS_W_PX / MM_W; // ~3.781

function mmToPx(mm, axis = 'x') {
  return mm * PX_PER_MM;
}
function pxToMm(px, axis = 'x') {
  return parseFloat((px / PX_PER_MM).toFixed(3));
}
function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
}

let elements = <?= json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
let selectedId = null;
let idCounter = elements.length;
let bgColor = '<?= e($bgColor) ?>';
let bgImagePath = '<?= e((string) $bgImage) ?>';
let isDragging = false;
let isResizing = false;
let dragStart = {};
let resizeDir = '';

function renderCanvas() {
  const canvas = document.getElementById('canvas-wrap');
  const orient = document.getElementById('tpl-orientation').value;
  canvas.classList.toggle('portrait', orient === 'P');
  canvas.querySelectorAll('.cert-el').forEach(el => el.remove());

  elements.forEach(el => {
    const div = document.createElement('div');
    div.className = 'cert-el' + (el.id === selectedId ? ' selected' : '');
    div.dataset.id = el.id;

    const pxX = mmToPx(Number(el.x || 0), 'x');
    const pxY = mmToPx(Number(el.y || 0), 'y');
    const pxW = mmToPx(Number(el.w || 1), 'x');
    const pxH = el.type === 'line' ? Math.max(mmToPx(Number(el.h || 0.5), 'y'), 4) : mmToPx(Number(el.h || 1), 'y');
    let left = pxX;
    const top = pxY;
    if (el.anchor === 'center') left = pxX - pxW / 2;

    div.style.cssText = `left:${left}px; top:${top}px; width:${pxW}px; height:${pxH}px;`;

    if (el.type === 'text') {
      const s = el.style || {};
      const label = escapeHtml(el.content || '').replace(/\{\{(\w+)\}\}/g, '<span style="color:#E87722;font-size:0.8em">[$1]</span>');
      div.innerHTML = `<div style="
        width:100%; height:100%; overflow:hidden;
        font-size:${Number(s.size || 16) * 1.35}px;
        font-weight:${s.bold ? '700' : '400'};
        font-style:${s.italic ? 'italic' : 'normal'};
        color:${s.color || '#000'};
        text-align:${(s.align || 'C') === 'C' ? 'center' : (s.align === 'R' ? 'right' : 'left')};
        line-height:1.3; display:flex; align-items:center;
        justify-content:${(s.align || 'C') === 'C' ? 'center' : (s.align === 'R' ? 'flex-end' : 'flex-start')};
        pointer-events:none; padding:2px;
      ">${label}</div>`;
    } else if (el.type === 'image') {
      const src = el.content ? BASE_URL + '/' + encodeURI(el.content) : '';
      div.innerHTML = src
        ? `<img src="${src}" style="width:100%;height:100%;object-fit:contain;pointer-events:none;">`
        : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border:1px dashed #d1d5db;font-size:11px;color:#9ca3af;pointer-events:none"><i class="fas fa-image"></i></div>`;
    } else if (el.type === 'qrcode') {
      div.innerHTML = `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f9fafb;border:1px solid #e5e7eb;pointer-events:none">
        <i class="fas fa-qrcode" style="font-size:${pxW * 0.6}px;color:#374151;"></i></div>`;
    } else if (el.type === 'line') {
      const s = el.style || {};
      div.innerHTML = `<div style="width:100%;border-top:${s.lineWidth || 0.5}px solid ${s.color || '#999'};margin-top:${pxH / 2 - 1}px;pointer-events:none;"></div>`;
    }

    if (el.id === selectedId && el.type !== 'line') {
      ['nw','ne','sw','se'].forEach(dir => {
        const h = document.createElement('div');
        h.className = `resize-handle ${dir}`;
        h.dataset.dir = dir;
        div.appendChild(h);
      });
    }

    canvas.appendChild(div);
    bindEvents(div, el);
  });
}

function bindEvents(div, el) {
  div.addEventListener('mousedown', e => {
    if (e.target.classList.contains('resize-handle')) {
      e.preventDefault();
      isResizing = true;
      resizeDir = e.target.dataset.dir;
      dragStart = { x: e.clientX, y: e.clientY, el: {...el} };
      return;
    }
    e.preventDefault();
    isDragging = true;
    selectElement(el.id);
    dragStart = { mouseX: e.clientX, mouseY: e.clientY, elX: el.x, elY: el.y };
  });
}

document.addEventListener('mousemove', e => {
  if (!isDragging && !isResizing) return;
  const elData = elements.find(x => x.id === selectedId);
  if (!elData) return;

  if (isDragging) {
    const dx = pxToMm(e.clientX - dragStart.mouseX, 'x');
    const dy = pxToMm(e.clientY - dragStart.mouseY, 'y');
    elData.x = parseFloat((Number(dragStart.elX) + dx).toFixed(3));
    elData.y = parseFloat((Number(dragStart.elY) + dy).toFixed(3));
  } else if (isResizing) {
    const orig = dragStart.el;
    if (resizeDir.includes('e')) elData.w = Math.max(5, Number(orig.w) + pxToMm(e.clientX - dragStart.x, 'x'));
    if (resizeDir.includes('s')) elData.h = Math.max(2, Number(orig.h) + pxToMm(e.clientY - dragStart.y, 'y'));
    if (resizeDir.includes('w')) {
      const dw = pxToMm(e.clientX - dragStart.x, 'x');
      elData.x = Number(orig.x) + dw;
      elData.w = Math.max(5, Number(orig.w) - dw);
    }
    if (resizeDir.includes('n')) {
      const dh = pxToMm(e.clientY - dragStart.y, 'y');
      elData.y = Number(orig.y) + dh;
      elData.h = Math.max(2, Number(orig.h) - dh);
    }
  }

  renderCanvas();
  if (selectedId) updatePropsForm();
});

document.addEventListener('mouseup', () => {
  isDragging = false;
  isResizing = false;
});

document.getElementById('canvas-area').addEventListener('mousedown', e => {
  if (e.target.id === 'canvas-area' || e.target.id === 'canvas-wrap' || e.target.id === 'bg-layer') {
    selectedId = null;
    renderCanvas();
    showPropsEmpty();
  }
});

function selectElement(id) {
  selectedId = id;
  renderCanvas();
  updatePropsForm();
}

function showPropsEmpty() {
  document.getElementById('props-empty').classList.remove('hidden');
  document.getElementById('props-form').classList.add('hidden');
}

function updatePropsForm() {
  const el = elements.find(x => x.id === selectedId);
  if (!el) return showPropsEmpty();

  document.getElementById('props-empty').classList.add('hidden');
  document.getElementById('props-form').classList.remove('hidden');

  const s = el.style || {};
  let html = `
    <div><p class="prop-label">Type: <strong>${escapeHtml(el.type)}</strong></p></div>
    <div class="grid grid-cols-2 gap-1.5">
      <div><p class="prop-label">X (mm)</p><input class="prop-input" type="number" step="0.5" value="${el.x}" onchange="updateEl('x', parseFloat(this.value))"></div>
      <div><p class="prop-label">Y (mm)</p><input class="prop-input" type="number" step="0.5" value="${el.y}" onchange="updateEl('y', parseFloat(this.value))"></div>
      <div><p class="prop-label">W (mm)</p><input class="prop-input" type="number" step="0.5" value="${el.w}" onchange="updateEl('w', parseFloat(this.value))"></div>
      <div><p class="prop-label">H (mm)</p><input class="prop-input" type="number" step="0.5" value="${el.h}" onchange="updateEl('h', parseFloat(this.value))"></div>
    </div>
    <div>
      <p class="prop-label">Anchor</p>
      <select class="prop-input" onchange="updateEl('anchor', this.value)">
        <option value="topleft" ${el.anchor === 'topleft' ? 'selected' : ''}>Top-Left</option>
        <option value="center" ${el.anchor === 'center' ? 'selected' : ''}>Center</option>
      </select>
    </div>`;

  if (el.type === 'text') {
    html += `
      <div><p class="prop-label">ข้อความ / Variable</p><textarea class="prop-input" rows="3" onchange="updateEl('content', this.value)">${escapeHtml(el.content)}</textarea></div>
      <div><p class="prop-label">ขนาด Font (pt)</p><input class="prop-input" type="number" min="1" max="300" value="${s.size || 16}" onchange="updateStyle('size', Math.min(300, Math.max(1, parseInt(this.value) || 16)))"></div>
      <div class="flex items-center gap-3">
        <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" ${s.bold ? 'checked' : ''} onchange="updateStyle('bold', this.checked)"> Bold</label>
        <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" ${s.italic ? 'checked' : ''} onchange="updateStyle('italic', this.checked)"> Italic</label>
      </div>
      <div><p class="prop-label">Align</p><select class="prop-input" onchange="updateStyle('align', this.value)">
        <option value="L" ${(s.align || 'C') === 'L' ? 'selected' : ''}>Left</option>
        <option value="C" ${(s.align || 'C') === 'C' ? 'selected' : ''}>Center</option>
        <option value="R" ${(s.align || 'C') === 'R' ? 'selected' : ''}>Right</option>
      </select></div>
      <div><p class="prop-label">สีตัวอักษร</p><input type="color" class="w-full h-8 rounded-lg border border-gray-200 cursor-pointer px-1" value="${s.color || '#000000'}" onchange="updateStyle('color', this.value)"></div>`;
  }

  if (el.type === 'image') {
    html += `<div><p class="prop-label">อัปโหลดรูป</p><label class="w-full flex items-center gap-1.5 px-2 py-1.5 text-xs text-gray-600 bg-gray-50 hover:bg-primary-50 border border-gray-200 rounded-lg cursor-pointer">
      <i class="fas fa-upload text-primary-400"></i> เลือกไฟล์<input type="file" accept="image/*" class="hidden" onchange="uploadElementImage(this)"></label></div>`;
  }

  if (el.type === 'line') {
    html += `
      <div><p class="prop-label">สีเส้น</p><input type="color" class="w-full h-8 rounded-lg border border-gray-200 cursor-pointer px-1" value="${s.color || '#999999'}" onchange="updateStyle('color', this.value)"></div>
      <div><p class="prop-label">ความหนา (mm)</p><input class="prop-input" type="number" step="0.1" value="${s.lineWidth || 0.3}" onchange="updateStyle('lineWidth', parseFloat(this.value))"></div>`;
  }

  html += `<div class="pt-1 border-t border-gray-100"><button onclick="deleteElement()" class="w-full flex items-center justify-center gap-2 py-2 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg border border-red-200 transition-colors"><i class="fas fa-trash text-xs"></i> ลบ Element</button></div>`;
  document.getElementById('props-form').innerHTML = html;
}

function updateEl(key, val) {
  const el = elements.find(x => x.id === selectedId);
  if (!el) return;
  el[key] = val;
  renderCanvas();
}

function updateStyle(key, val) {
  const el = elements.find(x => x.id === selectedId);
  if (!el) return;
  if (!el.style) el.style = {};
  el.style[key] = val;
  renderCanvas();
}

function addElement(type) {
  idCounter++;
  const id = 'el_' + idCounter;
  const defaults = {
    text: { x:148.5, y:100, w:150, h:15, anchor:'center', content:'ข้อความใหม่', style:{font:'thsarabunnew', size:20, bold:false, color:'#1A1A1A', align:'C'} },
    image: { x:20, y:20, w:40, h:28, anchor:'topleft', content:'', style:{} },
    qrcode: { x:257, y:175, w:25, h:25, anchor:'topleft', content:'{{verify_url}}', style:{} },
    line: { x:40, y:165, w:80, h:0, anchor:'topleft', content:'', style:{color:'#AAAAAA', lineWidth:0.3} },
  };
  elements.push({ id, type, ...defaults[type] });
  selectElement(id);
}

function deleteElement() {
  elements = elements.filter(x => x.id !== selectedId);
  selectedId = null;
  renderCanvas();
  showPropsEmpty();
}

function setBgColor(color) {
  bgColor = color;
  bgImagePath = '';
  const layer = document.getElementById('bg-layer');
  layer.style.backgroundColor = color;
  layer.style.backgroundImage = '';
}

function uploadBg(input) {
  if (!input.files[0]) return;
  const fd = new FormData();
  fd.append('file', input.files[0]);
  fd.append('type', 'bg');
  fd.append('csrf_token', CSRF_TOKEN);
  $.ajax({ url: BASE_URL + '/api/upload_asset.php', method: 'POST', data: fd, processData: false, contentType: false, dataType: 'json' })
   .done(res => {
     if (res.success) {
       bgImagePath = res.path;
       document.getElementById('bg-layer').style.backgroundImage = `url(${BASE_URL}/${res.path})`;
     } else {
       Swal.fire({ icon:'error', title:'อัปโหลดไม่สำเร็จ', text: res.message || 'Upload failed' });
     }
   });
}

function clearBg() {
  bgImagePath = '';
  document.getElementById('bg-layer').style.backgroundImage = '';
}

function uploadElementImage(input) {
  if (!input.files[0]) return;
  const fd = new FormData();
  fd.append('file', input.files[0]);
  fd.append('type', 'element');
  fd.append('csrf_token', CSRF_TOKEN);
  $.ajax({ url: BASE_URL + '/api/upload-asset', method: 'POST', data: fd, processData: false, contentType: false, dataType: 'json' })
   .done(res => {
     if (res.success) updateEl('content', res.path);
     else Swal.fire({ icon:'error', title:'อัปโหลดไม่สำเร็จ', text: res.message || 'Upload failed' });
   });
}

function copyVar(v) {
  navigator.clipboard?.writeText(v);
  const el = elements.find(x => x.id === selectedId);
  if (el && el.type === 'text') {
    el.content = v;
    renderCanvas();
    updatePropsForm();
  }
}

function saveTemplate() {
  const payload = {
    id: document.getElementById('tpl-id').value,
    name: document.getElementById('tpl-name').value,
    orientation: document.getElementById('tpl-orientation').value,
    bg_color: bgColor,
    bg_image: bgImagePath,
    bg_type: bgImagePath ? 'image' : 'color',
    elements: JSON.stringify(elements),
    csrf_token: CSRF_TOKEN,
  };
  $.post(BASE_URL + '/api/save-template', payload, res => {
    if (res.success) {
      if (!payload.id && res.id) document.getElementById('tpl-id').value = res.id;
      Swal.fire({ icon:'success', title:'บันทึกสำเร็จ', timer:1500, showConfirmButton:false, customClass:{popup:'rounded-2xl font-sans'} });
    } else {
      Swal.fire({ icon:'error', title:'เกิดข้อผิดพลาด', text: res.message || 'Save failed', customClass:{popup:'rounded-2xl font-sans'} });
    }
  }, 'json');
}

function previewPDF() {
  const id = document.getElementById('tpl-id').value;
  if (!id) {
    Swal.fire({icon:'info', text:'กรุณาบันทึกก่อน Preview', customClass:{popup:'rounded-2xl font-sans'}});
    return;
  }
  window.open(BASE_URL + '/certificates/preview_pdf?template_id=' + encodeURIComponent(id), '_blank');
}

function changeOrientation() {
  document.getElementById('tpl-orientation').value = document.getElementById('orientation-select').value;
}

renderCanvas();
</script>
</body>
</html>
