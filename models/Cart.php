<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;
class Cart extends ActiveRecord 
{
    public static function tableName()
    {
        return "{{%cart}}";
    }
    
    public function rules() {
        return [
            [['productid', 'productnum', 'userid', 'price'], 'required', 'message' => '参数不能为空'],
            ['id', 'integer', 'message' => 'id必须是整型']

        ];
    }

    //自动添加更新时间和创建时间(数据库中更新和创建时间必须为int型)
//    public function behaviors()
//    {
//        return [
//            [
//                'class' => TimestampBehavior::className(),
//                'createdAtAttribute' => 'createtime',
//                'updatedAtAttribute' => 'updatetime',
//                'attributes' => [
//                    //在insert之前更新当前创建时间和更新时间
//                    ActiveRecord::EVENT_BEFORE_INSERT => ['createtime', 'updatetime'],
//                    //在update之前更新更新时间
//                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatetime'],
//                ]
//            ]
//        ];
//    }
}
