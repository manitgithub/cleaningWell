<?php

use yii\db\Migration;

class m250905_072701_fix_phase4_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Fix projects table: change 'status' to 'is_active'
        $projectsTableExists = $this->db->schema->getTableSchema('projects') !== null;
        if ($projectsTableExists) {
            $projectsSchema = $this->db->schema->getTableSchema('projects');
            
            if (isset($projectsSchema->columns['status']) && !isset($projectsSchema->columns['is_active'])) {
                $this->renameColumn('{{%projects}}', 'status', 'is_active');
            }
        }

        // Fix customers table: change 'status' to 'is_active'
        $customersTableExists = $this->db->schema->getTableSchema('customers') !== null;
        if ($customersTableExists) {
            $customersSchema = $this->db->schema->getTableSchema('customers');
            
            if (isset($customersSchema->columns['status']) && !isset($customersSchema->columns['is_active'])) {
                $this->renameColumn('{{%customers}}', 'status', 'is_active');
            }
        }

        // Fix quotations table: add missing columns
        $quotationsTableExists = $this->db->schema->getTableSchema('quotations') !== null;
        if ($quotationsTableExists) {
            $quotationsSchema = $this->db->schema->getTableSchema('quotations');
            
            if (!isset($quotationsSchema->columns['expire_date']) && isset($quotationsSchema->columns['valid_until'])) {
                $this->renameColumn('{{%quotations}}', 'valid_until', 'expire_date');
            }
            if (!isset($quotationsSchema->columns['subject'])) {
                $this->addColumn('{{%quotations}}', 'subject', $this->string(255)->after('expire_date'));
            }
            if (!isset($quotationsSchema->columns['notes'])) {
                $this->addColumn('{{%quotations}}', 'notes', $this->text()->after('subject'));
            }
            
            // Remove payment_terms if exists
            if (isset($quotationsSchema->columns['payment_terms'])) {
                $this->dropColumn('{{%quotations}}', 'payment_terms');
            }
        }

        // Fix invoices table: add missing columns
        $invoicesTableExists = $this->db->schema->getTableSchema('invoices') !== null;
        if ($invoicesTableExists) {
            $invoicesSchema = $this->db->schema->getTableSchema('invoices');
            
            if (!isset($invoicesSchema->columns['subject'])) {
                $this->addColumn('{{%invoices}}', 'subject', $this->string(255)->after('due_date'));
            }
            if (!isset($invoicesSchema->columns['notes'])) {
                $this->addColumn('{{%invoices}}', 'notes', $this->text()->after('subject'));
            }
            if (!isset($invoicesSchema->columns['paid_amount'])) {
                $this->addColumn('{{%invoices}}', 'paid_amount', $this->decimal(12,2)->notNull()->defaultValue(0)->after('grand_total'));
            }
            if (!isset($invoicesSchema->columns['balance'])) {
                $this->addColumn('{{%invoices}}', 'balance', $this->decimal(12,2)->notNull()->defaultValue(0)->after('paid_amount'));
            }
            
            // Remove credit_days if exists
            if (isset($invoicesSchema->columns['credit_days'])) {
                $this->dropColumn('{{%invoices}}', 'credit_days');
            }
        }

        // Fix items table
        $itemsTableExists = $this->db->schema->getTableSchema('items') !== null;
        if ($itemsTableExists) {
            $itemsSchema = $this->db->schema->getTableSchema('items');
            
            if (!isset($itemsSchema->columns['description'])) {
                $this->addColumn('{{%items}}', 'description', $this->text()->after('name'));
            }
            if (!isset($itemsSchema->columns['unit_price']) && isset($itemsSchema->columns['base_price'])) {
                $this->renameColumn('{{%items}}', 'base_price', 'unit_price');
            }
            
            // Remove unused columns
            if (isset($itemsSchema->columns['vat_applicable'])) {
                $this->dropColumn('{{%items}}', 'vat_applicable');
            }
            if (isset($itemsSchema->columns['wht_default'])) {
                $this->dropColumn('{{%items}}', 'wht_default');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Reverse the changes
        $projectsTableExists = $this->db->schema->getTableSchema('projects') !== null;
        $customersTableExists = $this->db->schema->getTableSchema('customers') !== null;
        $quotationsTableExists = $this->db->schema->getTableSchema('quotations') !== null;
        $invoicesTableExists = $this->db->schema->getTableSchema('invoices') !== null;
        $itemsTableExists = $this->db->schema->getTableSchema('items') !== null;

        if ($projectsTableExists) {
            $projectsSchema = $this->db->schema->getTableSchema('projects');
            if (isset($projectsSchema->columns['is_active'])) {
                $this->renameColumn('{{%projects}}', 'is_active', 'status');
            }
        }

        if ($customersTableExists) {
            $customersSchema = $this->db->schema->getTableSchema('customers');
            if (isset($customersSchema->columns['is_active'])) {
                $this->renameColumn('{{%customers}}', 'is_active', 'status');
            }
        }

        if ($quotationsTableExists) {
            $quotationsSchema = $this->db->schema->getTableSchema('quotations');
            
            if (isset($quotationsSchema->columns['notes'])) {
                $this->dropColumn('{{%quotations}}', 'notes');
            }
            if (isset($quotationsSchema->columns['subject'])) {
                $this->dropColumn('{{%quotations}}', 'subject');
            }
            if (isset($quotationsSchema->columns['expire_date'])) {
                $this->renameColumn('{{%quotations}}', 'expire_date', 'valid_until');
            }
        }

        if ($invoicesTableExists) {
            $invoicesSchema = $this->db->schema->getTableSchema('invoices');
            
            if (isset($invoicesSchema->columns['balance'])) {
                $this->dropColumn('{{%invoices}}', 'balance');
            }
            if (isset($invoicesSchema->columns['paid_amount'])) {
                $this->dropColumn('{{%invoices}}', 'paid_amount');
            }
            if (isset($invoicesSchema->columns['notes'])) {
                $this->dropColumn('{{%invoices}}', 'notes');
            }
            if (isset($invoicesSchema->columns['subject'])) {
                $this->dropColumn('{{%invoices}}', 'subject');
            }
        }

        if ($itemsTableExists) {
            $itemsSchema = $this->db->schema->getTableSchema('items');
            
            if (isset($itemsSchema->columns['description'])) {
                $this->dropColumn('{{%items}}', 'description');
            }
            if (isset($itemsSchema->columns['unit_price'])) {
                $this->renameColumn('{{%items}}', 'unit_price', 'base_price');
            }
        }

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250905_072701_fix_phase4_tables cannot be reverted.\n";

        return false;
    }
    */
}
