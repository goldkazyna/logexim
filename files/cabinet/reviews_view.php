<?php if($user['status'] != '1' && $user['status'] != '2') { header("Location: /cabinet"); } ?>
<?$this->load->view("templates/cabinet/left_menu_view")?>
<style>
.review-block{
    padding:20px;
    background-color: #ffffff;
    border-radius:10px;
    border:1px solid #2C4659;
}
.review-email{
    font-weight: bold;
    text-align: left;
    letter-spacing: 1.5;
}
.review-date{
    text-align: right;
    font-size:14px;
}
.review-text{
    margin-top:30px;
    font-size:15px;
}
.review-rating{
    margin-top:20px;
}
</style>
<script src="<?echo base_url()?>js/rated/jquery.rateit.js"></script>
<link rel="stylesheet" type="text/css" href="<?echo base_url()?>js/rated/rateit.css"/>
<script>
    $(function(){
        $(".rateit").click(function(){
            var n = $('.rateit').rateit('value');
            $('.rating').val(n);
        });
    });     
</script>  
                    <div class="col-12 col-md-12 col-lg order-2 order-md-3 order-lg-2">
                        <div class="tech-middle">
                            <div class="for-title">
                                <div class="title fs-38 fs-mob-25 fs-lg-30 mb-3">
                                    Отзывы | <?echo $user['email']?>
                                </div>
                                <p class="fs-16">Ниже вы видите ваши отзывы которые оставили о Вас ваши клиенты</p>
                            </div>
                            <?if (!empty($reviews)){?>
                            <?php foreach ($reviews as $item):?>
                            <div class="row review-block">
                                <div class="col-lg-2 col-xs-12 review-email">
                                    <?echo $item['email']?>
                                </div>
                                <div class="col-lg-10 col-xs-12 review-date">
                                    <?echo $item['date']?>
                                </div>
                                <div class="col-lg-12 col-xs-12 review-text">
                                <?echo $item['review']?>
                                </div>
                                <div class="col-lg-12 review-rating">
                                    <div class="rateit" data-rateit-value="<?echo $item['rating']?>" data-rateit-ispreset="true" data-rateit-readonly="true">
                                        </div>
                                        &nbsp;&nbsp;
                                        
                                </div>
                            </div>
                            <? endforeach;?>
                            <?;} else {?>
                            <div class="row review-block">
                                
                                <div class="col-lg-12 col-xs-12 review-text">
                                У вас нету пока отзывов
                                </div>
                            </div>
                            <?;}?>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <?$this->load->view("templates/cabinet/fotter_view")?>
