<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content_types`.
 */
class m180111_120759_create_content_types_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('content_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('content_types');
    }
}
