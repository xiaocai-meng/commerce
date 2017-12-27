<?php
namespace app\models;

use yii\db\ActiveRecord;

class OrderDetail extends ActiveRecord 
{
    public static function tableName() {
        return "{{%order_detail}}"; 
    }

    public function rules() {
        return [
            [['productid', 'productnum', 'price', 'orderid'], 'required'],
        ];
    }

    public function add($data) {
        if ($this->load($data) && $this->save()) {
            return TRUE;
        }
        return FALSE;
    }

}
