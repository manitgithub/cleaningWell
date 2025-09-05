<?php

use yii\db\Migration;

/**
 * Full initialization migration for Project + Finance + Housekeeper + Payroll
 */
class m250904_140000_full_init extends Migration
{
    private string $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

    public function safeUp()
    {
        /** ================= CORE ================= */
        $this->createTable('{{%users}}', [
            'id' => $this->bigPrimaryKey(),
            'username' => $this->string(64)->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'display_name' => $this->string(128),
            'phone' => $this->string(32),
            'email' => $this->string(128),
            'role' => $this->tinyInteger()->notNull()->defaultValue(1), // 1=admin,2=housekeeper
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'device_id' => $this->string(128),
            'last_login_at' => $this->dateTime(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%customer_types}}', [
            'id' => $this->tinyInteger()->notNull(),
            'name' => $this->string(64)->notNull(),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $this->tableOptions);
        $this->addPrimaryKey('pk_customer_types', '{{%customer_types}}', 'id');

        $this->createTable('{{%settings}}', [
            'id' => $this->bigPrimaryKey(),
            'key' => $this->string(100)->notNull()->unique(),
            'value' => $this->text()->notNull(),
        ], $this->tableOptions);

        $this->batchInsert('{{%customer_types}}', ['id','name','is_active'], [
            [1,'Individual',1],
            [2,'Company',1],
            [3,'Government',1],
            [9,'Other',1],
        ]);

        /** ================= MASTER ================= */
        $this->createTable('{{%customers}}', [
            'id' => $this->bigPrimaryKey(),
            'customer_type_id' => $this->tinyInteger()->notNull(),
            'name' => $this->string(255)->notNull(),
            'branch' => $this->string(128),
            'tax_id' => $this->string(32),
            'citizen_id' => $this->string(32),
            'address' => $this->text(),
            'phone' => $this->string(32),
            'email' => $this->string(128),
            'contact_name' => $this->string(128),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey('fk_customers_type', '{{%customers}}', 'customer_type_id', '{{%customer_types}}', 'id', 'RESTRICT');

        $this->createTable('{{%projects}}', [
            'id' => $this->bigPrimaryKey(),
            'code' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(255)->notNull(),
            'customer_id' => $this->bigInteger()->notNull(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'budget' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'notes' => $this->text(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey('fk_projects_customer', '{{%projects}}', 'customer_id', '{{%customers}}', 'id', 'RESTRICT');

        $this->createTable('{{%items}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(255)->notNull(),
            'unit' => $this->string(32)->notNull()->defaultValue('หน่วย'),
            'base_price' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'vat_applicable' => $this->tinyInteger()->notNull()->defaultValue(1),
            'wht_default' => $this->decimal(5,2)->notNull()->defaultValue(0),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%doc_counters}}', [
            'id' => $this->bigPrimaryKey(),
            'doc_type' => $this->string(20)->notNull(),
            'period_key' => $this->string(10)->notNull(),
            'last_number' => $this->integer()->notNull()->defaultValue(0),
        ], $this->tableOptions);
        $this->createIndex('ux_doc_counters_type_period', '{{%doc_counters}}', ['doc_type','period_key'], true);

        /** ================= FINANCE ================= */
        $this->createTable('{{%quotations}}', [
            'id' => $this->bigPrimaryKey(),
            'code' => $this->string(32)->notNull()->unique(),
            'project_id' => $this->bigInteger()->notNull(),
            'customer_id' => $this->bigInteger()->notNull(),
            'date' => $this->date()->notNull(),
            'valid_until' => $this->date(),
            'sub_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'discount_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'vat_rate' => $this->decimal(5,2)->notNull()->defaultValue(7.00),
            'vat_amount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'wht_rate' => $this->decimal(5,2)->notNull()->defaultValue(0),
            'wht_amount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'grand_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'payment_terms' => $this->string(255),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%quotation_items}}', [
            'id' => $this->bigPrimaryKey(),
            'quotation_id' => $this->bigInteger()->notNull(),
            'item_id' => $this->bigInteger(),
            'description' => $this->text()->notNull(),
            'qty' => $this->decimal(12,2)->notNull()->defaultValue(1),
            'unit_price' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'line_discount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'line_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
        ], $this->tableOptions);

        $this->createTable('{{%invoices}}', [
            'id' => $this->bigPrimaryKey(),
            'code' => $this->string(32)->notNull()->unique(),
            'project_id' => $this->bigInteger()->notNull(),
            'customer_id' => $this->bigInteger()->notNull(),
            'quotation_id' => $this->bigInteger(),
            'date' => $this->date()->notNull(),
            'due_date' => $this->date(),
            'credit_days' => $this->integer()->defaultValue(0),
            'sub_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'discount_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'vat_rate' => $this->decimal(5,2)->notNull()->defaultValue(7.00),
            'vat_amount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'wht_rate' => $this->decimal(5,2)->notNull()->defaultValue(0),
            'wht_amount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'grand_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%invoice_items}}', [
            'id' => $this->bigPrimaryKey(),
            'invoice_id' => $this->bigInteger()->notNull(),
            'item_id' => $this->bigInteger(),
            'description' => $this->text()->notNull(),
            'qty' => $this->decimal(12,2)->notNull()->defaultValue(1),
            'unit_price' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'line_discount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'line_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
        ], $this->tableOptions);

