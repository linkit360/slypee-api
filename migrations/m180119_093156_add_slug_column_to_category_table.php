<?php

use yii\db\Migration;

/**
 * Handles adding slug to table `category`.
 */
class m180119_093156_add_slug_column_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('category', 'slug', $this->string(50)->notNull());

        $this->createIndex(
            'idx-category-slug',
            'category',
            'slug'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-category-slug',
            'category'
        );

        $this->dropColumn('category', 'slug');
    }
}
