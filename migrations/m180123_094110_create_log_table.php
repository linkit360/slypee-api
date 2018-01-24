<?php

use yii\db\Migration;

/**
 * Handles the creation of table `log`.
 */
class m180123_094110_create_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('log', [
            'id' => $this->primaryKey(),
            'datetime' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'crud_type_id' => $this->integer()->notNull(),
            // TODO что делать с миграцией в MYSQL и postgres
            // CREATE TYPE content_type as ENUM('category', 'content', 'user', 'customer', 'slider' - тип для postgres
            // 'object_type' => "ENUM('category', 'content', 'user', 'customer', 'slider')",
            'object_type' => "content_type"
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-log-user_id',
            'log',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-log-user_id',
            'log',
            'user_id',
            'user',
            'id',
            'RESTRICT'
        );

        // creates index for column `crud_type_id`
        $this->createIndex(
            'idx-log-crud_type_id',
            'log',
            'crud_type_id'
        );

        // add foreign key for table `crud_types`
        $this->addForeignKey(
            'fk-log-crud_type_id',
            'log',
            'crud_type_id',
            'crud_types',
            'id',
            'RESTRICT'
        );

        // creates index for column `object_id`
        $this->createIndex(
            'idx-log-object_id',
            'log',
            'object_id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-log-user_id',
            'log'
        );

        $this->dropIndex(
            'idx-log-user_id',
            'log'
        );

        $this->dropForeignKey(
            'fk-log-crud_type_id',
            'category_log'
        );

        $this->dropIndex(
            'idx-log-crud_type_id',
            'log'
        );

        $this->dropIndex(
            'idx-log-object_id',
            'log'
        );

        $this->dropTable('log');
    }
}
