<?php

use yii\db\Migration;

/**
 * Handles the creation of table `photos`.
 */
class m180129_050948_create_photos_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('photos', [
            'id' => $this->primaryKey(),
            'image' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('photos');
    }
}
