<?php

use yii\db\Migration;

/**
 * Handles adding priority to table `content`.
 */
class m180124_073153_add_priority_column_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'priority', $this->integer( 10));

        $this->createIndex(
            'idx-content-priority',
            'content',
            'priority'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-content-priority',
            'content'
        );

        $this->dropColumn('content', 'priority');
    }
}
