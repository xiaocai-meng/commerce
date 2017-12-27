<p>尊敬的<?php echo $adminuser ?>, 您好: </p>

<p>您的找回密码链接如下</p>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['admin/manage/mailchangepassword', 'timestamp' => $time, 'adminuser' => $adminuser, 'token' => $token]); ?>
<p><a href="<?php echo $url ?>"><?php echo $url ?></a></p>

<p>该链接10分钟有效,请勿泄漏给他人!</p>

<p>该邮件为系统默认发送邮件请勿回复!</p>