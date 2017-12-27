<?php
//引入资源包里面注册的css和JS文件 如果不注册则无法显示debug状态条
use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <!-- Meta -->
    <meta charset="utf-8">

    <?php
        $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
    ?>

<!--    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="MediaCenter, Template, eCommerce">
    <meta name="robots" content="all">

    <title><?php Html::encode($this->title); ?>-京西商城</title>

        <!-- Bootstrap Core CSS -->
<!--    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">-->

        <!-- Customizable CSS -->
<!--    <link rel="stylesheet" href="/assets/css/main.css">-->
<!--    <link rel="stylesheet" href="/assets/css/red.css">-->
<!--    <link rel="stylesheet" href="/assets/css/owl.carousel.css">-->
<!--    <link rel="stylesheet" href="/assets/css/owl.transitions.css">-->
<!--    <link rel="stylesheet" href="/assets/css/animate.min.css">-->

        <!-- Icons/Glyphs -->
<!--    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">-->

        <!-- Favicon -->
<!--    <link rel="shortcut icon" href="/assets/images/favicon.ico">-->

        <!-- HTML5 elements and media queries Support for IE8 : HTML5 shim and Respond.js -->
        <!--[if lt IE 9]>-->
<!--    <script src="/assets/js/html5shiv.js"></script>-->
<!--    <script src="/assets/js/respond.min.js"></script>-->
<!--        <![endif]-->

    <link rel="shortcut icon" href="/images/favicon.ico">

    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div class="wrapper">
    <!-- ============================================================= TOP NAVIGATION ============================================================= -->
    <nav class="top-bar animate-dropdown">
        <div class="container">
            <div class="col-xs-12 col-sm-6 no-margin">
                <ul>
                    <li><a href="<?php echo yii\helpers\Url::to(['/index']); ?>">首页</a></li>
                    <?php
                        //if (\Yii::$app->session['isLogin'] == 1) :
                        if (!\Yii::$app->user->isGuest) :
                    ?>
