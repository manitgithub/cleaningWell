# 🛠 Roadmap ระบบบริหารจัดการโครงการ + การเงิน + แม่บ้าน + Payroll

## Phase 0: Authentication & Core ✅
- [x] **Auth & Login**
  - [x] ทำระบบ Login (username/password)
  - [x] ใช้ `Yii::$app->security->generatePasswordHash()` เก็บ password
  - [x] Session-based login/logout
  - [x] Middleware ตรวจ role (admin / housekeeper)
- [x] **Users Management**
  - [x] CRUD สำหรับ users
  - [x] Assign role: 1=Admin, 2=Housekeeper
  - [x] Reset Password

---

## Phase 1: UI & Theme ✅
- [x] **Theme**
  - [x] ติดตั้ง AdminLTE 3 
  - [x] Sidebar menu: Master / Finance / Housekeeper / Payroll / Reports
  - [x] Layout: Header, Footer, Breadcrumb
- [x] **Dashboard**
  - [x] Card แสดง summary (ลูกค้า, โครงการ, Invoice คงค้าง, Payroll)
  - [x] กราฟ (รายได้รายเดือน / การเช็คอินแม่บ้าน)

---

## Phase 2: System Settings ✅
- [x] ค่า default VAT, WHT
- [x] Document numbering pattern  
- [x] Logo, Address, Tax ID
- [x] ค่าพื้นฐานอื่น (GPS accuracy, Geofence default)

---

## Phase 3: Master Data ✅
- [x] Customers
- [x] Projects  
- [x] Items

---

## Phase 4: Documents & Finance
- [ ] Quotations (CRUD, Dynamic items, Export PDF)
- [ ] Invoices (แปลงจาก Quotation, Update status)
- [ ] Receipts (บันทึกการรับชำระ, Update invoice balance)
- [ ] Expenses
- [ ] Reports (AR Aging, Cash Ledger)

---

## Phase 5: Housekeeper Module
- [ ] Service Points
- [ ] Shifts
- [ ] Assignments
- [ ] Attendances (Check-in/out + GPS + Photo)

---

## Phase 6: Payroll (Weekly)
- [ ] Wage Profiles
- [ ] Pay Periods
- [ ] Payroll Runs (generate payroll_items)
- [ ] Payslips (PDF)
- [ ] Pay Transactions

---

## Phase 7: Export & Reports (Final Polish)
- [ ] Export PDF: Quotation, Invoice, Receipt, Payslip
- [ ] Export Excel: AR Aging, Cash Ledger, Payroll
- [ ] Final Report Layout/Branding

---
