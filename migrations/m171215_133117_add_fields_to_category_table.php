<?php

use yii\db\Migration;

/**
 * Class m171215_133117_add_fields_to_category_table
 */
class m171215_133117_add_fields_to_category_table extends Migration
{
    public function up()
    {
        $this->addColumn('category', 'active', $this->integer(1)->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('category', 'active');
        return false;
    }
}
