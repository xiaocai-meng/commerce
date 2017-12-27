<?php
namespace app\controllers;

use app\controllers\CommonController;
use app\models\Product;
use yii\web\Controller;
use Yii;

class IndexController extends CommonController
{
    protected $except = ['index'];
    public function actionIndex(){
        //Yii::$app->kafka->send(['1122']);
        //Yii::warning('222');
        //Yii::trace('111111');
        //Yii::info('111111', 'myinfo');
        //Yii::beginProfile()
        //********可以测试此间代码的性能
        //Yii::endProfile()
        //print_r($_SESSION);
        //指定模板文件为views\layouts\layout1.php  如果值为false则为关闭模板布局(去掉yii2模板默认的头部和脚部layouts\main.php) = $this->renderPartial("index")
        $this->layout = 'layout1';
//        $data = [];
//        $data['tui'] = Product::find()->where('istuijian = "1" and ison = "1"')->orderBy('createtime desc')->limit(4)->all();
//        $data['new'] = Product::find()->where('ison = "1"')->orderBy('createtime desc')->limit(4)->all();
//        $data['hot'] = Product::find()->where('ison = "1" and ishot = "1"')->orderBy('createtime desc')->limit(4)->all();
//        $data['all'] = Product::find()->where('ison = "1"')->orderBy('createtime desc')->limit(7)->all();
        //print_r($data['all']);return;
        return $this->render("index");
    }
}