<!--                    <li><a href="category-grid.html">所有分类</a></li>-->
                    <li><a href="<?php echo yii\helpers\Url::to(['/cart']); ?>">我的购物车</a></li>
                    <li><a href="<?php echo yii\helpers\Url::to(['/order/index']);  ?>">我的订单</a></li>
                    <?php endif; ?>
                </ul>
            </div><!-- /.col -->

            <div class="col-xs-12 col-sm-6 no-margin">
                <ul class="right">
                   
                    <?php
                        //if (\Yii::$app->session['isLogin'] == 1) :
                        if (!\Yii::$app->user->isGuest) :
                    ?>
                        您好,欢迎您回来 <?php echo \Yii::$app->user->identity->username; //\Yii::$app->session['loginname']; ?> <a href="<?php echo yii\helpers\Url::to(['member/logout']); ?>">退出</a>
                    <?php else: ?>
                    <li><a href="<?php echo yii\helpers\Url::to(['member/auth']); ?>">注册</a></li>
                    <li><a href="<?php echo yii\helpers\Url::to(['member/auth']); ?>">登录</a></li>
                    <?php endif; ?>

                </ul>
            </div><!-- /.col -->
        </div><!-- /.container -->
    </nav><!-- /.top-bar -->
    <!-- ============================================================= TOP NAVIGATION : END ============================================================= -->		<!-- ============================================================= HEADER ============================================================= -->
    <header>
        <div class="container no-padding">

            <div class="col-xs-12 col-sm-12 col-md-3 logo-holder">
                <!-- ============================================================= LOGO ============================================================= -->
                <div class="logo">
                    <a href="index.html">
                        <img alt="logo" src="/images/logo.PNG" width="233" height="54"/>
                    </a>
                </div><!-- /.logo -->
                <!-- ============================================================= LOGO : END ============================================================= -->		</div><!-- /.logo-holder -->

            <div class="col-xs-12 col-sm-12 col-md-6 top-search-holder no-margin">
                <div class="contact-row">
                    <div class="phone inline">
                        <i class="fa fa-phone"></i> (+086) 123 456 7890
                    </div>
                    <div class="contact inline">
                        <i class="fa fa-envelope"></i> contact@<span class="le-color">jason.com</span>
                    </div>
                </div><!-- /.contact-row -->
                <!-- ============================================================= SEARCH AREA ============================================================= -->

                <div class="search-area">
                    <?php
                        ActiveForm::begin([
                            'action' => ['product/search'],
                            'options' => [
                                'id' => 'searchform',
                            ],
                            'method' => 'get'
                        ]);
                    ?>
                        <div class="control-group">
                            <input class="search-field" name='keyword' placeholder="搜索商品" value="<?php echo $keyword; ?>" />

                            <ul class="categories-filter animate-dropdown">
                                <li class="dropdown">

                                    <a class="dropdown-toggle"  data-toggle="dropdown" href="category-grid.html">所有分类</a>

                                    <ul class="dropdown-menu" role="menu" >
                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="category-grid.html">电子产品</a></li>
                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="category-grid.html">电子产品</a></li>
                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="category-grid.html">电子产品</a></li>
                                        <li role="presentation"><a role="menuitem" tabindex="-1" href="category-grid.html">电子产品</a></li>

                                    </ul>
                                </li>
                            </ul>

                            <a style="padding:15px 15px 13px 12px" class="search-button" href="javascript:document.getElementById('searchform').submit();" ></a>

                        </div>
                    <?php
                        ActiveForm::end();
                    ?>
                </div><!-- /.search-area -->
                <!-- ============================================================= SEARCH AREA : END ============================================================= -->		</div><!-- /.top-search-holder -->

            <div class="col-xs-12 col-sm-12 col-md-3 top-cart-row no-margin">
                <div class="top-cart-row-container">

                    <!-- ============================================================= SHOPPING CART DROPDOWN ============================================================= -->
                    <div class="top-cart-holder dropdown animate-dropdown">

                        <div class="basket">

                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <div class="basket-item-count">
                                    <span class="count"><?php echo count($this->params['cart']['products']); ?></span>
                                    <img src="/images/icon-cart.png" alt="" />
                                </div>

                                <div class="total-price-basket">
                                    <span class="lbl">您的购物车:</span>
                    <span class="total-price">
                        <span class="sign">￥</span><span class="value"><?php echo $this->params['cart']['total']; ?></span>
                    </span>
                                </div>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <div class="basket-item">
                                        <div class="row">
                                            <div class="col-xs-4 col-sm-4 no-margin text-center">
                                                <div class="thumb">
                                                    <img alt="" src="/images/products/product-small-01.jpg" />
                                                </div>
                                            </div>
                                            <div class="col-xs-8 col-sm-8 no-margin">
                                                <div class="title">前端课程</div>
                                                <div class="price">￥270.00</div>
                                            </div>
                                        </div>
                                        <a class="close-btn" href="#"></a>
                                    </div>
                                </li>

                                <li>
                                    <div class="basket-item">
                                        <div class="row">
                                            <div class="col-xs-4 col-sm-4 no-margin text-center">
                                                <div class="thumb">
                                                    <img alt="" src="/images/products/product-small-01.jpg" />
                                                </div>
                                            </div>
                                            <div class="col-xs-8 col-sm-8 no-margin">
                                                <div class="title">Java课程</div>
                                                <div class="price">￥270.00</div>
                                            </div>
                                        </div>
                                        <a class="close-btn" href="#"></a>
                                    </div>
                                </li>

                                <li>
                                    <div class="basket-item">
                                        <div class="row">
                                            <div class="col-xs-4 col-sm-4 no-margin text-center">
                                                <div class="thumb">
                                                    <img alt="" src="/images/products/product-small-01.jpg" />
                                                </div>
                                            </div>
                                            <div class="col-xs-8 col-sm-8 no-margin">
                                                <div class="title">PHP课程</div>
                                                <div class="price">￥270.00</div>
                                            </div>
                                        </div>
                                        <a class="close-btn" href="#"></a>
                                    </div>
                                </li>


                                <li class="checkout">
                                    <div class="basket-item">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <a href="cart.html" class="le-button inverse">查看购物车</a>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <a href="checkout.html" class="le-button">去往收银台</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div><!-- /.basket -->
                    </div><!-- /.top-cart-holder -->
                </div><!-- /.top-cart-row-container -->
                <!-- ============================================================= SHOPPING CART DROPDOWN : END ============================================================= -->		</div><!-- /.top-cart-row -->

        </div><!-- /.container -->
    </header>
    <!-- ============================================================= HEADER : END ============================================================= -->		
     
     
        
        <?php echo $content; ?>
        
        
        
        
        <footer id="footer" class="color-bg">

            <div class="container">
                <div class="row no-margin widgets-row">
                    <div class="col-xs-12  col-sm-4 no-margin-left">
                        <!-- ============================================================= FEATURED PRODUCTS ============================================================= -->
                        <div class="widget">
                            <h2>推荐商品</h2>
                            <div class="body">
                                <ul>
                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">Netbook Acer Travel B113-E-10072</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-01.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">PowerShot Elph 115 16MP Digital Camera</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="/images/products/product-small-02.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">PowerShot Elph 115 16MP Digital Camera</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="/images/products/product-small-03.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div><!-- /.body -->
                        </div> <!-- /.widget -->
                        <!-- ============================================================= FEATURED PRODUCTS : END ============================================================= -->            </div><!-- /.col -->

                    <div class="col-xs-12 col-sm-4 ">
                        <!-- ============================================================= ON SALE PRODUCTS ============================================================= -->
                        <div class="widget">
                            <h2>促销商品</h2>
                            <div class="body">
                                <ul>
                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">HP Scanner 2910P</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-04.jpg" />
                                                </a>
                                            </div>
                                        </div>

                                    </li>
                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">Galaxy Tab 3 GT-P5210 16GB, Wi-Fi, 10.1in - White</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-05.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">PowerShot Elph 115 16MP Digital Camera</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-06.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div><!-- /.body -->
                        </div> <!-- /.widget -->
                        <!-- ============================================================= ON SALE PRODUCTS : END ============================================================= -->            </div><!-- /.col -->

                    <div class="col-xs-12 col-sm-4 ">
                        <!-- ============================================================= TOP RATED PRODUCTS ============================================================= -->
                        <div class="widget">
                            <h2>最热商品</h2>
                            <div class="body">
                                <ul>
                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">Galaxy Tab GT-P5210, 10" 16GB Wi-Fi</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-07.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">PowerShot Elph 115 16MP Digital Camera</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-08.jpg" />
                                                </a>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9 no-margin">
                                                <a href="single-product.html">Surface RT 64GB, Wi-Fi, 10.6in - Dark Titanium</a>
                                                <div class="price">
                                                    <div class="price-prev">￥2000</div>
                                                    <div class="price-current">￥1873</div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3 no-margin">
                                                <a href="#" class="thumb-holder">
                                                    <img alt="" src="/images/blank.gif" data-echo="assets/images/products/product-small-09.jpg" />
                                                </a>
                                            </div>

                                        </div>
                                    </li>
                                </ul>
                            </div><!-- /.body -->
                        </div><!-- /.widget -->
                        <!-- ============================================================= TOP RATED PRODUCTS : END ============================================================= -->            </div><!-- /.col -->

                </div><!-- /.widgets-row-->
            </div><!-- /.container -->

            <div class="sub-form-row">
                <!--<div class="container">
                    <div class="col-xs-12 col-sm-8 col-sm-offset-2 no-padding">
                        <form role="form">
                            <input placeholder="Subscribe to our newsletter">
                            <button class="le-button">Subscribe</button>
                        </form>
                    </div>
                </div>--><!-- /.container -->
            </div><!-- /.sub-form-row -->

            <div class="link-list-row">
                <div class="container no-padding">
                    <div class="col-xs-12 col-md-4 ">
                        <!-- ============================================================= CONTACT INFO ============================================================= -->
                        <div class="contact-info">
                            <div class="footer-logo">
                                <img alt="logo" src="/images/logo.PNG" width="233" height="54"/>
                            </div><!-- /.footer-logo -->

                            <p class="regular-bold"> 请通过电话，电子邮件随时联系我们</p>

                            <p>
                                西城区二环到三环德胜门外大街10号TCL大厦3层(马甸桥南), 北京市西城区, 中国
                                <br>京西 (QQ群:416465236)
                            </p>

                            <!--<div class="social-icons">
                                <h3>Get in touch</h3>
                                <ul>
                                    <li><a href="http://facebook.com/transvelo" class="fa fa-facebook"></a></li>
                                    <li><a href="#" class="fa fa-twitter"></a></li>
                                    <li><a href="#" class="fa fa-pinterest"></a></li>
                                    <li><a href="#" class="fa fa-linkedin"></a></li>
                                    <li><a href="#" class="fa fa-stumbleupon"></a></li>
                                    <li><a href="#" class="fa fa-dribbble"></a></li>
                                    <li><a href="#" class="fa fa-vk"></a></li>
                                </ul>
                            </div>--><!-- /.social-icons -->

                        </div>
                        <!-- ============================================================= CONTACT INFO : END ============================================================= -->            </div>

                    <div class="col-xs-12 col-md-8 no-margin">
                        <!-- ============================================================= LINKS FOOTER ============================================================= -->
                        <div class="link-widget">
                            <div class="widget">
                                <h3>快速检索</h3>
                                <ul>
                                    <li><a href="category-grid.html">laptops &amp; computers</a></li>
                                    <li><a href="category-grid.html">Cameras &amp; Photography</a></li>
                                    <li><a href="category-grid.html">Smart Phones &amp; Tablets</a></li>
                                    <li><a href="category-grid.html">Video Games &amp; Consoles</a></li>
                                    <li><a href="category-grid.html">TV &amp; Audio</a></li>
                                    <li><a href="category-grid.html">Gadgets</a></li>
                                    <li><a href="category-grid.html">Car Electronic &amp; GPS</a></li>
                                    <li><a href="category-grid.html">Accesories</a></li>
                                </ul>
                            </div><!-- /.widget -->
                        </div><!-- /.link-widget -->

                        <div class="link-widget">
                            <div class="widget">
                                <h3>热门商品</h3>
                                <ul>
                                    <li><a href="category-grid.html">Find a Store</a></li>
                                    <li><a href="category-grid.html">About Us</a></li>
                                    <li><a href="category-grid.html">Contact Us</a></li>
                                    <li><a href="category-grid.html">Weekly Deals</a></li>
                                    <li><a href="category-grid.html">Gift Cards</a></li>
                                    <li><a href="category-grid.html">Recycling Program</a></li>
                                    <li><a href="category-grid.html">Community</a></li>
                                    <li><a href="category-grid.html">Careers</a></li>

                                </ul>
                            </div><!-- /.widget -->
                        </div><!-- /.link-widget -->

                        <div class="link-widget">
                            <div class="widget">
                                <h3>最近浏览</h3>
                                <ul>
                                    <li><a href="category-grid.html">My Account</a></li>
                                    <li><a href="category-grid.html">Order Tracking</a></li>
                                    <li><a href="category-grid.html">Wish List</a></li>
                                    <li><a href="category-grid.html">Customer Service</a></li>
                                    <li><a href="category-grid.html">Returns / Exchange</a></li>
                                    <li><a href="category-grid.html">FAQs</a></li>
                                    <li><a href="category-grid.html">Product Support</a></li>
                                    <li><a href="category-grid.html">Extended Service Plans</a></li>
                                </ul>
                            </div><!-- /.widget -->
                        </div><!-- /.link-widget -->
                        <!-- ============================================================= LINKS FOOTER : END ============================================================= -->            </div>
                </div><!-- /.container -->
            </div><!-- /.link-list-row -->

            <div class="copyright-bar">
                <div class="container">
                    <div class="col-xs-12 col-sm-6 no-margin">
                        <div class="copyright">
                            &copy; <a href="index.html">jingxi.com</a> - all rights reserved
                        </div><!-- /.copyright -->
                    </div>
                    <div class="col-xs-12 col-sm-6 no-margin">
                        <div class="payment-methods ">
                            <ul>
                                <li><img alt="" src="/images/payments/payment-visa.png"></li>
                                <li><img alt="" src="/images/payments/payment-master.png"></li>
                                <li><img alt="" src="/images/payments/payment-paypal.png"></li>
                                <li><img alt="" src="/images/payments/payment-skrill.png"></li>
                            </ul>
                        </div><!-- /.payment-methods -->
                    </div>
                </div><!-- /.container -->
            </div><!-- /.copyright-bar -->

        </footer><!-- /#footer -->
        <!-- ============================================================= FOOTER : END ============================================================= -->	</div><!-- /.wrapper -->

    <!-- JavaScripts placed at the end of the document so the pages load faster -->
   <script src="/assets/js/jquery-1.10.2.min.js"></script>
