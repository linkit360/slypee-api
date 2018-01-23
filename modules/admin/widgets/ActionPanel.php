<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

class ActionPanel extends Widget {
    public $activate;
    public $deactivate;
    public $edit;

    public function init(){
        parent::init();
    }

    public function run(){
        return $this->render("action_panel",[
            "activate" => $this->activate,
            "deactivate" => $this->deactivate,
            "edit" => $this->edit
        ]);
    }
}
?>