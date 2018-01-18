<?php

use yii\db\Migration;

/**
 * Handles the creation of table `customers_content`.
 */
class m180118_061120_create_customers_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('customers_content', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'content_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'currency_id' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
            'date' => $this->integer()->notNull(),
            'token' => $this->string()->unique(),
            'status' => $this->boolean()
        ]);

        $this->createIndex(
            'idx-customers_content-content_id',
            'customers_content',
            'content_id'
        );

        $this->addForeignKey(
            'fk-customers_content-content_id',
            'customers_content',
            'content_id',
            'content',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-customers_content-customer_id',
            'customers_content',
            'customer_id'
        );

        $this->addForeignKey(
            'fk-customers_content-customer_id',
            'customers_content',
            'customer_id',
            'customers',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-customers_content-type_id',
            'customers_content',
            'type_id'
        );

        $this->addForeignKey(
            'fk-customers_content-type_id',
            'customers_content',
            'type_id',
            'content_types',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-customers_content-currency_id',
            'customers_content',
            'currency_id'
        );

        $this->addForeignKey(
            'fk-customers_content-currency_id',
            'customers_content',
            'currency_id',
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
            'fk-customers_content-content_id',
            'customers_content'
        );

        $this->dropIndex(
            'idx-customers_content-content_id',
            'customers_content'
        );

        $this->dropForeignKey(
            'fk-customers_content-customer_id',
            'customers_content'
        );

        $this->dropIndex(
            'idx-customers_content-customer_id',
            'customers_content'
        );

        $this->dropForeignKey(
            'fk-customers_content-type_id',
            'customers_content'
        );

        $this->dropIndex(
            'idx-customers_content-type_id',
            'customers_content'
        );

        $this->dropForeignKey(
            'fk-customers_content-currency_id',
            'customers_content'
        );

        $this->dropIndex(
            'idx-customers_content-currency_id',
            'customers_content'
        );

        $this->dropTable('customers_content');
    }
}
