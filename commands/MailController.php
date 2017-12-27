<?php

namespace app\commands;

use yii\console\Controller;
use Yii;

class MailController extends Controller 
{
    //myphp yii mail/send
    public function actionSend()
    {   
        Yii::$app->mailer->process();
    }
}