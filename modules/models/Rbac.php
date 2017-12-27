<?php
namespace app\modules\models;
use yii\db\ActiveRecord;
use Yii;

class Rbac extends ActiveRecord
{
    public static function getOption($data, $parent)
    {
        $return = [];
        foreach ($data as $obj) 
        {
            if (!empty($parent) && $parent->name != $obj->name && Yii::$app->authManager->canAddChild($parent, $obj))
            {
                $return[$obj->name] = $obj->description; 
            }
        }
        return $return;
    }

    public static function addChild($children, $parent)
    {
        $auth = Yii::$app->authManager;
        $parentobj = $auth->getRole($parent);
        if (empty($parentobj))
        {
            return FALSE;
        }
        $trans = Yii::$app->db->beginTransaction();
        try {
            //删除掉该父节点的所有孩子
            $auth->removeChildren($parentobj);
            foreach ($children as $item)
            {
                $obj = empty($auth->getRole($item)) ? $auth->getPermission($item) : $auth->getRole($item);
                //对shop_auth_item_child添加数据
                $auth->addChild($parentobj,$obj);
            }
            $trans->commit();
            return TRUE;
        } catch (\Exception $e) {
            if (Yii::$app->db->getTransaction()) 
            {
                $trans->rollBack();
            }
            return FALSE;
        }
    }

    public static function getChildren($parent)
    {
        if (empty($parent))
        {
            return NULL;
        }
        $return = [];
        $return['roles'] = [];
        $return['permissions'] = [];
        $auth = Yii::$app->authManager;
        //SELECT `name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at` FROM `shop_auth_item`, `shop_auth_item_child` WHERE (`parent`='user1') AND (`name`=`child`)
        $children = $auth->getChildren($parent);
        //print_r($children);exit;
        if (empty($children))
        {
            return NULL;
        }
        foreach ($children as $obj) 
        {
            if ($obj->type == 1) 
            {
                $return['roles'][] = $obj->name;
            } else {
                $return['permissions'][] = $obj->name;
            }
        }
        return $return;
    }

    public static function grant($adminid,$children)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $auth = Yii::$app->authManager;
            //取消该用户的所有授权
            $auth->revokeAll($adminid);

            foreach ($children as $item)
            {
                $obj = empty($auth->getRole($item)) ? $auth->getPermission($item) :  $auth->getRole($item);
                //对shop_auth_assignment表操作
                $auth->assign($obj, $adminid);
            }
            $trans->commit();
            return TRUE;
        } catch (\Exception $e) {
            if (Yii::$app->db->getTransaction())
            {
                $trans->rollBack();
                return FALSE;
            }
        }

    }

    public static function getItemByUser ($adminid, $type)
    {
        $func = 'getPermissionsByUser';
        if ($type == 1)
        {
            $func = 'getRolesByUser';
        }
        $data = [];
        $auth = Yii::$app->authManager;
        $items = $auth->$func($adminid);
        foreach ($items as $item)
        {
            $data[] = $item->name;
        }
        return $data;
    }
    

}