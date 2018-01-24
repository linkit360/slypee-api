<?php

use yii\db\Migration;

/**
 * Handles adding active to table `customers`.
 */
class m180124_065105_add_active_column_to_customers_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('customers', 'active', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('customers', 'active');
    }
}
