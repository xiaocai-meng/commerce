<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use Yii;

class KafkaController extends Controller
{
    public function actionConsume()
    {
        is_callable(array($this, 'callback'), false, $callable_name);
        Yii::$app->kafka->consumer($this, $callable_name);
    }
    
    public static function callback($message)
    {
        //Yii::$app->log->setFlushInterval(1);
        Yii::info($message, 'testkafka');
    }
}