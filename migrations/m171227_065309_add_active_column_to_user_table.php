<?php

use yii\db\Migration;

/**
 * Handles adding active to table `user`.
 */
class m171227_065309_add_active_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'active', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'active');
    }
}
