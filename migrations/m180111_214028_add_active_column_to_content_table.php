<?php

use yii\db\Migration;

/**
 * Class m180111_214028_add_active_Column_to_content_table
 */
class m180111_214028_add_active_Column_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'active', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('content', 'active');
    }
}
