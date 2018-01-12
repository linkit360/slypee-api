<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content`.
 */
class m180111_094637_create_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('content', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'name' => $this->string(128)->notNull(),
            'logo' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'rating' => $this->float()->notNull(),
            'price' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-content-category_id',
            'content',
            'category_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-content-category_id',
            'content',
            'category_id',
            'category',
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
            'fk-content-category_id',
            'content'
        );

        $this->dropIndex(
            'idx-content-category_id',
            'content'
        );

        $this->dropTable('content');
    }
}
