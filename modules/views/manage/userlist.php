<!-- main container -->

    <div class="container-fluid">
        <div id="pad-wrapper" class="users-list">
            <div class="row-fluid header">
                <h3>管理员列表</h3>
                <div class="span10 pull-right">
                    <a href="<?php echo yii\helpers\Url::to(['manage/addmanage']); ?>" class="btn-flat success pull-right">
                        <span>&#43;</span>添加新管理员</a>
                </div>
            </div>
            <!-- Users table -->
            <div class="row-fluid table">

                <?php
                    if (Yii::$app->session->hasFlash('info')) {
                        echo Yii::$app->session->getFlash('info');
                    }
                ?>

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="span2">管理员ID</th>
                        <th class="span2">
                            <span class="line"></span>管理员账号</th>
                        <th class="span2">
                            <span class="line"></span>管理员邮箱</th>
                        <th class="span3">
                            <span class="line"></span>最后登录时间</th>
                        <th class="span3">
                            <span class="line"></span>最后登录IP</th>
                        <th class="span2">
                            <span class="line"></span>添加时间</th>
                        <th class="span2">
                            <span class="line"></span>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- row -->

                    <?php foreach ($manages as $key => $value) : ?>

                    <tr>
                        <td><?php echo $value->adminid; ?></td>
                        <td><?php echo $value->adminuser; ?></td>
                        <td><?php echo $value->adminemail; ?></td>
                        <td><?php echo date('Y-m-d H:i:s' ,$value->logintime); ?></td>
                        <td><?php echo long2ip($value->loginip); ?></td>
                        <td><?php echo date('Y-m-d H:i:s' ,$value->createtime); ?></td>
                        <td class="align-right">
                            <a href="<?php echo Yii\helpers\Url::to(['manage/delete', 'adminid' => $value->adminid]); ?>">删除</a>
                            <a href="<?php echo Yii\helpers\Url::to(['manage/assign', 'adminid' => $value->adminid]); ?>">授权</a>
                        </td>

                    </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination pull-right">
                <?php
                    echo yii\widgets\LinkPager::widget([
                        'pagination' => $page,
                        'prevPageLabel' => '&#8249;',
                        'nextPageLabel' => '&#8250;'
                        ]);
                ?>
            </div>
            <!-- end users table --></div>
    </div>

<!-- end main container -->
