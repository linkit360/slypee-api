<?php

use yii\db\Migration;

/**
 * Class m180124_105617_remove_unique_index_from_category_table
 */
class m180124_105617_remove_unique_index_from_category_table extends Migration
{
    public function up()
    {
        // TODO разобратся почему и как в миграциях из за типа БД разницу убрать!
        // remove the unique index
        //$this->dropIndex('priority', 'category');
        //$this->dropIndex('category_priority_key', 'category');
    }

    public function down()
    {
        // TODO разобратся почему и как в миграциях из за типа БД разницу убрать!
        // add the unique index again
        //$this->createIndex('priority', 'category', 'priority', $unique = true );
        //$this->createIndex('category_priority_key', 'category', 'priority', $unique = true );
    }
}
