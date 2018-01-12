<?php

use yii\db\Migration;

/**
 * Handles the creation of table `currency_types`.
 */
class m180111_120834_create_currency_types_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('currency_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('currency_types');
    }
}
