<?php
namespace app\controllers;
use app\models\User;
use app\models\Cart;
use app\models\Product;
use app\models\Category;
use yii\web\Controller;
use Yii;
class CommonController extends Controller
{
    //全部方法如果未登录都没有权限访问,如果想未登录就有权限访问则在except里面添加方法名
    protected $actions = ['*'];
    protected $except = [];
    protected $mustlogin = [];
    protected $verbs = [];

    //初始化时自动调用
    public function init()
    {
        //菜单缓存redis
        $cache = Yii::$app->cache;
        $key = 'menu';
        if (empty($menu = $cache->get($key))) {
            $menu = Category::getMenu();
            //数据2个小时清空同步一次保证数据的有效性
            $cache->set($key, $menu, 3600 * 2);
        }
        $this->view->params['menu'] = $menu;

        //购物车缓存
        $data = [];
        if (!Yii::$app->user->isGuest)
        {
            $key = 'cart';
            if (empty($data = $cache->get($key)))
            {
                $data['products'] = [];
                $total = 0;
                //验证是否登录
                //if (Yii::$app->session['isLogin']) {
                //有问题
                //$usermodel = User::find()->where('username = :name', [':name' => Yii::$app->session['loginname']])->one();
                //if (!empty($usermodel) && !empty($usermodel->id)) {
                //$userid = $usermodel->id;
                $userid = Yii::$app->user->id;
                $carts = Cart::find()->where('userid = :userid', [':userid' => $userid])->asArray()->all();
                foreach ($carts as $k => $cart) {
                    $product = Product::find()->where('id = :id', [':id' => $cart['productid']])->one();
                    $data['products'][$k]['cover'] = $product->cover;
                    $data['products'][$k]['title'] = $product->title;
                    //此件物品买了几件
                    $data['products'][$k]['productnum'] = $cart['productnum'];
                    //实际购买的价钱
                    $data['products'][$k]['price'] = $cart['price'];
                    $data['products'][$k]['productid'] = $cart['productid'];
                    $data['products'][$k]['cartid'] = $cart['id'];
                    $total += $data['products'][$k]['price'] * $data['products'][$k]['productnum'];
                }
                $data['total'] = $total;
                //缓存依赖
                $dep = new \yii\caching\DbDependency([
                    'sql' => "select max(updatetime) from {{%cart}} where userid = :id",
                    'params' => [':id' => Yii::$app->user->id],
                ]);
                //print_r($dep);
                $cache->set($key, $data, 600, $dep);
            }
        }
        $this->view->params['cart'] = $data;

        //缓存依赖
        $dep = new \yii\caching\DbDependency([
            'sql' => "select max(updatetime) from {{%product}} where ison = '1'",
        ]);
        //对商品做查询缓存
        $tui = Product::getDb()->cache(function(){
            return Product::find()->where('istuijian = "1" and ison = "1"')->orderBy('createtime desc')->limit(4)->all();
        }, 600, $dep);
        $new = Product::getDb()->cache(function(){
            return Product::find()->where('ison = "1"')->orderBy('createtime desc')->limit(4)->all();
        }, 600, $dep);
        $hot = Product::getDb()->cache(function(){
            return Product::find()->where('ison = "1" and ishot = "1"')->orderBy('createtime desc')->limit(4)->all();
        }, 600, $dep);
//        $sale = Product::getDb()->cache(function(){
//            return Product::find()->where('ison = "1" and issale = "1"')->orderBy('createtime desc')->limit(3)->all();
//        }, 60, $dep);
        $all = Product::getDb()->cache(function(){
            return Product::find()->where('ison = "1"')->orderBy('createtime desc')->limit(7)->all();
        }, 600, $dep);
        $this->view->params['tui'] = (array)$tui;
        $this->view->params['new'] = (array)$new;
        $this->view->params['hot'] = (array)$hot;
        $this->view->params['all'] = (array)$all;
        //print_r($all);
    }

    public function isLogin() {
        /*
        if (Yii::$app->session['isLogin'] != 1) {
            $this->redirect(['member/auth']);
            Yii::$app->end();
        }
        */
        if (Yii::$app->user->isGuest) {
            $this->redirect(['member/auth']);
            Yii::$app->end();
        }
    }

    //使用过滤器AccessControl控制认证用户登录权限
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => $this->actions,
                'except' => $this->except,
                'rules' => [
                    [
                        'allow' => false,
                        'actions' => empty($this->mustlogin) ? [] : $this->mustlogin,
                        //guest
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => empty($this->mustlogin) ? [] : $this->mustlogin,
                        //not guest 登录以后的
                        'roles' => ['@']
                    ]
                ]
            ],
            //添加访问方式的过滤
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => $this->verbs,
            ]
        ];
    }

}
