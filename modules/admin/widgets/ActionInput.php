<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

class ActionInput extends Widget {
    public $item;
    public $property;

    public function init(){
        parent::init();
    }

    public function run(){
        return $this->render("action_input",[
            'item' => $this->item,
            'property' => $this->property
        ]);
    }
}
?>