<?php

use yii\db\Migration;

/**
 * Class m180131_122526_add_avatar_Field_to_customers_table
 */
class m180131_122526_add_avatar_Field_to_customers_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('customers', 'avatar', $this->string(255));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('customers', 'avatar');
    }
}
