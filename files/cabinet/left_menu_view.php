<!DOCTYPE html>

<html>

<head>
    <title>Личный кабинет</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/assets/cabinet/plugins/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/assets/cabinet/css/style.css?t=<?php echo date('U'); ?>">
    <link rel="stylesheet" href="/assets/cabinet/css/font-sizes.css">

    <script src="/assets/cabinet/plugins/jquery/jquery-3.2.0.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/assets/cabinet/plugins/bootstrap/js/popper.min.js"></script>
    <script src="/assets/cabinet/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/cabinet/plugins/jqueryvalidate/jquery.validate.min.js"></script>
    <script src="/assets/cabinet/js/my.js?t=<?php echo date('U'); ?>"></script>
    <script src="//code.jivosite.com/widget/h95auhJ3z8" async></script>
</head>
<style>
.profilephoto input, .fill-order form .photos .photo input {
    display: none;
}
.profilephoto input, .fill-order form .photos .photo input {
    display: none;
}
.profilephoto label, .fill-order form .photos .photo label {
    width: 300px;
    height: 300px;
    border-radius: 2px;
    border: 1px solid #c9c9c9;
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 27px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    color: #919191;
}
.profilephoto span{
    font-weight: bold;
    display: block;
    text-align: center;
    border-bottom:1px solid #000000;
    margin-bottom:10px;
}
.download{
    width: 100%;
    display: block;
    margin-top:10px;
    cursor: pointer;
    background-color: #fcad0f;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight: bold;
    padding:10px;
    margin-bottom:10px;
    
}
.download:hover{
    background-color: #f0c161;
}
.tech-header .logo img{
    width: auto !important;
}
</style>
<body class="tech-page">
    <!--***БОКОВОЕ МОБ МЕНЮ***-->
    <div class="for-mob-menu d-xl-none">
        <div class="tech-manage">
            <div class="logo">
                <a href="#">
                    <img src="/images/1.PNG" alt="logo">
                </a>
            </div>
            <div class="for-cart"></div>
        </div>
        <div class="tech-humb">
            <div class="dv-1"></div>
            <div class="dv-2"></div>
            <div class="dv-3"></div>
        </div>

        <div class="tech-mobile-menu">
            <div class="for-menu"></div>
            <div class="for-other"></div>
        </div>
        <div class="tech-menu-back"></div>
    </div>
    <!--//боковое моб. меню-->

    <div class="wrapper">
        <div class="for-top">
            <div class="tech-header">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl d-flex align-items-center">
                            <div class="logo">
                                <a href="/">
                                    <img src="/images/1.PNG" alt="logo" style="height: 55px;">
                                </a>
                            </div>
                            <div class="for-menu pl-5">
                                <div class="menu">
                                    <div class="nav-item">
                                        <a class="dropdown-item nav-link" href="/">Главная</a>
                                    </div>
                                    
                                    <!--<div class="dropdown nav-item">
                                        <a href="#" class="btn dropdown-toggle nav-link" data-toggle="dropdown">
                                            Новости
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <div class="nav-item">
                                                <a class="dropdown-item nav-link" href="#">Action</a>
                                            </div>
                                            <div class="nav-item">
                                                <a class="dropdown-item nav-link" href="#">Another action</a>
                                            </div>
                                            <div class="nav-item">
                                                <a class="dropdown-item nav-link" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </div>-->
                                    <div class="nav-item">
                                        <a class="dropdown-item nav-link" href="/cabinet/kontakty">Контакты</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="for-login">
                                <a href="/cabinet/logout" class="login d-flex align-items-center">
                                    <div class="icon mr-2">
                                        <img src="/assets/cabinet/images/login.png" alt="icon">
                                    </div>
                                    <p class="font-weight-bold fs-16"><?echo $user['email']?></p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="breadcrumbs">
                <div class="container">
                    <a href="/"><span>Главная</span></a> <span class="divider">/</span> <a href="">Личный кабинет</a>
                </div>
            </div>
        </div>

        <div class="tech-inner">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3 col-xl-auto order-1">
                        <div class="tech-left">
                            <div class="photo-edit">
                               
                                <form method="post" enctype="multipart/form-data" action="/cabinet/add_avatar">
                                <div class="profilephoto">
                                    
                                <input name="avaphoto"  id="avaphoto" type="file"/>
                                <input name="avaupload" value="1" type="hidden"/>
                                    <?php
                                    $photdata='';
                                    if(!empty($user['ava']))
                                    {
                                        $photdata="background-image: url('".$user['ava']."'); background-repeat: no-repeat; background-size: cover;";
                                    } else {
                                        $photdata="background-image: url('/images/images/1645549245ZD6St1heq0vLXUiA.jpg'); background-repeat: no-repeat; background-size: cover;";
                                    }
                                    ?>
                                <label style="<?=$photdata?>" for="avaphoto">+</label>
                                </div>
                                <button type='submit' class="download">Загрузить</button>
                            </form>
                            </div>
                            <div class="tech-controls">
                                <div class="link">
                                    <a class="active" href="/cabinet">
                                        <div class="icon">
                                            <img src="/assets/cabinet/images/mn1.png" alt="icon">
                                        </div>
                                        <p>Личный профиль</p>
                                    </a>
                                </div>
                                <?php if($user['status'] == '1' OR $user['status'] == '2') { ?>
                                <div class="link">
                                    <a href="/cabinet/price">
                                        <div class="icon">
                                            <img src="/assets/cabinet/images/mn5.png" alt="icon">
                                        </div>
                                        <p>Услуги и стоимость</p>
                                    </a>
                                </div>
                                <div class="link">
                                    <a href="/cabinet/portfolio">
                                        <div class="icon">
                                            <img src="/assets/cabinet/images/mn2.png" alt="icon">
                                        </div>
                                        <p>Портфолио</p>
                                    </a>
                                </div>
                                <div class="link">
                                    <a href="/cabinet/message">
                                        <div class="icon">
                                            <img src="/assets/cabinet/images/mn4.png" alt="icon">
                                        </div>
                                        <p>Мои заказы</p>
                                    </a>
                                </div>
                                <div class="link">
                                    <a href="/cabinet/reviews">
                                        <div class="icon">
                                            <img src="/assets/cabinet/images/mn3.png" alt="icon">
                                        </div>
                                        <p>Отзывы</p>
                                    </a>
                                </div>
                                <?php } ?>
                                <?php if($user['status'] == '0') { ?>
                                <div class="link">
                                    <a href="/cabinet/message">
                                        <div class="icon">
                                            <img src="/assets/cabinet/images/mn4.png" alt="icon">
                                        </div>
                                        <p>Мои заказы</p>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="order">
                                <a class="cl-btn" href="/">
                                    
                                    Интернет-магазин
                                </a>
                            </div>
                            <br />
                            <div class="order">
                                <a class="cl-btn" href="/cart">
                                    <span class="icon mr-3">
                                        <img src="/assets/cabinet/images/tech-cart.png" alt="cart">
                                    </span>
                                   Корзина товаров
                                </a>
                            </div>
                        </div>
                    </div>
<script>
$('input[type="file"]').on("change", function(){ 
                var id=this.id
                console.log("click id="+id);
                var file=this.files[0];
                var data=getBase64(this,file);
            });
			function getBase64(elem,file) {
                var reader = new FileReader();
                reader.readAsDataURL(file);
                var container=$(elem).parent().find('label').first();
                reader.onload = function () {
                    console.log(reader.result);
                    
                    
                    container.css('background-image', 'url(' + reader.result + ')');
                    container.css('background-repeat', 'no-repeat');
                    container.css('background-size', 'cover');
                    return reader.result;
                };
                reader.onloadstart  = function(event) {
                        
                        container.css('background-image', 'url("/img/spinload.svg")');
                        container.css('background-repeat', 'no-repeat');
                        container.css('background-size', 'cover');
                        
                    };
                
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                    return null;
                };
            
            }
</script>