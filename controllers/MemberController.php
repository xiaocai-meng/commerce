<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\User;
use Yii;

class MemberController extends CommonController {

    protected $except = ['auth', 'reg', 'qqlogin', 'qqcallback', 'qqreg', 'logout'];

    public $layout = 'layout2' ;

    public function actionAuth() {
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            //如果登录成功则跳转
            if ($model->login($post)) {
                $this->redirect(['index/index']);
                Yii::$app->end();
            }
        } 
        //检测用户是否登录
//        if (Yii::$app->session['isLogin']) {
//            $this->redirect(['index/index']);
//            Yii::$app->end();
//        }
        if (!Yii::$app->user->isGuest) {
            $this->redirect(['index/index']);
            Yii::$app->end();
        }
        return $this->render('auth', ['model' => $model]);
    }

    public function actionReg() {
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->reg($post)) {
                Yii::$app->session->setFlash('info', '电子邮件发送成功');
            } else {
                Yii::$app->session->setFlash('info', '电子邮件发送失败');
            }
        }
        $model->userpassword = '';
        return $this->render('auth', ['model' => $model]);
    }

    public function actionQqlogin() {
        require_once("../vendor/qqlogin/qqConnectAPI.php");
        $qq = new \QC();
        $qq->qq_login();
    }

    public function actionQqcallback() {
        require_once("../vendor/qqlogin/qqConnectAPI.php");
        $Oauth = new \OAuth();
        $accessToken = $Oauth->qq_callback();
        //QQ用户唯一标识
        $openid = $Oauth->get_openid();
        $qq = new \QC($accessToken, $openid);
        //获取qq登录人的信息
        $userinfo = $qq->get_user_info();
        //var_dump($userinfo);
        $session = Yii::$app->session;
        $session['userinfo'] = $userinfo;
        $session['openid'] = $openid;
        //如果该用户已经绑定了openid直接跳转到初始界面
        if ($user = User::find()->where("openid = :openid", [':openid' => $openid])->one()) {
            //QQ用户的名称
            $session['loginname'] = $userinfo['nickname'];
            $session['isLogin'] = 1;
            $session['userid'] = $user->userid;
            $this->redirect(['index/index']);
            Yii::$app->end();
        }
        $this->redirect(['member/qqreg']);
        Yii::$app->end();
    }
    

    public function actionQqreg() {
        $this->layout = 'layout1';
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $post['User']['openid'] = $session['openid'];
            if ($model->addUser($post, 'qqreg')) {
                $session['loginname'] = $session['userinfo']['nickname'];
                $session['isLogin'] = 1;
                $session['userid'] = $model->getuserid($post['User']['username']);
                $this->redirect(['index/index']);
                Yii::$app->end();
            }
        }
        return $this->render('qqreg', ['model' => $model]);
    }

    public function actionLogout() {
        //删除session   Yii::$app->session->remove('loginname');  Yii::$app->session->remove('isLogin'); = removeAll()
        //Yii::$app->session->removeAll();
        //如果session删除成功,则isLogin不存在跳转到首页index/index
        //if (!isset(Yii::$app->session['isLogin'])) {
            //goback() 跳到index.php，如果传入参数则会跳转参数指定地址 Yii::$app->request->referrer = $_SERVER['HTTP_REFERER']
            //return $this->goBack(Yii::$app->request->referrer);
        //}
        //$this->goBack();
        //true清除所有session false只清除对应user用户的相关session
        Yii::$app->user->logout(FALSE);
        return $this->goBack(Yii::$app->request->referrer);
    }


}
