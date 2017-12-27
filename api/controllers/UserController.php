<?php
namespace app\api\controllers;

use yii\rest\ActiveController;
use app\models\User;
use Yii;
class UserController extends ActiveController
{
    public $enableCsrfValidation = false;
    public $modelClass = 'app\models\User';
    public function actionTest()
    {
        $data = ['a' => 1, 'b' => 2];
        return $data;
    }
}