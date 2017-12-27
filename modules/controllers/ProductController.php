<?php

namespace app\modules\controllers;
use crazyfd\qiniu\Qiniu;
use yii\web\Controller;
use app\models\Category;
use app\models\Product;
use yii\data\Pagination;
use app\models\ProductSearch;
use Yii;

class ProductController extends CommonController
{
    public function actionAdd() {
        $this->layout = 'layout1';
        $cate = new Category();
        $product = new Product();
        $list = $cate->getOptions();
        unset($list[0]);

        if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                $pics = $this->upload();
                //print_r($post);exit;
                if (!$pics) {
                    $product->addError('cover', '封面不能为空');
                } else {
                    $post['Product']['cover'] = $pics['cover'];
                    $post['Product']['pics'] = $pics['pics'];
                    //$post['Product']['describe'] = htmlspecialchars($post['Product']['describe']);
                }
                if ($pics && $product->add($post)) {
                    Yii::$app->session->setFlash('info', '添加成功');
                } else {
                    Yii::$app->session->setFlash('info', '添加失败');
                }

        }
        return $this->render('add', ['model' => $product, 'opts' => $list]);
    }

    public function actionProducts() {
        $this->layout = 'layout1';
        $model = Product::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['product'];
        $page = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $products = $model->offset($page->offset)->limit($page->limit)->all();
        return $this->render('products', ['products' => $products, 'page' => $page]);
    }

    private function upload() {
        //print_r($_FILES);exit;
        //如果cover封面图上传有错,直接跳出循环。封面图必须有
        if ($_FILES['Product']['error']['cover'] > 0) {
            return FALSE;
        }
        $qiniu = new Qiniu(Product::AK, Product::SK, Product::DOMAIN, Product::BUCKET);
        $key = uniqid(microtime(true),true);
        //上传图片
        $qiniu->uploadFile($_FILES['Product']['tmp_name']['cover'], $key);
        //返回七牛云服务器上储存的图片地址
        $cover = $qiniu->getLink($key);
        //商品图片如果没有上传错误则上传(没有选择图片错误为4详情见shop upload类)
        $pics = [];
        foreach($_FILES['Product']['tmp_name']['pics'] as $k => $v) {
            if ($_FILES['Product']['error']['pics'][$k] > 0) {
                continue;
            }
            $key = uniqid(microtime(true),true);
            //上传图片
            $qiniu->uploadFile($v, $key);
            $pics[$k] = $qiniu->getLink($key);
        }
        //print_r($pics);exit;
        return [
            'cover' => $cover,
            'pics' => json_encode($pics),
        ];
    }
    
    public function actionMod() {
        $this->layout = 'layout1';
        $cate = new Category();
        $list = $cate->getOptions();
        unset($list[0]);

        //print_r($_FILES);
        $productid = Yii::$app->request->get('id');
        $model = Product::find()->where("id = :id", [":id" => $productid])->one();
        $model->scenario = 'mod';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $qiniu = new Qiniu(Product::AK, Product::SK, Product::DOMAIN, Product::BUCKET);
            //把原来的图片地址赋值给post防止插入0
            $post['Product']['cover'] = $model->cover;
            //如果封面图上传没有错误 则删除七牛上原来的cover图片并更新新的图片
            if ($_FILES['Product']['error']['cover'] == 0) {
                $key = uniqid(microtime(TRUE), TRUE);
                $qiniu->uploadFile($_FILES['Product']['tmp_name']['cover'], $key);
                $post['Product']['cover'] = $qiniu->getLink($key);
                $qiniu->delete(basename($model->cover));
            }
            //商品图片如果没有上传错误则上传
            $pics = [];
            foreach($_FILES['Product']['tmp_name']['pics'] as $k => $v) {
                if ($_FILES['Product']['error']['pics'][$k] > 0) {
                    continue;
                }
                $key = uniqid(microtime(TRUE), TRUE);
                $qiniu->uploadFile($v, $key);
                $pics[$k] = $qiniu->getLink($key);
            }
            $post['Product']['pics'] = json_encode(array_merge((array)json_decode($model->pics, TRUE), $pics));

            if ($model->load($post) && $model->save()) {
                Yii::$app->session->setFlash('info', '编辑成功');
            } else {
                Yii::$app->session->setFlash('info', '编辑失败');
            }

        }
        return $this->render('mod', ['model' => $model, 'opts' => $list]);
    }
    
    public function actionRemovepic() {
        $key = Yii::$app->request->get('key');
        $id = Yii::$app->request->get('id');
        $model = Product::find()->where('id = :id', [':id' => $id])->one();
        $pics = json_decode($model->pics, TRUE);
        //删除七牛图片并且更新数据库
        $qiniu = new Qiniu(Product::AK, Product::SK, Product::DOMAIN, Product::BUCKET);
        $qiniu->delete(basename($pics[$key]));
        unset($pics[$key]);
        Product::updateAll(['pics' => json_encode($pics)], 'id = :id', [':id' => $id]);
        //重新请求mod页面 刷新数据库数据
        $this->redirect(['product/mod', 'id' => $id]);
        Yii::$app->end();
    }

    public function actionDel() {
        $id = Yii::$app->request->get('id');
        $model = Product::find()->where('id = :id', [':id' => $id])->one();
        $qiniu = new Qiniu(Product::AK, Product::SK, Product::DOMAIN, Product::BUCKET);
        $qiniu->delete(basename($model->cover));
        $pics = json_decode($model->pics, TRUE);
        //print_r($pics);return;
        foreach($pics as $value) {
            $qiniu->delete(basename($value));
        }
        Product::deleteAll('id = :id', [':id' => $id]);
        
        //同时删除ES索引
        $customer = ProductSearch::findOne(intval($id));
        if (!empty($customer)) {
            $customer->delete();
        }

        $this->redirect(['product/products']);
        Yii::$app->end();
    }

    public function actionOn() {
        $id = Yii::$app->request->get('id');
        Product::updateAll(['ison' => 1], 'id = :id', [':id' => $id]);
        $this->redirect(['product/products']);
        Yii::$app->end();
    }

    public function actionOff() {
        $id = Yii::$app->request->get('id');
        Product::updateAll(['ison' => 0], 'id = :id', [':id' => $id]);
        $this->redirect(['product/products']);
        Yii::$app->end();
    }
}
