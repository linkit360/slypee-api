<?php
namespace app\modules\api\assets;

use yii\web\AssetBundle;


class ApiAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/api/web';
    public $css = [
        'css/api.css'
    ];

    // Path to admin.css file : $sourcePath/css/admin.css
    public $js = [
    ];

    public $publishOptions = [
        'forceCopy' => true,
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}
