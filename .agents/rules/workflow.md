# 🔄 Development Workflow Rules

## 📝 Worklog Management
- AI ต้องอัปเดต `WORKLOG.md` หลังจบงานใหญ่ทุกครั้ง.
- แยกบันทึกเป็นรายวัน.
- ระบุไฟล์ที่แก้ไขและสิ่งที่ทำเสร็จให้ชัดเจน.

## 🛡️ Git Checkpoint (Safety First)
- **Status Check**: ตรวจ `git status` ก่อนเริ่มงาน.
- **Diff Check**: ตรวจ `git diff` หลังจบ Milestone ย่อย.
- **Commit**: ใช้รูปแบบ `checkpoint: <description>`.
- **Restoration**: หากงานมั่ว ให้ใช้ `git restore .` ทันทีเพื่อกลับสู่สถานะที่ปลอดภัย.

## 🧠 Chain of Thought Enforcement
- ก่อนเริ่มแก้โค้ด AI ต้องระบุ "เป้าหมาย" และ "กฎที่เกี่ยวข้อง" ให้ผู้ใช้ทราบก่อนเสมอ เพื่อให้ผู้ใช้กด Approve แผนการก่อน.
