<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
?>
<!-- ============================================================= HEADER : END ============================================================= -->		<!-- ========================================= MAIN ========================================= -->
<main id="authentication" class="inner-bottom-md">
	<div class="container">
		<div class="row">
			
			<div class="col-md-6">
				<section class="section sign-in inner-right-xs">
                    <h2 class="bordered">
                        <!-- 输出QQ返回信息中的50像素的头像 -->
                        <img src="<?php echo Yii::$app->session['userinfo']['figureurl_1'] ?>">
                        <!-- 输出QQ返回信息中的用户名      -->
                        <?php echo Yii::$app->session['userinfo']['nickname']; ?>
                        完善您的信息
                    </h2>
                    
					<p>请填写一个用户名和密码</p>

					<div class="social-auth-buttons">
					</div>

                    <?php $form = ActiveForm::begin([
                        'fieldConfig' => [
                            'template' => '<div class="field-row">{error}{label}{input}</div>'
                        ],
                        'options' => [
                            'class' => 'login-form cf-style-1',
                            'role' => 'form',
                        ],
                        //'action' => ['member/auth'],
                    ]); ?>

<!--                    <input type="text" value="--><!--" class="le-input" /><br>-->
                        <?php echo $form->field($model, 'username')->textInput(['class' => 'le-input']); ?>
                        <?php echo $form->field($model, 'userpassword')->passwordInput(['class' => 'le-input']); ?>
                        <?php echo $form->field($model, 'confirmPassword')->passwordInput(['class' => 'le-input']); ?>
                        <div class="field-row clearfix">
                        </div>

                        <div class="buttons-holder">
                            <?php echo Html::submitButton('完善信息', ['class' => 'le-button huge']); ?>
                        </div><!-- /.buttons-holder -->

                    <?php ActiveForm::end(); ?><!-- /.cf-style-1 -->

				</section><!-- /.sign-in -->
			</div><!-- /.col -->

		</div><!-- /.row -->
	</div><!-- /.container -->
</main><!-- /.authentication -->
<!-- ========================================= MAIN : END ========================================= -->		<!-- ============================================================= FOOTER ============================================================= -->






