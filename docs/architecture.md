# 🏗️ ExamCert Architecture

## 📱 System Overview
ระบบใช้สถาปัตยกรรมแบบ **MVC (Model-View-Controller)** โดยมี **Front Controller** เป็นจุดรับงานเพียงจุดเดียว

## 🧭 Routing Pattern
- ทุก request จะถูกส่งมาที่ `index.php`
- `index.php` จะทำการ Match URL และเรียก Controller ที่เกี่ยวข้อง
- ห้ามมีไฟล์ PHP อื่นๆ ใน root directory นอกเหนือจากไฟล์ระบบหลัก

## 층 Layers
1.  **Models**: จัดการกับฐานข้อมูลโดยตรง (PDO) ห้ามมี HTML
2.  **Controllers**: รับค่าจาก Request, เรียก Model, และเลือก View ที่จะแสดงผล
3.  **Views**: แสดงผล HTML/Tailwind ห้ามมีการ Query ฐานข้อมูลในหน้านี้
4.  **API**: สำหรับการรับ-ส่งข้อมูลแบบ AJAX (JSON)

## 📁 Key File Location
- กฎโปรเจกต์: `PROJECT.md`
- กฎ AI: `.agents/rules/`
- โค้ดหลัก: `controllers/`, `models/`, `views/`
