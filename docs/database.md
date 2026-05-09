# 🗄️ Database & Schema Rules

## 🆔 Naming Conventions
- **Tables**: ใช้พหูพจน์ (Plural) เช่น `projects`, `participants`.
- **Columns**: ใช้ snake_case เช่น `project_id`, `first_name`.
- **Primary Key**: ใช้ `id` (AUTO_INCREMENT).
- **Foreign Keys**: ใช้ `singular_table_name_id` เช่น `project_id`.

## 🛡️ Constraints
- ทุกตารางต้องมี `created_at` และ `updated_at`.
- ใช้ **Foreign Keys** เสมอเพื่อรักษาความสัมพันธ์ของข้อมูล.
- ห้ามลบข้อมูลหลัก (Soft Delete) ในอนาคตถ้าจำเป็น แต่ตอนนี้ใช้ Hard Delete ตาม Schema v1.

## 📝 Performance
- เพิ่ม **Index** ใน Column ที่มีการค้นหาบ่อย เช่น `access_token`, `project_id`, `verify_token`.
- ใช้ `utf8mb4` สำหรับภาษาไทยที่ถูกต้อง.
