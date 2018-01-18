<?php

use yii\db\Migration;

/**
 * Handles adding video to table `content`.
 */
class m180118_065946_add_video_column_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'video', $this->string(100));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('content', 'video');
    }
}
