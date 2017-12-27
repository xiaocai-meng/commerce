<?php
namespace app\controllers;

use app\controllers\CommonController;
use app\models\Pay;
use Yii;

class PayController extends CommonController
{
    //关闭post表单提交时Yii进行的csrf攻击验证
    public $enableCsrfValidation = FALSE;
    
    //支付宝异步通知
    public function actionNotify() {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (Pay::notify($post)) {
                echo 'success';
                exit;
            }
        }
        echo 'fail';
        exit;
    }

    //支付宝同步通知
    public function actionReturn() {
        $this->layout = 'layout1';
        $trade_status = Yii::$app->request->get('trade_status');
        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
            $mes = 'success';
        } else {
            $mes = 'fail';
        }
        return $this->render('status', ['mes' => $mes]);
    }
}