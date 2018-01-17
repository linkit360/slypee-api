<?php

use yii\db\Migration;

/**
 * Handles the creation of table `slider`.
 */
class m180117_063057_create_slider_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('slider', [
            'id' => $this->primaryKey(),
            'title' => $this->string(50)->notNull(),
            'subtitle' => $this->string(50)->notNull(),
            'description' => $this->text(),
            'link' => $this->string(128)->notNull(),
            'image' => $this->string(255)->notNull(),
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
        $this->dropTable('slider');
    }
}
