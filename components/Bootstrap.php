<?php
namespace app\components;

use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Here you can refer to Application object through $app variable
        $app->params['robot_email'] = 'noreply@slypee.com';

        // Global variable for type of connetion
        // Use in filters for boolean variables
        $dsn = Yii::$app->db->dsn;
        $app->params['connection_type'] = explode(":", $dsn)[0];
    }
}