        $this->createTable('{{%receipts}}', [
            'id' => $this->bigPrimaryKey(),
            'code' => $this->string(32)->notNull()->unique(),
            'invoice_id' => $this->bigInteger()->notNull(),
            'received_at' => $this->dateTime()->notNull(),
            'amount' => $this->decimal(12,2)->notNull(),
            'method' => $this->tinyInteger()->notNull()->defaultValue(1),
            'ref_no' => $this->string(64),
            'note' => $this->string(255),
            'attachment_path' => $this->string(255),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%expenses}}', [
            'id' => $this->bigPrimaryKey(),
            'project_id' => $this->bigInteger()->notNull(),
            'paid_at' => $this->dateTime()->notNull(),
            'category' => $this->string(100)->notNull(),
            'amount' => $this->decimal(12,2)->notNull(),
            'payee' => $this->string(255),
            'ref_no' => $this->string(64),
            'note' => $this->string(255),
            'attachment_path' => $this->string(255),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        /** ================= HOUSEKEEPER ================= */
        $this->createTable('{{%service_points}}', [
            'id' => $this->bigPrimaryKey(),
            'project_id' => $this->bigInteger()->notNull(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255),
            'lat' => $this->decimal(10,7)->notNull(),
            'lng' => $this->decimal(10,7)->notNull(),
            'radius_m' => $this->integer()->notNull()->defaultValue(100),
            'note' => $this->string(255),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%shifts}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(64)->notNull(),
            'start_time' => $this->time()->notNull(),
            'end_time' => $this->time()->notNull(),
            'break_minutes' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $this->tableOptions);

