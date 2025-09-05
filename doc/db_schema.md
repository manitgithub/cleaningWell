# DB Schema — Project+Finance+Housekeeper (MySQL 8.0)

## 0) Conventions
- Charset: `utf8mb4`, Collation: `utf8mb4_0900_ai_ci`, Engine: `InnoDB`
- เวลา: เก็บเป็น `DATETIME` (UTC) ชื่อฟิลด์ `created_at`, `updated_at`
- เงิน: `DECIMAL(12,2)`; พิกัด: `DECIMAL(10,7)`; ความแม่นยำ GPS: `DECIMAL(6,2)` (เมตร)
- รหัสเอกสาร: เก็บใน `code` (Unique) + ตาราง `doc_counters` สำหรับวิ่งเลข
- สถานะ: ใช้ `TINYINT` (อ้างอิงในตาราง) เพื่อปรับขยายได้ง่าย
- ลบข้อมูล: ใช้การจำกัด FK (`RESTRICT`) และสถานะยกเลิก (ไม่ soft-delete)

---

## 1) Security / Users
### 1.1 users
- `id` PK
- `username` (unique), `password_hash`, `display_name`, `phone`, `email`
- `role` ENUM-like via tinyint: 1=admin, 2=housekeeper
- `status` tinyint (1=active,0=inactive)
- `device_id` (nullable, bind มือถือแม่บ้าน)
- `last_login_at` DATETIME
- `created_at`, `updated_at`

**Indexes**
- `ux_users_username` UNIQUE(username)
- `ix_users_role_status` (role, status)

---

## 2) Master Data
### 2.1 customer_types
- `id` PK (1=Individual, 2=Company, 3=Government, 9=Other)
- `name` (th/en), `is_active` tinyint

### 2.2 customers
- `id` PK
- `customer_type_id` FK → customer_types.id
- `name` (บุคคล/บริษัท/หน่วยงาน)
- `branch` (เช่น Head Office/สาขา)
- `tax_id` / `citizen_id` (ตามประเภทลูกค้า)
- `address` TEXT, `phone`, `email`, `contact_name`
- `status` tinyint (1=active,0=inactive)
- `created_at`, `updated_at`

**Indexes**
- `ix_customers_type_status` (customer_type_id, status)
- `ix_customers_tax` (tax_id)

### 2.3 projects
- `id` PK
- `code` (unique), `name`
- `customer_id` FK → customers.id
- `start_date` DATE, `end_date` DATE, `budget` DECIMAL(12,2)
- `status` tinyint (0=draft,1=active,2=on_hold,3=closed)
- `notes` TEXT
- `created_at`, `updated_at`

**Indexes**
- `ux_projects_code` UNIQUE(code)
- `ix_projects_customer_status` (customer_id, status)

### 2.4 items (catalog)
- `id` PK
- `name`, `unit`, `base_price` DECIMAL(12,2)
- `vat_applicable` tinyint(1), `wht_default` DECIMAL(5,2) (เช่น 3.00)
- `is_active` tinyint
- `created_at`, `updated_at`

---

