<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

class AddNewItem extends Widget {
    public $link;
    public $label;

    public function init(){
        parent::init();
    }

    public function run(){
        return $this->render("add_new_item", [
            "link" => $this->link,
            "label" => $this->label
        ]);
    }
}
?>