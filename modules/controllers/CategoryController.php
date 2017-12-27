<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\models\Category;
use yii\web\Response;
use Yii;
class CategoryController extends CommonController
{
    public function actionList() {
        $this->layout = 'layout1';
        $model = new Category();
        //$list = $model->getTreeList();
        $data = $model->getJsTreeData();
        //print_r($data['page']);
        //当前页
        $page = Yii::$app->request->get('page');
        //页面size
        $perpage = Yii::$app->request->get('per-page');
        return $this->render('cates', ['pager' => $data['page'], 'page' => $page, 'perpage' => $perpage]);
    }

    public function actionAdd() {
        $model = new Category;
        $this->layout = 'layout1';
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if ($model->add($data)) {
                Yii::$app->session->setFlash('info', '添加成功');
            } else {
                Yii::$app->session->setFlash('info', '添加失败');
            }
        }
        $list = $model->getOptions();
        return $this->render('add', ['list' => $list, 'model' => $model]);
    }

    public function actionMod() {
        $this->layout = "layout1";
        $id = Yii::$app->request->get("id");
        $model = Category::find()->where('id = :id', [':id' => $id])->one();
        $list = $model->getOptions();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->load($post) && $model->save()) {
                Yii::$app->session->setFlash('info', '修改成功');
            } else {
                Yii::$app->session->setFlash('info', '修改失败');
            }
        }
        return $this->render('mod', ['list' => $list, 'model' => $model]);

    }

    public function actionDel() {
       try {
           $id = Yii::$app->request->get("id");
           if (empty($id)) {
               throw new \Exception("参数错误");
           }
           $data = Category::find()->where("parentid = :id", [':id' => $id])->one();
           if ($data) {
               throw new \Exception("该分类下有子类,请先删除全部子类");
           }
           if (Category::deleteAll("id = :id", [':id' => $id])) {
               Yii::$app->session->setFlash('info', '删除成功');
           } else {
               throw new \Exception("删除失败");
           }
       } catch(\Exception $e) {
           Yii::$app->session->setFlash('info', $e->getMessage());
       }
        $this->redirect(['category/list']);
        Yii::$app->end();
    }

    public function actionTree() {
        //返回json格式的数据
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Category();
        $data = $model->getJsTreeData();
        return $data['data'];

    }

    public function actionRename() {
        //返回json格式的数据
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isPost) {
            throw new \Exception("is not post");
        }
        $post = Yii::$app->request->post();
        $newtext = $post['newtext'];
        $old = $post['old'];
        $id = $post['id'];
        if (empty($newtext) || empty($id)) {
            return ['code' => -1, 'message' => '参数错误', 'data' => []];
        }
        if ($newtext == $old) {
            return ['code' => 1, 'message' => '和原来数据一样', 'data' => []];
        }
        $model = Category::findOne($id);
        $model->scenario = 'rename';
        $model->title = $newtext;
        if ($model->save()) {
            return ['code' => 1, 'message' => 'Ok', 'data' => []];
        }
        return ['code' => -2, 'message' => '保存失败', 'data' => []];
    }

    public function actionDelete() {
        //返回json格式的数据
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isGet) {
            throw new \Exception("is not get");
        }
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return ['code' => -1, 'message' => 'id为空'];
        }
        $total = Category::find()->where('parentid = :pid', [':pid' => $id])->count();
        if ($total > 0) {
            return ['code' => -2, 'message' => '该分类下有子类, 请先删除所有子类后在删除该分类'];
        }
        if (Category::findOne($id)->delete()) {
            return ['code' => 1, 'message' => '删除成功'];
        }
        return ['code' => -3, 'message' => '删除失败'];
    }



}