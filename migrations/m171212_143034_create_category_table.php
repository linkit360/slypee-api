<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category`.
 */
class m171212_143034_create_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'description' => $this->text(),
            'main_menu' => $this->boolean(),
            'main_page' => $this->boolean(),
            'content' => $this->integer()->notNull(),
            'priority' => $this->integer( 10)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('category');
    }
}