        $this->createTable('{{%assignments}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'service_point_id' => $this->bigInteger()->notNull(),
            'shift_id' => $this->bigInteger()->notNull(),
            'work_date' => $this->date()->notNull(),
            'note' => $this->string(255),
            'created_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->createIndex('ux_assignments_unique', '{{%assignments}}', ['user_id','service_point_id','shift_id','work_date'], true);

        $this->createTable('{{%attendances}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'service_point_id' => $this->bigInteger()->notNull(),
            'shift_id' => $this->bigInteger()->notNull(),
            'work_date' => $this->date()->notNull(),
            'checkin_at' => $this->dateTime(),
            'checkin_lat' => $this->decimal(10,7),
            'checkin_lng' => $this->decimal(10,7),
            'checkin_accuracy' => $this->decimal(6,2),
            'checkin_photo_path' => $this->string(255),
            'checkout_at' => $this->dateTime(),
            'checkout_lat' => $this->decimal(10,7),
            'checkout_lng' => $this->decimal(10,7),
            'checkout_accuracy' => $this->decimal(6,2),
            'checkout_photo_path' => $this->string(255),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'minutes_worked' => $this->integer()->notNull()->defaultValue(0),
            'late_minutes' => $this->integer()->notNull()->defaultValue(0),
            'early_leave_minutes' => $this->integer()->notNull()->defaultValue(0),
            'flagged_reason' => $this->text(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);
        $this->createIndex('ux_att_unique', '{{%attendances}}', ['user_id','service_point_id','shift_id','work_date'], true);

        $this->createTable('{{%attendance_logs}}', [
            'id' => $this->bigPrimaryKey(),
            'attendance_id' => $this->bigInteger()->notNull(),
            'action' => $this->string(20)->notNull(),
            'meta_json' => $this->text(),
            'ip' => $this->string(64),
            'user_agent' => $this->string(255),
            'created_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        /** ================= PAYROLL ================= */
        $this->createTable('{{%wage_profiles}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'wage_type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'rate_value' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'ot_policy_id' => $this->bigInteger(),
            'effective_from' => $this->date()->notNull(),
            'effective_to' => $this->date(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%ot_policies}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(128)->notNull(),
            'daily_threshold_hours' => $this->decimal(5,2),
            'weekly_threshold_hours' => $this->decimal(5,2),
            'multiplier' => $this->decimal(5,2)->notNull()->defaultValue(1.50),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $this->tableOptions);

        $this->createTable('{{%allowance_policies}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(128)->notNull(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'amount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'conditions_json' => $this->text(),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $this->tableOptions);

        $this->createTable('{{%deduction_policies}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(128)->notNull(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'amount' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'rule_json' => $this->text(),
            'is_active' => $this->tinyInteger()->notNull()->defaultValue(1),
        ], $this->tableOptions);

        $this->createTable('{{%pay_periods}}', [
            'id' => $this->bigPrimaryKey(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%payroll_runs}}', [
            'id' => $this->bigPrimaryKey(),
            'pay_period_id' => $this->bigInteger()->notNull(),
            'approved_by' => $this->bigInteger(),
            'approved_at' => $this->dateTime(),
            'locked_at' => $this->dateTime(),
            'notes' => $this->text(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%payroll_items}}', [
            'id' => $this->bigPrimaryKey(),
            'payroll_run_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'hours_total' => $this->decimal(6,2)->notNull()->defaultValue(0),
            'ot_hours' => $this->decimal(6,2)->notNull()->defaultValue(0),
            'base_pay' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'ot_pay' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'allowance_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'deduction_total' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'net_pay' => $this->decimal(12,2)->notNull()->defaultValue(0),
            'project_breakdown_json' => $this->text(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createTable('{{%payslips}}', [
            'id' => $this->bigPrimaryKey(),
            'payroll_run_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'slip_no' => $this->string(32)->notNull()->unique(),
            'pdf_path' => $this->string(255),
            'published_at' => $this->dateTime(),
        ], $this->tableOptions);

        $this->createTable('{{%pay_transactions}}', [
            'id' => $this->bigPrimaryKey(),
            'payroll_run_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'paid_at' => $this->dateTime()->notNull(),
            'method' => $this->tinyInteger()->notNull()->defaultValue(1),
            'amount' => $this->decimal(12,2)->notNull(),
            'ref_no' => $this->string(64),
            'note' => $this->string(255),
            'attachment_path' => $this->string(255),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        /** ================= VIEWS ================= */
        $this->execute(<<<SQL
CREATE OR REPLACE VIEW v_cash_ledger AS
SELECT r.received_at AS entry_at,
       'IN'          AS type,
       r.amount      AS amount,
       i.project_id  AS project_id,
       'RECEIPT'     AS ref_type,
       r.id          AS ref_id,
       CONCAT('INV:', i.code, ' RCPT:', r.code) AS note
FROM receipts r
JOIN invoices i ON i.id = r.invoice_id
UNION ALL
SELECT e.paid_at     AS entry_at,
       'OUT'         AS type,
       -e.amount     AS amount,
       e.project_id  AS project_id,
       'EXPENSE'     AS ref_type,
       e.id          AS ref_id,
       e.category    AS note
FROM expenses e;
SQL);

        $this->execute(<<<SQL
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
WHERE i.status IN (1,2,4);
SQL);
    }

    public function safeDown()
    {
        $this->execute('DROP VIEW IF EXISTS v_ar_aging');
        $this->execute('DROP VIEW IF EXISTS v_cash_ledger');

        $this->dropTable('{{%pay_transactions}}');
        $this->dropTable('{{%payslips}}');
        $this->dropTable('{{%payroll_items}}');
        $this->dropTable('{{%payroll_runs}}');
        $this->dropTable('{{%pay_periods}}');
        $this->dropTable('{{%deduction_policies}}');
        $this->dropTable('{{%allowance_policies}}');
        $this->dropTable('{{%ot_policies}}');
        $this->dropTable('{{%wage_profiles}}');

        $this->dropTable('{{%attendance_logs}}');
        $this->dropTable('{{%attendances}}');
        $this->dropTable('{{%assignments}}');
        $this->dropTable('{{%shifts}}');
        $this->dropTable('{{%service_points}}');

        $this->dropTable('{{%expenses}}');
        $this->dropTable('{{%receipts}}');
        $this->dropTable('{{%invoice_items}}');
        $this->dropTable('{{%invoices}}');
        $this->dropTable('{{%quotation_items}}');
        $this->dropTable('{{%quotations}}');

        $this->dropTable('{{%doc_counters}}');
        $this->dropTable('{{%items}}');
        $this->dropTable('{{%projects}}');
        $this->dropTable('{{%customers}}');
        $this->dropTable('{{%settings}}');
        $this->dropTable('{{%customer_types}}');
        $this->dropTable('{{%users}}');
    }
}
