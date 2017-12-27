<?php
namespace app\modules\controllers;
use app\models\Profile;
use yii\base\Exception;
use yii\web\Controller;
use app\models\User;
use yii\data\Pagination;
use Yii;

class UserController extends CommonController 
{

    public function actionUsers() {
        $this->layout = 'layout1';
        //左连接
        $model = User::find()->joinWith('profile');
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['user'];
        $page = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $users = $model->offset($page->offset)->limit($page->limit)->all();
        return $this->render('users', ['users' => $users, 'page' => $page]);
    }
    
    public function actionAdduser() {
        $this->layout = 'layout1';
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->addUser($post)) {
                Yii::$app->session->setFlash('info', '添加成功');
            } else {
                Yii::$app->session->setFlash('info', '添加失败');
            }
        }
        $model->userpassword = '';
        $model->confirmPassword = '';
        return $this->render('adduser', ['model' => $model]);
    }

    public function actionDelete() {
        try {
            $userid = (int)Yii::$app->request->get('userid');
            if (empty($userid)) {
                throw new \Exception();
            }
            //事务处理保证一致性
            $trans = Yii::$app->db->beginTransaction();
            if (Profile::find()->where('userid = :id', [':id' => $userid])->one()) {
                if (empty(Profile::deleteAll('userid = :id', [':id' => $userid]))) {
                    throw new \Exception();
                }
            }
            if (empty(User::deleteAll('userid = :id', [':id' => $userid]))) {
                throw new \Exception();
            }
            $trans->commit();
        } catch(\Exception $e) {
            //如果进行了sql操作有活动的事务则回滚到begin以前的状态
            if (Yii::$app->db->getTransaction()) {
                $trans->rollBack();
            }
            Yii::$app->session->setFlash('info', '删除失败');
        }
        Yii::$app->session->setFlash('info', '删除成功');
        $this->redirect(['user/users']);
        Yii::$app->end();

    }
    
}
