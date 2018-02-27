<?php

use yii\db\Migration;

/**
 * Class m180131_203219_add_foreign_key_to_table_related_content
 */
class m180131_203219_add_foreign_key_to_table_related_content extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->addForeignKey(
            'fk-related_content-content_id_b',
            'related_content',
            'content_id_b',
            'content',
            'id',
            'RESTRICT'
        );
    }

    public function down()
    {
        $this->dropForeignKey(
            'fk-related_content-content_id_b',
            'related_content'
        );
    }
}
