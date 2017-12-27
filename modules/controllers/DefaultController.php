<?php

namespace app\modules\controllers;
use yii\web\Controller;
//use Yii;

class DefaultController extends CommonController
{
    public $layout = 'layout1';

    public function actionIndex()
    {
        //print_r(\Yii::$app->admin->id);
        return $this->render('index');
    }
}