<!--    <script src="/assets/js/jquery-migrate-1.2.1.js"></script>-->
<!--    <script src="/assets/js/bootstrap.min.js"></script>-->
<!--    <script src="/assets/js/gmap3.min.js"></script>-->
<!--    <script src="/assets/js/bootstrap-hover-dropdown.min.js"></script>-->
<!--    <script src="/assets/js/owl.carousel.min.js"></script>-->
<!--    <script src="/assets/js/css_browser_selector.min.js"></script>-->
<!--    <script src="/assets/js/echo.min.js"></script>-->
<!--    <script src="/assets/js/jquery.easing-1.3.min.js"></script>-->
<!--    <script src="/assets/js/bootstrap-slider.min.js"></script>-->
<!--    <script src="/assets/js/jquery.raty.min.js"></script>-->
<!--    <script src="/assets/js/jquery.prettyPhoto.min.js"></script>-->
<!--    <script src="/assets/js/jquery.customSelect.min.js"></script>-->
<!--    <script src="/assets/js/wow.min.js"></script>-->
<!--    <script src="/assets/js/scripts.js"></script>-->

<script>
    $("#createlink").click(function(){
        //slideDown() 方法通过使用滑动效果，显示隐藏的被选元素
        $(".billing-address").slideDown();
    });

    $(".minus").click(function(event){
        //var cartid = $("input[name=productnum]").attr("id");
        var cartid = event.target.id;
        var num = parseInt($(event.target).attr("num"), 10) - 1;
        //var num = parseInt($("input[name=productnum]").val(), 10) - 1;
        //数量不能小于1
        if (parseInt($(event.target).attr("num"), 10) <= 1) {
            var num = 1;
        }
        //总共的价钱
        var total = parseFloat($(".value.pull-right span").html());
        //单价
        var price = parseFloat($(".price span").html());
        changeNum(cartid, num);
        //数量减少一个以后价钱也要减少一个产品的单价
        var p = total - price;
        //减少后的单间不能为负数
        if (p < 0) {
            var p = "0";
        }
        $(".value.pull-right span").html(p + "");
        $(".value.pull-right.ordertotal span").html(p + "");
    });

    $(".plus").click(function(event){
        //var cartid = $("input[name=productnum]").attr("id");
        var cartid = event.target.id;
        //console.log($(event.target).attr("num"));
        //var num = parseInt($("input[name=productnum]").val(), 10) + 1;
        var num = parseInt($(event.target).attr("num"), 10) + 1;
        //总共的价钱
        var total = parseFloat($(".value.pull-right span").html());
        //单价
        var price = parseFloat($(".price span").html());
        changeNum(cartid, num);
        var p = total + price;
        //数量增加一个以后价钱也要增加一个产品的单价
        $(".value.pull-right span").html(p + "");
        $(".value.pull-right.ordertotal span").html(p + "");
    })

    //提交ajax请求更新数据库
    function changeNum(cartid, num) {
        //reload() 方法用于重新加载当前文档 等于刷新F5键
        $.get('<?php echo yii\helpers\Url::to(['cart/mod']); ?>', {'productnum' : num, 'cartid' : cartid}, function(data){
            location.reload();
        });
    }

    //触发第一次默认地址点击事件
    $(function(){
        $("input#address0").trigger("click");
    });

    //设置addressid的值
    $("input.address").click(function(){
        var addressid = $(this).val();
        $("input[name=addressid]").val(addressid);
    });

    //选择快递时订单总额随时更新
    $(".le-radio.express").click(function(){
        var total = parseFloat($("#total span").html());
        //商品总价加快递价钱
        var ototal = parseFloat($(this).attr('data')) + total;
        $("#ototal span").html(ototal);
    });


</script>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
