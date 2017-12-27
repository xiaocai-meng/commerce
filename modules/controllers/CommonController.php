<?php
namespace app\modules\controllers;

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
    public function init() {
        /*
        if (Yii::$app->session['admin']['isLogin'] != 1) {
            $this->redirect(['/admin/public/login']);
            Yii::$app->end();
        }
        */
    }
    
    public function beforeAction($action)
    {
        //如果父类此方法返回的是false 则返回false程序就此终止执行
        if (!parent::beforeAction($action))
        {
            return FALSE;
        }
        $controller = $action->controller->id;
        $action = $action->id;
        //验证该用户是否有控制器方法的权限
        if (Yii::$app->admin->can($controller.'/*'))
        {
            return TRUE;
        }
        if (Yii::$app->admin->can($controller.'/'.$action))
        {
            return TRUE;
        }
        throw new \yii\web\UnauthorizedHttpException('没有访问'. $controller. '/'. $action. '的权限');
        //return TRUE;
    }

    //使用过滤器AccessControl控制认证用户登录权限
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                //如果不写默认是user组件
                'user' => 'admin',
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