## 3) Document & Finance
### 3.1 quotations (ใบเสนอราคา)
- `id` PK
- `code` UNIQUE (เช่น Q-YYYYMM-####)
- `project_id` FK → projects.id
- `customer_id` FK → customers.id
- `date` DATE, `valid_until` DATE
- ตัวเงิน: `sub_total`, `discount_total`, `vat_rate` DECIMAL(5,2), `vat_amount`, `wht_rate`, `wht_amount`, `grand_total`
- `payment_terms` VARCHAR(255)
- `status` tinyint (0=draft,1=sent,2=approved,3=rejected,4=cancelled)
- `created_at`, `updated_at`

**Indexes**
- `ux_quotations_code` UNIQUE(code)
- `ix_quotations_customer_status` (customer_id, status)

### 3.2 quotation_items
- `id` PK
- `quotation_id` FK → quotations.id
- `item_id` (nullable, reference catalog)
- `description` TEXT
- `qty` DECIMAL(12,2), `unit_price` DECIMAL(12,2), `line_discount` DECIMAL(12,2)
- `line_total` DECIMAL(12,2)
- `sort_order` INT

**Index**
- `ix_qitems_quotation` (quotation_id, sort_order)

### 3.3 invoices (ใบวางบิล/ใบแจ้งหนี้)
- `id` PK
- `code` UNIQUE (INV-YYYYMM-####)
- `project_id` FK → projects.id
- `customer_id` FK → customers.id
- `quotation_id` (nullable) FK → quotations.id
- `date` DATE, `due_date` DATE, `credit_days` INT
- ตัวเงิน: โครงเดียวกับ quotations (sub_total, vat_rate, …, grand_total)
- `status` tinyint (0=draft,1=sent,2=partially_paid,3=paid,4=overdue,5=cancelled)
- `created_at`, `updated_at`

**Indexes**
- `ux_invoices_code` UNIQUE(code)
- `ix_invoices_customer_status` (customer_id, status)
- `ix_invoices_due` (due_date, status)

### 3.4 invoice_items
- โครงเหมือน `quotation_items` แต่ FK เป็น `invoice_id`

### 3.5 receipts (รับชำระ)
- `id` PK
- `code` UNIQUE (RCPT-YYYYMM-####)
- `invoice_id` FK → invoices.id
- `received_at` DATETIME
- `amount` DECIMAL(12,2)
- `method` tinyint (1=transfer,2=cash,3=cheque,4=PromptPay,9=other)
- `ref_no` (เลขอ้างอิงธนาคาร/เช็ค), `note`, `attachment_path`
- `created_at`, `updated_at`

**Index**
- `ix_receipts_invoice` (invoice_id, received_at)

### 3.6 expenses (จ่ายเงินโครงการ)
- `id` PK
- `project_id` FK → projects.id
- `paid_at` DATETIME
- `category` VARCHAR(100) (เช่น ค่าคนงาน/อุปกรณ์/เดินทาง)
- `amount` DECIMAL(12,2)
- `payee` VARCHAR(255) (ผู้รับเงิน/ร้านค้า)
- `ref_no`, `note`, `attachment_path`
- `created_at`, `updated_at`

### 3.7 doc_counters (เลขรันเอกสาร)
- `id` PK
- `doc_type` VARCHAR(20) (quotation/invoice/receipt/payslip)
- `period_key` VARCHAR(10) (เช่น `YYYYMM`)
- `last_number` INT
- UNIQUE(doc_type, period_key)

### 3.8 settings (ค่าเริ่มต้นระบบ)
- `id` PK
- `key` UNIQUE, `value` TEXT (JSON ได้) — เช่น default_vat, numbering_pattern

---

## 4) Housekeeper — Service & Attendance
### 4.1 service_points
- `id` PK
- `project_id` FK → projects.id
- `name`, `address`
- `lat` DECIMAL(10,7), `lng` DECIMAL(10,7), `radius_m` INT (เช่น 100)
- `note`, `is_active` tinyint
- `created_at`, `updated_at`

**Index**
- `ix_service_points_project` (project_id, is_active)

### 4.2 shifts
- `id` PK
- `name` (เช้า/บ่าย/ดึก), `start_time` TIME, `end_time` TIME
- `break_minutes` INT (นาที), `is_active` tinyint

### 4.3 assignments (มอบหมายงานรายวัน)
- `id` PK
- `user_id` FK → users.id (เฉพาะ role=housekeeper)
- `service_point_id` FK → service_points.id
- `shift_id` FK → shifts.id
- `work_date` DATE
- `note`, `created_at`
- UNIQUE(user_id, service_point_id, shift_id, work_date)

### 4.4 attendances
- `id` PK
- `user_id` FK → users.id
- `service_point_id` FK → service_points.id
- `shift_id` FK → shifts.id
- `work_date` DATE
- **Check-in**: `checkin_at` DT, `checkin_lat`, `checkin_lng`, `checkin_accuracy`, `checkin_photo_path`
- **Check-out**: `checkout_at` DT, `checkout_lat`, `checkout_lng`, `checkout_accuracy`, `checkout_photo_path`
- `status` tinyint (0=pending,1=checked_in,2=checked_out,3=flagged,4=approved)
- Metrics: `minutes_worked` INT, `late_minutes` INT, `early_leave_minutes` INT
- `flagged_reason` TEXT
- `created_at`, `updated_at`
- UNIQUE(user_id, service_point_id, shift_id, work_date)

**Indexes**
- `ix_att_user_date` (user_id, work_date)
- `ix_att_point_date` (service_point_id, work_date)

### 4.5 attendance_logs
- `id` PK
- `attendance_id` FK → attendances.id
- `action` VARCHAR(20) (create/checkin/checkout/edit/flag/approve)
- `meta_json` JSON, `ip`, `user_agent`
- `created_at`

---

## 5) Weekly Payroll
### 5.1 wage_profiles (อัตราค่าจ้าง)
- `id` PK
- `user_id` FK → users.id
- `wage_type` tinyint (1=hourly, 2=per_shift, 3=per_point, 4=monthly)
- `rate_value` DECIMAL(12,2)
- `ot_policy_id` (nullable) FK → ot_policies.id
- `effective_from` DATE, `effective_to` DATE (nullable)
- `created_at`, `updated_at`

### 5.2 ot_policies (นโยบาย OT) *(ออปชัน)*
- `id` PK
- `name`
- `daily_threshold_hours` DECIMAL(5,2) (nullable)
- `weekly_threshold_hours` DECIMAL(5,2) (nullable)
- `multiplier` DECIMAL(5,2) (เช่น 1.50)
- `is_active` tinyint

### 5.3 allowance_policies
- `id` PK
- `name`
- `type` tinyint (1=per_day,2=per_shift,3=fixed_per_period)
- `amount` DECIMAL(12,2)
- `conditions_json` JSON (เช่น เงื่อนไขขั้นต่ำชั่วโมง)
- `is_active` tinyint

### 5.4 deduction_policies
- โครงคล้าย allowance_policies แต่เป็น "หัก"

### 5.5 pay_periods (รอบสัปดาห์)
- `id` PK
- `start_date` DATE, `end_date` DATE
- `status` tinyint (0=open,1=closed,2=approved,3=paid)
- `created_at`, `updated_at`
- UNIQUE(start_date, end_date)

### 5.6 payroll_runs (การคำนวณ/ปิดรอบจริง)
- `id` PK
- `pay_period_id` FK → pay_periods.id
- `approved_by` FK → users.id (admin), `approved_at` DATETIME
- `locked_at` DATETIME, `notes`
- `created_at`, `updated_at`

### 5.7 payroll_items (สรุปต่อคนต่อรอบ)
- `id` PK
- `payroll_run_id` FK → payroll_runs.id
- `user_id` FK → users.id
- Hours: `hours_total` DECIMAL(6,2), `ot_hours` DECIMAL(6,2)
- Money: `base_pay`, `ot_pay`, `allowance_total`, `deduction_total`, `net_pay` DECIMAL(12,2)
- `project_breakdown_json` JSON (ต้นทุนตามโครงการ)
- `created_at`, `updated_at`

### 5.8 payslips (สลิป)
- `id` PK
- `payroll_run_id` FK → payroll_runs.id
- `user_id` FK → users.id
- `slip_no` UNIQUE (SLP-YYYYWW-####)
- `pdf_path`, `published_at` DATETIME

### 5.9 pay_transactions (การโอนจ่าย)
- `id` PK
- `payroll_run_id` FK → payroll_runs.id
- `user_id` FK → users.id
- `paid_at` DATETIME, `method` tinyint (1=transfer,2=cash,9=other)
- `amount` DECIMAL(12,2), `ref_no`, `note`, `attachment_path`
- `created_at`, `updated_at`

---

## 6) Attachments & Audit
### 6.1 files (ไฟล์แนบกลาง)
- `id` PK
- `entity_type` VARCHAR(40) (e.g., 'invoice','receipt','attendance')
- `entity_id` BIGINT
- `path`, `original_name`, `mime_type`, `size`
- `created_by` FK → users.id
- `created_at`

### 6.2 audit_logs
- `id` PK
- `entity_type`, `entity_id`
- `action` VARCHAR(30) (create/update/status_change/delete/cancel)
- `old_json` JSON, `new_json` JSON
- `actor_id` FK → users.id
- `created_at`

---

## 7) Views (แนะนำ)
### 7.1 v_cash_ledger
- รวมรายการ **รับ** จาก receipts (บวก) และ **จ่าย** จาก expenses (ลบ) → ใช้สำหรับเล่มเงินสด/กระแสเงินสด

**คอลัมน์**
- `entry_at` DATETIME, `type` ('IN'/'OUT'), `amount`, `project_id`, `ref_type` ('RECEIPT'/'EXPENSE'), `ref_id`, `note`

### 7.2 v_ar_aging
- แยก Invoice ที่สถานะค้างชำระตามช่วงวัน (0–30, 31–60, 61–90, >90)

---

## 8) Relationships (สรุป)
- customers(1) — (n) projects
- projects(1) — (n) service_points, quotations, invoices, expenses
- quotations(1) — (n) quotation_items; invoices(1) — (n) invoice_items
- customers(1) — (n) quotations/invoices
- invoices(1) — (n) receipts
- users(แม่บ้าน)(1) — (n) assignments/attendances
- service_points/shifts — (n) assignments/attendances
- pay_periods(1) — (n) payroll_runs — (n) payroll_items/payslips/pay_transactions
- customers(1) — (n) projects
- customers(1) — (n) quotations, invoices
- projects(1) — (n) service_points, quotations, invoices, expenses
- quotations(1) — (n) quotation_items
- invoices(1) — (n) invoice_items, receipts
- users(แม่บ้าน)(1) — (n) assignments, attendances
- service_points(1) — (n) assignments, attendances
- shifts(1) — (n) assignments, attendances
- attendances(1) — (n) attendance_logs
- pay_periods(1) — (n) payroll_runs
- payroll_runs(1) — (n) payroll_items, payslips, pay_transactions
- users(1) — (n) wage_profiles, payroll_items, payslips, pay_transactions, audit_logs, files

---

## 9) Indexing & Constraints (แนวทาง)
- ทุก FK ใส่ `ON UPDATE RESTRICT ON DELETE RESTRICT` (กันข้อมูลหาย)  
- ตารางเอกสาร (quotations/invoices/receipts) มี `UNIQUE(code)` + ดัชนี `status`, `date/due_date`
- `attendances` ใส่ `UNIQUE(user_id, service_point_id, shift_id, work_date)`
- `assignments` ใส่ `UNIQUE(user_id, service_point_id, shift_id, work_date)`
- ดัชนีช่วยรายงาน:
  - `ix_invoices_due (due_date, status)`
  - `ix_receipts_invoice (invoice_id, received_at)`
  - `ix_expenses_project_date (project_id, paid_at)`
  - `ix_att_user_date (user_id, work_date)`
  - `ix_payroll_run_user (payroll_run_id, user_id)`
- จำนวนเงินใช้ `DECIMAL(12,2)` เสมอ, อัตรา/เปอร์เซ็นต์ `DECIMAL(5,2)`

---

## 10) Document Numbering (doc_counters) — รูปแบบแนะนำ
- โครง `doc_counters(doc_type, period_key=YYYYMM, last_number)`  
- รูปแบบ:
  - Quotation: `Q-{YYYY}{MM}-{####}`
  - Invoice:   `INV-{YYYY}{MM}-{####}`
  - Receipt:   `RCPT-{YYYY}{MM}-{####}`
  - Payslip:   `SLP-{YYYY}{WW}-{####}` (รายสัปดาห์ ใช้ ISO Week)
- รีเซ็ตเลขเมื่อ period เปลี่ยน (เดือน/สัปดาห์ตามชนิดเอกสาร)

---

## 11) Seed Data (ค่าเริ่มต้นที่ควรมี)
- `customer_types`:  
  - 1=Individual, 2=Company, 3=Government, 9=Other (is_active=1)
- `settings` ตัวอย่าง (key → value JSON):
  - `default_vat_rate` → `{"value":7.00}`
  - `numbering_patterns` → `{"quotation":"Q-{YYYY}{MM}-{####}","invoice":"INV-{YYYY}{MM}-{####}","receipt":"RCPT-{YYYY}{MM}-{####}","payslip":"SLP-{YYYY}{WW}-{####}"}`
  - `gps_accuracy_max_m` → `{"value":50}`
  - `geofence_default_m` → `{"value":100}`
  - `pay_period_week_start` → `{"value":"MON"}`

---

## 12) Views (ตัวอย่างสคริปต์)
### 12.1 v_cash_ledger
รวมเงินรับจาก `receipts` (+) และเงินจ่ายจาก `expenses` (−)
```sql
CREATE OR REPLACE VIEW v_cash_ledger AS
SELECT r.received_at     AS entry_at,
       'IN'              AS type,
       r.amount          AS amount,
       i.project_id      AS project_id,
       'RECEIPT'         AS ref_type,
       r.id              AS ref_id,
       CONCAT('INV:', i.code, ' RCPT:', r.code) AS note
FROM receipts r
JOIN invoices i ON i.id = r.invoice_id
UNION ALL
SELECT e.paid_at         AS entry_at,
       'OUT'             AS type,
       -e.amount         AS amount,
       e.project_id      AS project_id,
       'EXPENSE'         AS ref_type,
       e.id              AS ref_id,
       e.category        AS note;

CREATE OR REPLACE VIEW v_ar_aging AS
SELECT i.id AS invoice_id, i.code, i.customer_id, i.project_id, i.date, i.due_date,
       i.grand_total,
       IFNULL((SELECT SUM(amount) FROM receipts r WHERE r.invoice_id=i.id),0) AS received_total,
       (i.grand_total - IFNULL((SELECT SUM(amount) FROM receipts r WHERE r.invoice_id=i.id),0)) AS balance,
       CASE
         WHEN i.status IN (3,5) THEN 0
         ELSE DATEDIFF(UTC_DATE(), i.due_date)
       END AS days_overdue,
       CASE
         WHEN DATEDIFF(UTC_DATE(), i.due_date) <= 0 THEN 'CURRENT'
         WHEN DATEDIFF(UTC_DATE(), i.due_date) BETWEEN 1 AND 30 THEN '1-30'
         WHEN DATEDIFF(UTC_DATE(), i.due_date) BETWEEN 31 AND 60 THEN '31-60'
         WHEN DATEDIFF(UTC_DATE(), i.due_date) BETWEEN 61 AND 90 THEN '61-90'
         ELSE '>90'
       END AS bucket
FROM invoices i
WHERE i.status IN (1,2,4); -- sent/partially_paid/overdue
-- users
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(64) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(128),
  phone VARCHAR(32), email VARCHAR(128),
  role TINYINT NOT NULL,         -- 1=admin,2=housekeeper
  status TINYINT NOT NULL DEFAULT 1,
  device_id VARCHAR(128),
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- customer_types
CREATE TABLE customer_types (
  id TINYINT PRIMARY KEY,
  name VARCHAR(64) NOT NULL,
  is_active TINYINT NOT NULL DEFAULT 1
);

-- customers
CREATE TABLE customers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  customer_type_id TINYINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  branch VARCHAR(128),
  tax_id VARCHAR(32),
  citizen_id VARCHAR(32),
  address TEXT,
  phone VARCHAR(32),
  email VARCHAR(128),
  contact_name VARCHAR(128),
  status TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_cust_type FOREIGN KEY (customer_type_id) REFERENCES customer_types(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- projects
CREATE TABLE projects (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(32) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  customer_id BIGINT NOT NULL,
  start_date DATE, end_date DATE,
  budget DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  status TINYINT NOT NULL DEFAULT 1,
  notes TEXT,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_proj_customer FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- quotations (หลัก ๆ)
CREATE TABLE quotations (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(32) NOT NULL UNIQUE,
  project_id BIGINT NOT NULL,
  customer_id BIGINT NOT NULL,
  date DATE NOT NULL,
  valid_until DATE NULL,
  sub_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  discount_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  vat_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00,
  vat_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  wht_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  wht_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  grand_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_terms VARCHAR(255),
  status TINYINT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_q_proj FOREIGN KEY (project_id) REFERENCES projects(id),
  CONSTRAINT fk_q_customer FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- quotation_items
CREATE TABLE quotation_items (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  quotation_id BIGINT NOT NULL,
  item_id BIGINT NULL,
  description TEXT NOT NULL,
  qty DECIMAL(12,2) NOT NULL DEFAULT 1.00,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  line_discount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_qi_q FOREIGN KEY (quotation_id) REFERENCES quotations(id)
);

-- invoices / invoice_items, receipts, expenses, service_points, shifts,
-- assignments, attendances, attendance_logs, wage_profiles, pay_periods,
-- payroll_runs, payroll_items, payslips, pay_transactions, doc_counters, settings
-- => ใช้โครงสร้างตามข้อ 3–7 ด้านบน (สร้างตามรูปแบบเดียวกัน)


erDiagram
  customers ||--o{ projects : owns
  customer_types ||--o{ customers : class
  projects ||--o{ quotations : has
  projects ||--o{ invoices : has
  quotations ||--o{ quotation_items : contains
  invoices ||--o{ invoice_items : contains
  invoices ||--o{ receipts : receives
  users ||--o{ assignments : has
  service_points ||--o{ assignments : at
  shifts ||--o{ assignments : in
  users ||--o{ attendances : logs
  service_points ||--o{ attendances : at
  shifts ||--o{ attendances : in
  attendances ||--o{ attendance_logs : has
  pay_periods ||--o{ payroll_runs : groups
  payroll_runs ||--o{ payroll_items : summarizes
  payroll_runs ||--o{ payslips : issues
  payroll_runs ||--o{ pay_transactions : pays
