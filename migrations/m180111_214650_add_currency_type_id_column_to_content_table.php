<?php

use yii\db\Migration;

/**
 * Handles adding currency_type_id to table `content`.
 */
class m180111_214650_add_currency_type_id_column_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'currency_type_id', $this->integer()->notNull());

        // creates index for column `user_id`
        $this->createIndex(
            'idx-content-currency_type_id',
            'content',
            'currency_type_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-content-curency_type_id',
            'content',
            'currency_type_id',
            'currency_types',
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
            'fk-content-curency_type_id',
            'content'
        );

        $this->dropIndex(
            'idx-content-currency_type_id',
            'content'
        );

        $this->dropColumn('content', 'currency_type_id');
    }
}
