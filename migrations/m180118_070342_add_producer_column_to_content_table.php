<?php

use yii\db\Migration;

/**
 * Handles adding producer to table `content`.
 */
class m180118_070342_add_producer_column_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'producer', $this->string(50));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('content', 'producer');
    }
}
