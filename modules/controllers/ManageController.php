<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\modules\models\Admin;
use yii\data\Pagination;
use app\modules\models\Rbac;
use Yii;

class ManageController extends CommonController
{
    public function actionMailchangepassword() {
        $this->layout = false;
        $timestamp = Yii::$app->request->get('timestamp');
        $adminuser = Yii::$app->request->get('adminuser');
        $token = Yii::$app->request->get('token');
        $model = new Admin();
        $model->adminuser = $adminuser;
        $myToken = $model->createToken($adminuser, $timestamp);
        if ($myToken != $token) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        if ((time() - $timestamp) > 600) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $row = $model->adminChangePassword($post);
            if ($row > 0) {
                Yii::$app->session->setFlash('info', '密码修改成功');    
            } elseif ($row === 0) {
                Yii::$app->session->setFlash('info', '请勿使用最近使用过的密码');
            } else {
                Yii::$app->session->setFlash('info', '密码修改失败');
            }
        }
        return $this->render('mailchangepassword', ['model' => $model]);
    }
    
    public function actionUserlist() {
        $this->layout = 'layout1';
        $count = Admin::find()->count();
        $pageSize = Yii::$app->params['pageSize']['manage'];
        $page = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $manages = Admin::find()->offset($page->offset)->limit($page->limit)->all();
        return $this->render('userlist', ['manages' => $manages, 'page' => $page]);
    }
    
    public function actionAddmanage() {
        $this->layout = 'layout1';
        $model =new Admin();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->addManage($post)) {
                Yii::$app->session->setFlash('info', '添加成功');
            } else {
                Yii::$app->session->setFlash('info', '添加失败');
            }
        }
        $model->adminpassword = '';
        $model->confirmPassword = '';
        return $this->render('addmanage', ['model' => $model]);
    }

    public function actionDelete() {
        $adminid = (int)Yii::$app->request->get('adminid');
        if (empty($adminid)) {
            Yii::$app->session->setFlash('info', 'adminid为空');
            $this->redirect(['manage/userlist']);
            Yii::$app->end();
        }
        $model = new Admin();
        //deleteAll返回删除的行数
        if ($model->deleteAll('adminid = :id', ['id' => $adminid])) {
            Yii::$app->session->setFlash('info', '删除成功');
            $this->redirect(['manage/userlist']);
            Yii::$app->end();
        }
    }

    public function actionChangeemail() {
        $this->layout = 'layout1';
        $adminuser = Yii::$app->session['admin']['adminuser'];
        $model = Admin::find()->where('adminuser = :user', [':user' => $adminuser])->one();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $rows = $model->changeEmail($post);
            if ($rows > 0) {
                Yii::$app->session->setFlash('info', '更新成功');
            } elseif ($rows === 0) {
                Yii::$app->session->setFlash('info', '请勿使用最近使用过的邮箱');
            } else {
                Yii::$app->session->setFlash('info', '更新失败');
            }
        }
        $model->adminpassword = '';
        return $this->render('changeemail', ['model' => $model]);
    }
    
    public function actionChangepassword () {
        $this->layout = 'layout1';
        $model = Admin::find()->where('adminuser = :user', [':user' => \Yii::$app->admin->identity->adminuser])->one();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $row = $model->adminChangePassword($post);
            if ($row > 0) {
                Yii::$app->session->setFlash('info', '密码修改成功');
            } elseif ($row === 0) {
                Yii::$app->session->setFlash('info', '请勿使用重复的密码');
            } else {
                Yii::$app->session->setFlash('info', '密码修改失败');
            }
        }
        $model->adminpassword = '';
        $model->confirmPassword = '';
        return $this->render('changepassword', ['model' => $model]);
    }

    public function actionAssign()
    {
        $this->layout = 'layout1';
        $adminid = Yii::$app->request->get('adminid');
        if (empty($adminid))
        {
            throw new \Exception('adminid为空');
        }
        $admin = Admin::findOne($adminid);
        if (empty($admin))
        {
            throw new \yii\web\NotFoundHttpException('无此用户');
        }
        if (Yii::$app->request->isPost)
        {
            $post = Yii::$app->request->post();
            $children = !empty($post['children']) ? $post['children'] : [];
            if (Rbac::grant($adminid, $children))
            {
                Yii::$app->session->setFlash('info', '授权成功');
            } else {
                Yii::$app->session->setFlash('info', '授权失败');
            }
        }
        $auth = Yii::$app->authManager;
        $roles = [];
        foreach ($auth->getRoles() as $obj)
        {
            $roles[$obj->name] = $obj->description;
        }
        $permissions = [];
        foreach ($auth->getPermissions() as $obj)
        {
            $permissions[$obj->name] = $obj->description;
        }
        $data = [];
        $data['roles'] = Rbac::getItemByUser($adminid, 1);
        $data['permissions'] = Rbac::getItemByUser($adminid, 2);
        //var_dump($roles, $permissions);
        //print_r($data);
        return $this->render('_assign', ['roles' => $roles, 'permissions' => $permissions, 'admin' => $admin->adminuser, 'children' => $data]);
    }


}
