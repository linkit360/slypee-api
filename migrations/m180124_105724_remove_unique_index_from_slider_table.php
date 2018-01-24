<?php

use yii\db\Migration;

/**
 * Class m180124_105724_remove_unique_index_from_slider_table
 */
class m180124_105724_remove_unique_index_from_slider_table extends Migration
{
    public function up()
    {
        // TODO разобратся почему и как в миграциях из за типа БД разницу убрать!
        // remove the unique index
        //$this->dropIndex('priority', 'slider');
        //$this->dropIndex('slider_priority_key', 'slider');
    }

    public function down()
    {
        // TODO разобратся почему и как в миграциях из за типа БД разницу убрать!
        // add the unique index again
        //$this->createIndex('priority', 'slider', 'priority', $unique = true );
        //$this->createIndex('slider_priority_key', 'slider', 'priority', $unique = true );
    }
}
