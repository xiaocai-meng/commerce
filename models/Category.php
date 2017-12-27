<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use Yii;
class Category extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%category}}";
    }

    public function behaviors()
    {
        return [
            [
                //当用户更新分类或者创建分类时记录该用户id
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'adminid',
                'updatedByAttribute' => 'updateid',
                'value' => Yii::$app->admin->id,
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'parentid' => '上级分类',
            'title' => '分类名称',
        ];
    }
    
    public function rules()
    {
        return [
            ['parentid', 'required', 'message' => '上级分类不能为空', 'except' => 'rename'],
            ['title', 'required', 'message' => '分类名称不能为空'],
            ['createtime', 'safe'],
        ];
    }

    public function add($data) {
        $data['Category']['createtime'] = time();
        if ($this->load($data) && $this->save()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getData() {
        $cates = self::find()->all(); 
        $cates = \yii\helpers\ArrayHelper::toArray($cates);
        return $cates;
    }

    //无限级分类递归 把相关的类别整合在一起
    public function getTree($cates, $parentid = 0) {
        $tree = [];
        foreach ($cates as $key => $cate) {
            if ($cate['parentid'] == $parentid) {
                $tree [] = $cate;
                //array_merge如果输入的数组中有相同的字符串键名p, ，则该键名后面的值将覆盖前一个值。然而，如果数组包含数字键名，后面的值将不会覆盖原来的值，而是附加到后面
                $tree = array_merge($tree, $this->getTree($cates, $cate['id']));
            }
        }
        return $tree;
    }

    //给分类加|-- 一级一个二级两个以此类推
    public function setPrefix($data, $str = "|--") {
        $tree = [];
        //前缀重复个数
        $num = 1;
        $prefix = [0 => 1];
        while($val = current($data)) {
            $key = key($data);
            if ($key > 0) {
                if ($data[$key-1]['parentid'] != $val['parentid']) {
                    $num ++;
                }
            }
            if (array_key_exists($val['parentid'], $prefix)) {
                $num = $prefix[$val['parentid']];
            }
            $val['title'] = str_repeat($str, $num).$val['title'];
            //录入prefix 在同级分类时候重置num
            $prefix[$val['parentid']] = $num;
            $tree[] = $val;
            next($data);
        }
        return $tree;
    }

    //得到list下拉菜单的选项
    public function getOptions() {
        $data = $this->getData();
        $tree = $this->getTree($data);
        $tree = $this->setPrefix($tree);
        $options = [0 => '添加顶级分类'];
        foreach ($tree as $val) {
            $options[$val['id']] = $val['title'];
        }
        return $options;
    }

    public function getTreeList() {
        $data = $this->getData();
        $tree = $this->getTree($data);
        $tree = $this->setPrefix($tree);
        return $tree;
    }
    
    //生成1,2级菜单
    public static function getMenu() {
        //asArray() 有时候我们需要处理很大量的数据，这时可能需要用一个数组来存储取到的数据， 从而节省内存。你可以用 asArray() 函数做到这一点：
        $top = self::find()->where('parentid = :parentid', [':parentid' => 0])->limit(10)->orderBy('createtime asc')->asArray()->all();
        $data = [];
        foreach($top as $k => $cate) {
            $cate['twochildren'] = self::find()->where('parentid = :parentid', [':parentid' => $cate['id']])->limit(10)->asArray()->all();
            $data[$k] = $cate;
        }
        return $data;
    }

    //组装jstree需要的数据
    public function getJsTreeData() {
        $model = self::find()->where('parentid = 0');
        if (empty($model)) {
            return [];
        }
        //print_r($model->count());
        $page = new \yii\data\Pagination(['totalCount' => $model->count(), 'pageSize' => 1]);
        $data = $model->orderBy('createtime desc')->offset($page->offset)->limit($page->limit)->all();
        if (empty($data)) {
            return [];
        }
        $primay = [];
        foreach ($data as $cate) {
            $primay[] = [
                'id' => $cate->id,
                'text' => $cate->title,
                'children' => $this->getChild($cate->id),
            ];
        }
        return ['data' => $primay, 'page' => $page];
    }

    //递归查询所有子类
    public function getChild($pid) {
        $data = self::find()->where('parentid = :pid', [':pid' => $pid])->all();
        if (empty($data)) {
            return [];
        }
        $children = [];
        foreach ($data as $child) {
            $children[] = [
                'id' => $child->id,
                'text' => $child->title,
                'children' => $this->getChild($child->id),
            ];
        }
        return $children;
    }


}



