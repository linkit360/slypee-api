<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category_log`.
 */
class m171216_203249_create_category_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('category_log', [
            'id' => $this->primaryKey(),
            'datetime' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'crud_type_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-category_log-user_id',
            'category_log',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-category_log-user_id',
            'category_log',
            'user_id',
            'user',
            'id',
            'RESTRICT'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-category_log-category_id',
            'category_log',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-category_log-category_id',
            'category_log',
            'category_id',
            'category',
            'id',
            'RESTRICT'
        );

        // creates index for column `crud_type_id`
        $this->createIndex(
            'idx-category_log-crud_type_id',
            'category_log',
            'crud_type_id'
        );

        // add foreign key for table `crud_types`
        $this->addForeignKey(
            'fk-category_log-crud_type_id',
            'category_log',
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
            'fk-category_log-user_id',
            'category_log'
        );

        $this->dropIndex(
            'idx-category_log-user_id',
            'category_log'
        );

        $this->dropForeignKey(
            'fk-category_log-category_id',
            'category_log'
        );

        $this->dropIndex(
            'idx-category_log-category_id',
            'category_log'
        );

        $this->dropForeignKey(
            'fk-category_log-crud_type_id',
            'category_log'
        );

        $this->dropIndex(
            'idx-category_log-crud_type_id',
            'category_log'
        );


        $this->dropTable('category_log');
    }
}
