<?php

namespace app\api\controllers;

use yii\web\Controller;
use Yii;
/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = FALSE;
        print_r(Yii::$app->urlManager->rules);
        return $this->render('index');
    }
}
