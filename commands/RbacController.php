<?php
namespace app\commands;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    //myphp yii rbac/init 参数
    public function actionInit() 
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $dir = dirname(dirname(__FILE__)).'/modules/controllers';
            //controllers下的所有文件绝对路径
            $controllers = glob($dir.'/*');
            //print_r($controllers);
            $permissions = [];
            foreach ($controllers as $controller)
            {
                //取控制器内容
                $content = file_get_contents($controller);
                //print_r($content);
                $res = preg_match('/class ([a-zA-Z]+)Controller/', $content, $match);
                //print_r($match);
                //类名
                $controllerName = $match[1];
                $permissions[] = strtolower($controllerName).'/*';
                //print_r($permissions);
                //匹配方法名称
                preg_match_all('/action([a-zA-Z_]+)/', $content, $matches);
                foreach ($matches[1] as $actionName)
                {
                    $permissions[] = strtolower($controllerName.'/'.$actionName);
                }
            }
            //print_r($permissions);
            $auth = Yii::$app->authManager;
            foreach ($permissions as $permission)
            {
                //对item表进行批量导入
                if (!$auth->getPermission($permission))
                {
                    $per = $auth->createPermission($permission);
                    $per->description = $permission;
                    $auth->add($per);
                }
            }
            $trans->commit();
            echo "import success \n";
        } catch (\Exception $e) {
            //如果进行了sql操作有活动的事务则回滚到begin以前的状态
            if (Yii::$app->db->getTransaction())
            {
                $trans->rollBack();
            }
            echo "import failed \n";
        }
    }
}