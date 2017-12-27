<?php
namespace app\modules\controllers;

use yii\web\Controller;
use app\models\Order;
use yii\data\Pagination;
use Yii;

class OrderController extends CommonController
{
    public function actionList() {
        $this->layout = 'layout1';
        $model = Order::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['order'];
        $page = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $data = $model->offset($page->offset)->limit($page->limit)->all();
        $data = Order::getDetail($data);
        return $this->render('list', ['page' => $page, 'data' => $data]);
    }
    
    public function actionDetail() {
        $this->layout = 'layout1';
        $orderid = (int)Yii::$app->request->get('orderid');
        $order = Order::find()->where('orderid = :orderid', [':orderid' => $orderid])->one();
        $data = Order::getData($order);
        return $this->render('detail', ['order' => $data]);
    }

    public function actionSend() {
        $this->layout = 'layout1';
        $orderid = (int)Yii::$app->request->get('orderid');
        $order = Order::find()->where('orderid = :orderid', [':orderid' => $orderid])->one();
        $order->scenario = 'send';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $order->status = Order::SENDED;
            if ($order->load($post) && $order->save()) {
                Yii::$app->session->setFlash('info', '发货成功');
            } else {
                Yii::$app->session->setFlash('info', '发货失败');
            }
        }
        return $this->render('send', ['model' => $order]);
    }
}
