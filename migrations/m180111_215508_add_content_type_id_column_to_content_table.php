<?php

use yii\db\Migration;

/**
 * Handles adding content_type_id to table `content`.
 */
class m180111_215508_add_content_type_id_column_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'content_type_id', $this->integer()->notNull());

        // creates index for column `user_id`
        $this->createIndex(
            'idx-content-content_type_id',
            'content',
            'content_type_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-content-content_type_id',
            'content',
            'content_type_id',
            'content_types',
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
            'fk-content-content_type_id',
            'content'
        );

        $this->dropIndex(
            'idx-content-content_type_id',
            'content'
        );

        $this->dropColumn('content', 'content_type_id');
    }
}
