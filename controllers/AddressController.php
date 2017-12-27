<?php
namespace app\controllers;

use app\controllers\CommonController;
use app\models\Address;
use Yii;

class AddressController extends CommonController
{
    public function actionAdd() {
        $this->isLogin();
        $userid = Yii::$app->session['userid'];
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $post['userid'] = $userid;
            $post['address'] = $post['address1'].$post['address2'];
            $data['Address'] = $post;
            $model = new Address;
            $model->load($data);
            $model->save();
        }
        //$_SERVER['HTTP_REFERER'] 引导用户代理到当前页的前一页的地址（如果存在)
        $this->redirect($_SERVER['HTTP_REFERER']);
        Yii::$app->end();
    }

    public function actionDel() {
        $this->isLogin();
        $userid = Yii::$app->session['userid'];
        $addressid = Yii::$app->request->get('addressid');
        if (!Address::find()->where('userid = :userid and addressid = :addressid', [':userid' => $userid, ':addressid' => $addressid])->one()) {
            $this->goBack();
        }
        Address::deleteAll('addressid = :addressid', [':addressid' => $addressid]);
        //goback() 跳到index.php，如果传入参数则会跳转参数指定地址 Yii::$app->request->referrer = $_SERVER['HTTP_REFERER']
        $this->goBack(Yii::$app->request->referrer);
    }

}