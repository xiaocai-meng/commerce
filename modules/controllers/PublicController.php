<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\modules\models\Admin;
use Yii;

class PublicController extends Controller
{
    public function actionLogin() {
        //session_start();
        //print_r($_SESSION);
        $this->layout = false;
        $model = new Admin();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            //如果登录成功则跳转
            if ($model->login($post)) {
                $this->redirect(['default/index']);
                Yii::$app->end();
            }
        }
        //检测用户是否登录
//        if (Yii::$app->session['admin']['isLogin']) {
//            $this->redirect(['default/index']);
//            Yii::$app->end();
//        }
        if (!Yii::$app->admin->isGuest)
        {
            $this->redirect(['default/index']);
            Yii::$app->end();
        }
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout() {
        /*
        //删除session
        Yii::$app->session->removeAll();
        //如果session删除成功,则isLogin不存在跳转到登录页面
        if (!isset(Yii::$app->session['admin']['isLogin'])) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        */
        //true清除所有session false只清除对应user用户的相关session
        Yii::$app->admin->logout(FALSE);
        //return $this->goBack(Yii::$app->request->referrer);
        return $this->goBack(\yii\helpers\Url::to(['/admin/public/login']));
    }

    public function actionSeekpassword() {
        $this->layout = false;
        $model = new Admin();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->seekpassword($post)) {
                Yii::$app->session->setFlash('info', '邮件已成功发送请查收');
            } else {
                Yii::$app->session->setFlash('info', '电子邮件发送失败,请稍后再试');
            }
        }
        return $this->render('seekpassword', ['model' => $model]);
    }


}



