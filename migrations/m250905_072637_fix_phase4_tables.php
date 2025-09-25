<?php

use yii\db\Migration;

class m250905_072637_fix_phase4_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250905_072637_fix_phase4_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250905_072637_fix_phase4_tables cannot be reverted.\n";

        return false;
    }
    */
}
