<?php
namespace app\controllers;
use app\models\Product;
use app\models\Cart;
use yii\web\Controller;
use Yii;

class CartController extends CommonController {
    
    public $layout = 'layout1';
    
    public function actionIndex() {
//        if (Yii::$app->session['isLogin'] != 1) {
//            $this->redirect(['member/auth']);
//            Yii::$app->end();
//        }
        //$userid = Yii::$app->session['userid'];
//        $userid = Yii::$app->user->identity->userid;
//        $cart = Cart::find()->where('userid = :userid', [':userid' => $userid])->asArray()->all();
//        $data = [];
//        foreach ($cart as $k => $pro) {
//            $product = Product::find()->where('id = :id', [':id' => $pro['productid']])->one();
//            $data[$k]['cover'] = $product->cover;
//            $data[$k]['title'] = $product->title;
//            $data[$k]['productnum'] = $pro['productnum'];
//            $data[$k]['price'] = $pro['price'];
//            $data[$k]['productid'] = $pro['productid'];
//            $data[$k]['cartid'] = $pro['id'];
//        }
        return $this->render('index');
    }

    public function actionAdd() {
//        if (Yii::$app->session['isLogin'] != 1) {
//            $this->redirect(['member/auth']);
//            Yii::$app->end();
//        }
        //$userid = Yii::$app->session['userid'];
        
        $userid = Yii::$app->user->identity->userid;

        //通过商品详情页提交
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $num = $post['productnum'];
            $data['Cart'] = $post;
            $data['Cart']['userid'] = $userid;
            $data['Cart']['id'] = "next value for MYCATSEQ_CART";
        }

        if (Yii::$app->request->isGet) {
            $productid = Yii::$app->request->get('productid');
            $model = Product::find()->where('id = :id', [':id' => $productid])->one();
            $price = $model->issale ? $model->saleprice : $model->price;
            $num = 1;
            $data['Cart'] = ['productid' => $productid, 'productnum' => $num, 'price' => $price, 'userid' => $userid, 'id' => "next value for MYCATSEQ_CART"];
        }
        
        //判断购物车中有没有此产品,如果有数量+1    
        if (!$model = Cart::find()->where('productid = :productid and userid = :userid', [':productid' => $data['Cart']['productid'], ':userid' => $data['Cart']['userid']])->one()) {
            $model = new Cart;
        } else {
            $data['Cart']['productnum'] = $model->productnum + $num;
            unset($data['Cart']['id']);
        }

        if ($model->load($data) && $model->save()) {
            $this->redirect(['cart/index']);  
        }
    }

    public function actionMod() {
        $cartid = Yii::$app->request->get('cartid');
        $productnum = Yii::$app->request->get('productnum');
        echo Cart::updateAll(['productnum' => $productnum], 'id = :id', [':id' => $cartid]);
    } 

    public function actionDel() {
        $cartid = Yii::$app->request->get('cartid');
        Cart::deleteAll('id = :id', [':id' => $cartid]);
        $this->redirect(['cart/index']);
        Yii::$app->end();
    }  



}
