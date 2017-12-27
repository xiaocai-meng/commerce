<?php
namespace app\models;
use yii\rbac\Rule;
//use Yii;

class AuthorRule extends Rule
{
    /**
     * @var string shop_auth_rule表中的name字段的值
     */
    public $name = "isAuthor";
    /**
     * 在进行权限验证以后会执行此方法进行规则验证
     * @param int|string $user user id
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        //方法名
        $action = \Yii::$app->controller->action->id;
        //只对category里面的delete方法验证
        if ($action == "delete")
        {
            $cateid = \Yii::$app->request->get('id');
            $category = Category::findOne($cateid);
            return $category->adminid == $user;
        }
        return TRUE;
    }
}