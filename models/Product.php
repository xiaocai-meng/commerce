<?php
namespace app\models;
use yii\db\ActiveRecord;
use Yii;
class Product extends ActiveRecord
{
    //在七牛个人中心秘钥管理里面有个人的AK和SK
    const AK = '5mDzCfv3tgEbJXIP9FxtzLi4Z5STebuagGfsa0dy';
    const SK = 'spRTobXZfP51GODNbAqqj0W4NvTXUU534w8QP8LI';
    //绑定的域名如果没有申请cdn可以用七牛给的测试域名
    const DOMAIN = 'mengcname.julyangel.cn';
    //所使用储存空间的名称
    const BUCKET = 'xiaocai';
    public $cate;

    public static function tableName() {
        return '{{%product}}';
    }
    
    public function rules() {
        return [
            ['title', 'required', 'message' => '标题不能为空',],
            ['describe', 'required', 'message' => '描述不能为空',],
            ['cateid', 'required', 'message' => '分类不能为空',],
            ['price', 'required', 'message' => '单价不能为空',],
            [['price', 'saleprice'], 'number', 'min' => 0.01, 'message' => '价格必须是数字',],
            ['num', 'integer', 'min' => 0, 'message' => '库存必须是数字',],
            [['issale', 'ishot', 'pics', 'istuijian', 'ison'], 'safe',],
            ['cover', 'required', 'message' => '图片封面不能为空', 'except' => 'mod'],
        ];
    }

    public function attributeLabels() {
        return [
            'cateid' => '分类名称',
            'title'  => '商品名称',
            'describe'  => '商品描述',
            'price'  => '商品价格',
            'ishot'  => '是否热卖',
            'issale' => '是否促销',
            'saleprice' => '促销价格',
            'num'    => '库存',
            'cover'  => '图片封面',
            'pics'   => '商品图片',
            'istuijian' => '是否推荐',
            'ison' => '是否上架',
        ];
    }
    
    public function add($data) {
        if ($this->load($data) && $this->save()) {
            return TRUE;
        }
        return FALSE;
    }

    
}
