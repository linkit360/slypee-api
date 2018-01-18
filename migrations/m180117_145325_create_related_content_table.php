<?php

use yii\db\Migration;

/**
 * Handles the creation of table `related_content`.
 */
class m180117_145325_create_related_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('related_content', [
            'id' => $this->primaryKey(),
            'content_id_a' => $this->integer()->notNull(),
            'content_id_b' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-related_content-content_id_a',
            'related_content',
            'content_id_a'
        );

        $this->createIndex(
            'idx-related_content-unique',
            'related_content',
            'content_id_a, content_id_b',
            true
        );

        $this->addForeignKey(
            'fk-related_content-content_id_a',
            'related_content',
            'content_id_a',
            'content',
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
            'fk-related_content-content_id_a',
            'related_content'
        );

        $this->dropIndex(
            'idx-related_content-content_id_a',
            'related_content'
        );

        $this->dropIndex(
            'idx-related_content-unique',
            'related_content'
        );

        $this->dropTable('related_content');
    }
}
