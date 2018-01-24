<?php

use yii\db\Migration;

/**
 * Class m180124_105810_add_priority__index_to_category_table
 */
class m180124_105810_add_priority__index_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createIndex(
            'idx-category-priority',
            'category',
            'priority'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-category-priority',
            'category'
        );
    }
}
