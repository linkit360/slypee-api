<?php

use yii\db\Migration;

/**
 * Class m171216_202228_add_table_crud_types
 */
class m171216_202228_add_table_crud_types extends Migration
{
    public function up()
    {
        $this->createTable('crud_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('crud_types');
    }
}
