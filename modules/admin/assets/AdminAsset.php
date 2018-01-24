<?php
namespace app\modules\admin\assets;

use yii\web\AssetBundle;


class AdminAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/admin/web';
    public $css = [
        'css/materialize.min.css',
        'css/slypee.materialize.css',
        'css/admin.css',
        'css/zebra.css'
    ];

    // Path to admin.css file : $sourcePath/css/admin.css
    public $js = [
        'js/admin.js',
        'js/materialize.min.js',
        'js/zebra_datepicker.js',
        'js/jquery.tablednd_0_5.js'
    ];

    public $publishOptions = [
        'forceCopy' => true,
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}
