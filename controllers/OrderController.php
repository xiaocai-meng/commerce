<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Cart;
use app\models\Product;
use app\models\Address;
use app\models\Pay;
use app\models\User;
use dzer\express\Express;
use Yii;

class OrderController extends CommonController{
    
    //protected $mustlogin = ['index'];
    //confirm方法只允许post请求
    protected $verbs = [
        'confirm' => ['post']
    ];

    public function actionIndex() {
        $this->layout = 'layout2';
        //$this->isLogin();
        //$userid = Yii::$app->session['userid'];
        $userid = Yii::$app->user->id;
        $username = User::find()->where('userid = :uid', [':uid' => $userid])->one()->username;
        $orders = Order::getProducts($userid);
        return $this->render('index', ['orders' => $orders, 'username' => $username]);
    }

    //检查订单详情(去结算)
    public function actionCheck() {
        $this->layout = 'layout1';
        if (Yii::$app->session['isLogin'] != 1) {
            $this->redirect(['member/auth']);
            Yii::$app->end();
        }
        $orderid = Yii::$app->request->get('orderid');
        $status = Order::find()->where('orderid = :orderid', [':orderid' => $orderid])->one()->status;
        //如果订单状态不为初始化或者check,那么此订单就支付成功,防止重复支付
        if ($status != Order::CREATEORDER && $status != Order::CHECKORDER) {
            $this->redirect(['order/index']);
            Yii::$app->end();
        }
        $userid = Yii::$app->session['userid'];
        $addresses = Address::find()->where('userid = :userid', [':userid' => $userid])->asArray()->all();
        $details = OrderDetail::find()->where('orderid = :orderid', [':orderid' => $orderid])->asArray()->all();
        $data = [];
        foreach ($details as $detail) {
            $model = Product::find()->where('id = :id', [':id' => $detail['productid']])->one();
            $detail['title'] = $model->title;
            $detail['cover'] = $model->cover;
            $data[] = $detail;
        }
        $express = Yii::$app->params['express'];
        $expressPrice = Yii::$app->params['expressPrice'];
        return $this->render('check', ['express' => $express, 'expressPrice' => $expressPrice, 'addresses' => $addresses, 'products' => $data]);
    }
    
    public function actionAdd() {
        if (Yii::$app->session['isLogin'] != 1) {
            $this->redirect(['member/auth']);
            Yii::$app->end();
        }
        $trans = Yii::$app->db->beginTransaction();
        try {
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                $orderModel = new Order;
                //插入order表
                $orderModel->scenario = 'add';
                $userid = Yii::$app->session['userid'];
                $orderModel->userid = $userid;
                $orderModel->status = Order::CREATEORDER;
                if (!$orderModel->save()) {
                    throw new \Exception();
                }
                //获取最后添加的主键自增id
                $orderid = $orderModel->getPrimaryKey();
                foreach ($post['OrderDetail'] as $product) {
                    $model = new OrderDetail;
                    //插入到orderDetail表中
                    $product['orderid'] = $orderid;
                    $data = [];
                    $data['OrderDetail'] = $product;
                    if (!$model->add($data)) {
                        throw new \Exception();
                    }
                    //删除购物车内容
                    Cart::deleteAll('productid = :productid', [':productid' => $product['productid']]);
                    //把product表的库存数量更新(减去该用户购买的数量)
                    Product::updateAllCounters(['num' => -$product['productnum']], 'id = :id', [':id' => $product['productid']]);
                }
            }
        $trans->commit();
        } catch(\Exception $e) {
            if (Yii::$app->db->getTransaction()) {
                $trans->rollback();
            }
            $this->redirect(['cart/index']);
            Yii::$app->end();
        }
        $this->redirect(['order/check', 'orderid' => $orderid]);
        Yii::$app->end();
    }

    //确认订单
    public function actionConfirm() {
        try {
            $this->isLogin();
            //if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                $userid = Yii::$app->session['userid'];
                $orderid = $post['orderid'];
                $expressid = $post['expressid'];
                $model = Order::find()->where('userid = :userid and orderid = :orderid', [':userid' => $userid, ':orderid' => $orderid])->one();
                if (empty($model)) {
                    throw new \Exception();
                }
                $model->scenario = 'update';
                $post['status'] = Order::CHECKORDER;
                $details = OrderDetail::find()->where('orderid = :orderid', [':orderid' => $orderid])->all();
                $amount = 0;
                foreach ($details as $detail) {
                    $amount += $detail->productnum * $detail->price;
                }
                if ($amount <= 0) {
                    throw new \Exception();
                }
                $express = Yii::$app->params['expressPrice'][$expressid];
                //0为包邮
                if ($express < 0) {
                    throw new \Exception();
                }
                $amount += $express;
                $post['amount'] = $amount;
                $data['Order'] = $post;
                //更新order表
                if ($model->load($data) && $model->save()) {
                    $this->redirect(['order/pay', 'orderid' => $orderid, 'paymethod' => $post['paymethod']]);
                    Yii::$app->end();
                }
            //}
        } catch(\Exception $e) {
            $this->redirect(['index/index']);
            Yii::$app->end();
        }
    }

    public function actionPay() {
        try {
            $this->isLogin();
            $orderid = Yii::$app->request->get('orderid');
            $paymethod = Yii::$app->request->get('paymethod');
            if (empty($orderid) || empty($paymethod)) {
                throw new \Exception();
            }
            if ($paymethod == 'zhifubao') {
                return Pay::alipay($orderid);
            }
        } catch(\Exception $e) {
            $this->redirect(['order/index']);
            Yii::$app->end();
        }
    }
    
    public function actionGetexpress() {
        $expressno = Yii::$app->request->get('expressno');
        //不传递快递公司代码时，会自动判断快递单号所属快递公司，默认返回json.
        $res = Express::search($expressno);
        echo $res;
        exit;
    }
    
    public function actionReceived() {
        $orderid = Yii::$app->request->get('orderid');
        $order = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one();
        if (!empty($order) && $order->status == Order::SENDED) {
            $order->status = Order::RECEIVED;
            $order->save();
        }
        return $this->redirect(['order/index']);
    }

}
