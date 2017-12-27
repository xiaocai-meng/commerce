<?php
namespace app\controllers;
use app\models\Product;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\data\Pagination;
use Yii;

class ProductController extends CommonController{

    public $layout = 'layout2';

    public function actionIndex() {
        //$this->layout = false;
        $cateid = Yii::$app->request->get('cateid');
        $model = Product::find()->where('cateid = :cateid and ison = "1"', [':cateid' => $cateid]);

        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['frontproduct'];
        $page = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $all = $model->offset($page->offset)->limit($page->limit)->asArray()->all();

        $tui = $model->where('cateid = :cateid and ison = "1" and istuijian = \'1\'', [':cateid' => $cateid])->orderBy('createtime desc')->limit(5)->asArray()->all();
        $hot = $model->where('cateid = :cateid and ison = "1" and ishot = \'1\'', [':cateid' => $cateid])->orderBy('createtime desc')->limit(5)->asArray()->all();
        $sale = $model->where('cateid = :cateid and ison = "1" and issale = \'1\'', [':cateid' => $cateid])->orderBy('createtime desc')->limit(5)->asArray()->all();

        return $this->render('index', ['sale' => $sale, 'tui' => $tui, 'hot' => $hot, 'all' => $all, 'page' => $page, 'count' => $count]);
    }
    
    public function actionDetail() {
        $productid = Yii::$app->request->get('productid');
        $product = Product::find()->where('id = :id', [':id' => $productid])->asArray()->one();
        $data['all'] = Product::find()->where('ison = "1"')->orderBy('createtime desc')->limit(7)->all();
        return $this->render('detail', ['product' => $product, 'data' => $data]);
    }

    //通过ES搜索
    public function actionSearch() {
        $keyword = Yii::$app->request->get('keyword');
        $query = [
            'multi_match' => [
                'query' => $keyword,
                'fields' => ['title', 'describe']
            ]
        ];
        //new \stdClass() 创建一个空对象与json格式一一对应
        $highlight = [
            'pre_tags' => ['<em>'],
            'post_tags' => ['</em>'],
            'fields' => [
                'title' => new \stdClass(),
                'describe' => new \stdClass()
            ]
        ];
        $searchModel = ProductSearch::find()->query($query);

        $count = $searchModel->count();
        $pageSize = Yii::$app->params['pageSize']['frontproduct'];
        $page = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $res = $searchModel->highlight($highlight)->offset($page->offset)->limit($page->limit)->all();

        //print_r($res);exit;
        $products = [];
        foreach ($res as $value) {
            $product = Product::find()->where('id = :id', [':id' => $value->id])->one();
            $product->title = !empty($value->highlight['title'][0]) ? $value->highlight['title'][0] : $product->title;
            $product->describe = !empty($value->highlight['describe'][0]) ? $value->highlight['describe'][0] : $product->describe;
            $products[] = $product;
        }
        $this->view->params['keyword'] = $keyword;
        //var_dump($products);exit;
        return $this->render('search', ['all' => $products, 'page' => $page, 'count' => $count]);
    }

}
