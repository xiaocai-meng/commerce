<?php
namespace app\models;

use yii\elasticsearch\ActiveRecord;

class ProductSearch extends ActiveRecord
{
    public function attributes() {
        return ['id', 'title', 'describe'];
    }

    public static function index() {
        return 'shop';
    }

    public static function type()
    {
        return 'products';
    }

}