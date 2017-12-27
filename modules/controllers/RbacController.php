<?php
namespace app\modules\controllers;
use app\modules\models\Admin;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\modules\models\Rbac;

class RbacController extends CommonController
{
    public $layout = 'layout1';
    public function actionCreaterole()
    {
        if (Yii::$app->request->isPost) {
            //Dbmanager的一个对象
            $auth = Yii::$app->authManager;
            //表shop_auth_item的实例对象
            $role = $auth->createRole(null);
            $post = Yii::$app->request->post();
            if (empty($post['name']) || empty($post['description'])) {
                throw new \Exception('参数错误');
            }
            $role->name = $post['name'];
            $role->description = $post['description'];
            $role->ruleName = !empty($post['rule_name']) ? $post['rule_name'] : NULL;
            $role->data = !empty($post['data']) ? $post['data'] : NULL;
            //更新数据库
            if ($auth->add($role)) {
                Yii::$app->session->setFlash('info', '添加成功');
            } else {
                Yii::$app->session->setFlash('info', '添加失败');
            }
        }
        return $this->render('_createitem');
    }

    public function actionRoles()
    {
        $auth = Yii::$app->authManager;
        $data = new ActiveDataProvider([
            'query' => (new Query())->from($auth->itemTable)->where('type = 1')->orderBy('created_at desc'),
            'pagination' => ['pageSize' => 2],
        ]);
        return $this->render('_items', ['dataProvider' => $data]);
    }

    public function actionAssignitem()
    {
        $name = htmlspecialchars(Yii::$app->request->get('name'));
        $auth = Yii::$app->authManager;
        $parent = $auth->getRole($name);
        //type = 1 给角色分配角色
        $roles = Rbac::getOption($auth->getRoles(), $parent);
        //type = 2 给角色分配权限
        $permissions = Rbac::getOption($auth->getPermissions(), $parent);
        //print_r($roles);exit;
        if (Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            //print_r($post);exit;
            if (Rbac::addChild($post['children'], $name))
            {
                Yii::$app->session->setFlash('info', '分配成功');
            } else {
                Yii::$app->session->setFlash('info', '分配失败');
            }
        }
        $children = Rbac::getChildren($name);
        return $this->render('_assignitem', ['parent' => $name, 'roles' => $roles, 'permissions' => $permissions, 'children' => $children]);
    }

    public function actionCreaterule()
    {
        if (Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            if (empty($post['class_name']))
            {
                throw new \Exception('class_name为空');
            }
            // \\ 第一个为转义字符
            $className = "app\\models\\". $post['class_name'];
            if (!class_exists($className))
            {
                throw new \Exception('该类不存在');
            }
            $rule = new $className;
            //对shop_auth_rule表操作
            if (Yii::$app->authManager->add($rule))
            {
                Yii::$app->session->setFlash('info', '添加成功');
            } else {
                Yii::$app->session->setFlash('info', '添加失败');
            }
        }
        return $this->render('_createrule');
    }
}