<?php
    //使用gridview来展示前台页面
    use yii\grid\GridView;
    use yii\helpers\Html;
?>
<link rel="stylesheet" href="/assets/admin/css/compiled/user-list.css" type="text/css" media="screen" />
        <!-- main container -->

            <div class="container-fluid">
                <div id="pad-wrapper" class="users-list">
                    <div class="row-fluid header">
                        <h3>角色列表</h3>
                    </div>
                    <?php
                        echo GridView::widget([
                            //从数据库查的该表的数据
                            'dataProvider' => $dataProvider,
                             //列
                            'columns' => [
                                ['class' => '\yii\grid\SerialColumn', 'header' => 'ID',],
                                //列:类型:别名
                                'name:text:名称',
                                'description:text:描述',
                                'rule_name:text:规则名称',
                                'created_at:datetime:创建时间',
                                'updated_at:datetime:更新时间',
                                //操作按钮列
                                [
                                    'class' => '\yii\grid\ActionColumn',
                                    //列明
                                    'header' => '操作',
                                    'template' => '{assign} {update} {delete}',
                                    //按钮
                                    'buttons' => [
                                        'assign' => function ($url, $model, $key) {
                                            return Html::a('分配权限', ['assignitem', 'name' => $model['name']]);
                                        },
                                        'update' => function ($url, $model, $key) {
                                            return Html::a('更新', ['updateitem', 'name' => $model['name']]);
                                        },
                                        'delete' => function ($url, $model, $key) {
                                            return Html::a('删除', ['deleteitem', 'name' => $model['name']]);
                                        }
                                    ]
                                ]
                            ],
                            //整个gridview的布局
                            'layout' => "\n{items}\n{summary}<div class='pagination pull-right'>{pager}<div>",
                        ]);
                    ?>



                </div>
            </div>
        
        <!-- end main container -->