<?php

use yii\db\Migration;

/**
 * Handles the creation of table `slider_log`.
 */
class m180118_073923_create_slider_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('slider_log', [
            'id' => $this->primaryKey(),
            'datetime' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'crud_type_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-slider_log-user_id',
            'slider_log',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-slider_log-user_id',
            'slider_log',
            'user_id',
            'user',
            'id',
            'RESTRICT'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-slider_log-slide_id',
            'slider_log',
            'slide_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-slider_log-slide_id',
            'slider_log',
            'slide_id',
            'slider',
            'id',
            'RESTRICT'
        );

        // creates index for column `crud_type_id`
        $this->createIndex(
            'idx-slider_log-crud_type_id',
            'slider_log',
            'crud_type_id'
        );

        // add foreign key for table `crud_types`
        $this->addForeignKey(
            'fk-slider_log-crud_type_id',
            'slider_log',
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
            'fk-slider_log-user_id',
            'slider_log'
        );

        $this->dropIndex(
            'idx-slider_log-user_id',
            'slider_log'
        );

        $this->dropForeignKey(
            'fk-slider_log-slide_id',
            'slider_log'
        );

        $this->dropIndex(
            'idx-slider_log-slide_id',
            'slider_log'
        );

        $this->dropForeignKey(
            'fk-slider_log-crud_type_id',
            'category_log'
        );

        $this->dropIndex(
            'idx-slider_log-crud_type_id',
            'slider_log'
        );

        $this->dropTable('slider_log');
    }
}
