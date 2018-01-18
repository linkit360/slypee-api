<?php

use yii\db\Migration;

/**
 * Handles adding active to table `slider`.
 */
class m180118_064459_add_active_column_to_slider_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('slider', 'active', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('slider', 'active');
    }
}
