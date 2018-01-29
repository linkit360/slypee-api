<?php

use yii\db\Migration;

/**
 * Handles adding priority to table `content_photos`.
 */
class m180129_083017_add_priority_column_to_content_photos_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content_photos', 'priority', $this->integer( 10));

        $this->createIndex(
            'idx-content_photos-priority',
            'content_photos',
            'priority'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-content_photos-priority',
            'content_photos'
        );

        $this->dropColumn('content_photos', 'priority');
    }
}
