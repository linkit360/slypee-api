<?php

use yii\db\Migration;

/**
 * Class m171219_094444_add_table_per_page_settings
 */
class m171219_094444_add_table_per_page_settings extends Migration
{
    public function up()
    {
        $this->createTable('per_page_settings', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'value' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('per_page_settings');
    }
}
