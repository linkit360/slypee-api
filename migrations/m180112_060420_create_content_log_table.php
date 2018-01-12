<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content_log`.
 */
class m180112_060420_create_content_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('content_log', [
            'id' => $this->primaryKey(),
            'datetime' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'content_id' => $this->integer()->notNull(),
            'crud_type_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-content_log-user_id',
            'content_log',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-content_log-user_id',
            'content_log',
            'user_id',
            'user',
            'id',
            'RESTRICT'
        );

        // creates index for column `content_id`
        $this->createIndex(
            'idx-content_log-content_id',
            'content_log',
            'content_id'
        );

        // add foreign key for table `content`
        $this->addForeignKey(
            'fk-content_log-content_id',
            'content_log',
            'content_id',
            'content',
            'id',
            'RESTRICT'
        );

        // creates index for column `crud_type_id`
        $this->createIndex(
            'idx-content_log-crud_type_id',
            'content_log',
            'crud_type_id'
        );

        // add foreign key for table `crud_types`
        $this->addForeignKey(
            'fk-content_log-crud_type_id',
            'content_log',
            'crud_type_id',
            'crud_types',
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
            'fk-content_log-user_id',
            'content_log'
        );

        $this->dropIndex(
            'idx-content_log-user_id',
            'content_log'
        );

        $this->dropForeignKey(
            'fk-content_log-content_id',
            'content_log'
        );

        $this->dropIndex(
            'idx-content_log-content_id',
            'content_log'
        );

        $this->dropForeignKey(
            'fk-content_log-crud_type_id',
            'content_log'
        );

        $this->dropIndex(
            'idx-content_log-crud_type_id',
            'content_log'
        );

        $this->dropTable('content_log');
    }
}
