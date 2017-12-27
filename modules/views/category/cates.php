<?php
$this->params['breadcrumbs'][] = ['label' => '分类管理', 'url' => '/admin/category/list'];
$this->params['breadcrumbs'][] = ['label' => '添加分类', 'url' => '/admin/category/add']
//print_r($this->params['breadcrumbs']);exit;
?>
<link rel="stylesheet" href="/assets/admin/css/compiled/user-list.css" type="text/css" media="screen" />
        <!-- main container -->

            <div class="container-fluid">
                <div id="pad-wrapper" class="users-list">
                    <div class="row-fluid header">
                        <h3>分类列表</h3>
                        <div class="span10 pull-right">
                            <a href="<?php echo yii\helpers\Url::to(['category/add']); ?>" class="btn-flat success pull-right">
                                <span>&#43;</span>添加新分类</a></div>
                    </div>

                    <?php
                         if (Yii::$app->session->hasFlash('info')) {
                         echo Yii::$app->session->getFlash('info');
                         }
                    ?>
                    <!-- Users table -->
                    <div class="row-fluid table">
                        <?php
                           echo \yiidreamteam\jstree\JsTree::widget([
                                'containerOptions' => [
                                    'class' => 'data-tree',
                                ],
                                'jsOptions' => [
                                    'core' => [
                                        'check_callback' => true,
                                        'multiple' => false,
                                        'data' => [
                                            'url' => \yii\helpers\Url::to(['/admin/category/tree', 'page' => $page, 'per-page' => $perpage]),
                                        ],
                                        'themes' => [
                                            'stripes' => true,
                                            'variant' => 'large',
                                        ]
                                    ],
                                    'plugins' => [
                                        'contextmenu', 'dnd', 'search', 'state', 'types', 'wholerow'
                                    ],
                                ],
                            ]);
                        ?>
<!--                        <table class="table table-hover">-->
<!--                            <thead>-->
<!--                                <tr>-->
<!--                                    <th class="span3 sortable">-->
<!--                                        <span class="line"></span>分类ID</th>-->
<!--                                    <th class="span3 sortable">-->
<!--                                        <span class="line"></span>分类名称</th>-->
<!--                                    <th class="span3 sortable align-right">-->
<!--                                        <span class="line"></span>操作</th>-->
<!--                                </tr>-->
<!--                            </thead>-->
<!--                            <tbody>-->
<!--                                <!-- row -->
<!--                                --><?php //foreach ($cates as $cate) :?>
<!--                                <tr class="first">-->
<!--                                    <td>--><?php //echo $cate['id']; ?><!--</td>-->
<!--                                    <td>--><?php //echo $cate['title']; ?><!--</td>-->
<!--                                    <td class="align-right">-->
<!--                                        <a href="--><?php //echo yii\helpers\Url::to(['category/mod', 'id' => $cate['id']]); ?><!--">编辑</a>-->
<!--                                        <a href="--><?php //echo yii\helpers\Url::to(['category/del', 'id' => $cate['id']]); ?><!--">删除</a>-->
<!--                                    </td>-->
<!--                                </tr>-->
<!--                                --><?php //endforeach; ?>
<!--                            </tbody>-->
<!--                        </table>-->
                    </div>
                    <div class="pagination pull-right">

                        <?php
                            echo \yii\widgets\LinkPager::widget([
                                    'pagination' => $pager,
                                    'prevPageLabel' => '&#8249',
                                    'nextPageLabel' => '&#8250'
                                ]);

                        ?>
                    </div>
                    <!-- end users table -->
                </div>
            </div>

        <!-- end main container -->
<?php
$rename = \yii\helpers\Url::to(['category/rename']);
$deletename = \yii\helpers\Url::to(['category/delete']);
//post提交csrf攻击验证
$csrfvar = Yii::$app->request->csrfParam;
$csrfval = Yii::$app->request->getCsrfToken();
//必须用资源加载注册,如果写js则无法用php资源包里面的资源文件  
$js = <<<EOF
    $("#w0").on("rename_node.jstree", function(e, data) {
        //console.log(data);   
        var newtext = data.text;
        var old = data.old;
        var id = data.node.id;
        var postData = {
            '$csrfvar' : '$csrfval',
            'newtext' : newtext,
            'old' : old,
            'id' : id
        }
        $.post('$rename', postData, function(data) {
            if (data.code != 1) {
                alert(data.message);
                window.location.reload();
            }
        });
    }).on("delete_node.jstree", function(e, data) {
        var id = data.node.id;
        $.get('$deletename', {'id' : id}, function(data) {
            if (data.code != 1) {
                alert(data.message);
                window.location.reload();
            }
        });
    });
EOF;
$this->registerJs($js);
?>
