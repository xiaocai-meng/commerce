
        <link rel="stylesheet" href="/assets/admin/css/compiled/user-list.css" type="text/css" media="screen" />
        <!-- main container -->

            <div class="container-fluid">
                <div id="pad-wrapper" class="users-list">
                    <div class="row-fluid header">
                        <h3>会员列表</h3>
                        <div class="span10 pull-right">
                            <a href="<?php echo yii\helpers\Url::to(['user/adduser']);?>" class="btn-flat success pull-right">
                                <span>&#43;</span>添加新用户</a></div>
                    </div>
                    <!-- Users table -->
                    <div class="row-fluid table">
                        <table class="table table-hover">

                            <?php
                            if (Yii::$app->session->hasFlash('info')) {
                                echo Yii::$app->session->getFlash('info');
                            }
                            ?>
                            
                            <thead>
                                <tr>
                                    <th class="span3 sortable">
                                        <span class="line"></span>用户名</th>
                                    <th class="span3 sortable">
                                        <span class="line"></span>真实姓名</th>
                                    <th class="span2 sortable">
                                        <span class="line"></span>昵称</th>
                                    <th class="span3 sortable">
                                        <span class="line"></span>性别</th>
                                    <th class="span3 sortable">
                                        <span class="line"></span>年龄</th>
                                    <th class="span3 sortable">
                                        <span class="line"></span>生日</th>
                                    <th class="span3 sortable align-right">
                                        <span class="line"></span>操作</th>
                                </tr>
                            </thead>

                            <?php foreach ($users as $key => $value) : ?>
                            
                            <tbody>
                                <!-- row -->
                                <tr class="first">
                                    <td>
                                        
                                    <?php if (empty($value->profile->avater)) : ?>
                                        <img src="<?php echo Yii::$app->params['defaultValue']['avater'];?>" class="img-circle avatar hidden-phone" />
                                    <?php else: ?>
                                        <img src="<?php echo $value->profile->avater;?>" class="img-circle avatar hidden-phone" />
                                    <?php endif; ?>

                                        <a href="#" class="name"><?php echo $value->username;?></a>
                                        <span class="subtext"></span>
                                        
                                    </td>

                                    <td><?php echo isset($value->profile->truename) && $value->profile->truename ? $value->profile->truename : '未填写';?></td>
                                    
                                    <td><?php echo isset($value->profile->nickname) && $value->profile->nickname ? $value->profile->nickname : '未填写';?></td>
                                    
                                    <td>
                                        <?php
                                        if (isset($value->profile) && $value->profile) {
                                            if ($value->profile->sex == 0) {
                                                echo '保密';
                                            } elseif ($value->profile->sex == 1) {
                                                echo '男';
                                            } else {
                                                echo '女';
                                            }
                                        } else {
                                            echo '未填写';
                                        }
                                        ?>
                                    </td>
                                    
                                    <td><?php echo isset($value->profile->age) && $value->profile->age ? $value->profile->age : '未填写';?></td>
                                    
                                    <td><?php echo isset($value->profile->birthday) && $value->profile->birthday ? $value->profile->birthday : '未填写';?></td>
                                    
                                    <td class="align-right">
                                        <a href="<?php echo Yii\helpers\Url::to(['user/delete', 'userid' => $value->userid]); ?>">删除</a>
                                    </td>
                                </tr>
                            
                            <?php endforeach; ?>
                            
                            </tbody>
                            
                        </table>
                    </div>
                    <div class="pagination pull-right">
                        <?php echo yii\widgets\LinkPager::widget(['pagination' => $page, 'prevPageLabel' => '&#8249;', 'nextPageLabel' => '&#8250;']); ?>
                    </div>
                    <!-- end users table --></div>
            </div>
        
        <!-- end main container -->