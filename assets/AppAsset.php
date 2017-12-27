<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/main.css',
        'css/red.css',
        'css/owl.carousel.css',
        'css/owl.transitions.css',
        'css/animate.min.css',
        'css/font-awesome.min.css',
    ];
    public $js = [
        'js/jquery-migrate-1.2.1.js',
        'js/gmap3.min.js',
        'js/bootstrap-hover-dropdown.min.js',
        'js/owl.carousel.min.js',
        'js/css_browser_selector.min.js',
        'js/echo.min.js',
        'js/jquery.easing-1.3.min.js',
        'js/bootstrap-slider.min.js',
        'js/jquery.raty.min.js',
        'js/jquery.prettyPhoto.min.js',
        'js/jquery.customSelect.min.js',
        'js/wow.min.js',
        'js/scripts.js',
        ['js/html5shiv.js', 'condition' => 'lte IE9', 'position' => \yii\web\View::POS_HEAD],
        ['js/respond.min.js', 'condition' => 'lte IE9', 'position' => \yii\web\View::POS_HEAD],
    ];
    //依赖的CSS和JS文件
    public $depends = [
        //主要包含yii.js 文件
        'yii\web\YiiAsset',
        //包含从jQuery bower 包的jquery.js文件
        'yii\web\JqueryAsset',
        //包含从Twitter Bootstrap 框架的CSS文件
        'yii\bootstrap\BootstrapAsset',
        //包含从Twitter Bootstrap 框架的JavaScript 文件 来支持Bootstrap JavaScript插件
        'yii\bootstrap\BootstrapPluginAsset',

    ];
}
