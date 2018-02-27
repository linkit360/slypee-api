<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class SetPerPageController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->get();

        $types = ["content", "category"];
        $session = Yii::$app->session;
        // первый вариант
        if(isset($get["type"]) && isset($get["value"])) {
            if (in_array($get["type"], $types)) {
                $session->set($get["type"] . '_per_page_settings', intval($get["value"]));

                return json_encode(["success" => 1],JSON_PRETTY_PRINT);
            }
        }
    }
}
