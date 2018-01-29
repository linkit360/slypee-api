<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content_photos`.
 */
class m180129_051119_create_content_photos_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('content_photos', [
            'id' => $this->primaryKey(),
            'content_id' => $this->integer()->notNull(),
            'photo_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-log-content_id',
            'content_photos',
            'content_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-log-content_id',
            'content_photos',
            'content_id',
            'content',
            'id',
            'RESTRICT'
        );

        // creates index for column `user_id`
        $this->createIndex(
            'idx-log-photo_id',
            'content_photos',
            'photo_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-log-photo_id',
            'content_photos',
            'photo_id',
            'photos',
            'id',
            'RESTRICT'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-log-content_id',
            'content_photos'
        );

        $this->dropIndex(
            'idx-log-content_id',
            'content_photos'
        );

        $this->dropForeignKey(
            'fk-log-photo_id',
            'content_photos'
        );

        $this->dropIndex(
            'idx-log-photo_id',
            'content_photos'
        );

        $this->dropTable('content_photos');
    }
}
