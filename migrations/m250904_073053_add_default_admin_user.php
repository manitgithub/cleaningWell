<?php

use yii\db\Migration;

class m250904_073053_add_default_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert default admin user
        $passwordHash = Yii::$app->security->generatePasswordHash('admin123');
        
        $this->insert('{{%users}}', [
            'username' => 'admin',
            'password_hash' => $passwordHash,
            'display_name' => 'ผู้ดูแลระบบ',
            'email' => 'admin@cleaningwell.com',
            'role' => 1, // Admin role
            'status' => 1, // Active
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Insert sample housekeeper user
        $passwordHash2 = Yii::$app->security->generatePasswordHash('housekeeper123');
        
        $this->insert('{{%users}}', [
            'username' => 'housekeeper1',
            'password_hash' => $passwordHash2,
            'display_name' => 'แม่บ้าน 1',
            'email' => 'housekeeper1@cleaningwell.com',
            'phone' => '081-234-5678',
            'role' => 2, // Housekeeper role
            'status' => 1, // Active
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%users}}', ['username' => 'admin']);
        $this->delete('{{%users}}', ['username' => 'housekeeper1']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250904_073053_add_default_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
