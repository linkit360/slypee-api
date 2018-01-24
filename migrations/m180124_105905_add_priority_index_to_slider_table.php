<?php

use yii\db\Migration;

/**
 * Class m180124_105905_add_priority_index_to_slider_table
 */
class m180124_105905_add_priority_index_to_slider_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createIndex(
            'idx-slider-priority',
            'slider',
            'priority'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-slider-priority',
            'slider'
        );
    }
}
