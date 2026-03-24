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
.my-btn{
    background-color: #fcad0f;
    padding:5px 15px;
    display: block;
    color:#000000;
    font-weight: bold;
    font-size:12px;
    border-radius:10px;
    
}
.input-text-add, .input-price-add{
    width: 100%;
    border: 1px solid #cccccc;
    border-radius:10px;
    height: 50px;
    padding-left:20px;
    font-size:13px;
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
<script>

$(document).ready(function () {
    var itemBox = '.cover'; 
    var itemInputText = '.input-text'; 
    var itemInputPrice = '.input-price'; 
    var itemDel = $('.text');
    itemDel.click(function(e){

        var $this = $(this);
        var $thisItem = $this.closest(itemBox);
        var $thisInputText = $this.next(itemInputText);
        console.log($thisInputText.val());
        var thisIndex = $thisItem.attr('data-id');
         $.ajax({
              url:'/ajax/updatepricetext',
              data:{'itemid':thisIndex, 'text':$thisInputText.val() }
              
        });
    });
 });
 
 
 $(document).ready(function () {
    var itemBox = '.cover'; 
    var itemInputPrice = '.input-price'; 
    var itemDel = $('.price');
    itemDel.click(function(e){

        var $this = $(this);
        var $thisItem = $this.closest(itemBox);
        var $thisInputPrice = $this.next(itemInputPrice);
        console.log($thisInputPrice.val());
        var thisIndex = $thisItem.attr('data-id');
         $.ajax({
              url:'/ajax/updatepricetext2',
              data:{'itemid':thisIndex, 'price':$thisInputPrice.val() }
              
        });
    });
 });
 
 
 
 
 
 
 
 
</script>



 <script>
$(document).ready(function() {
    var itemBox = '.cover';       // контейнер записи, id записи храним в data-id
    var itemDel = $('.my-btn');
   
    itemDel.click(function(e){

        var $this = $(this);
        var $thisItem = $this.closest(itemBox);
        var thisIndex = $thisItem.attr('data-id');
        var result = confirm('Действительно удалить цену?');
        if (result) {
            $.ajax({
              url:'/ajax/delete_price',
              data:{'itemid':thisIndex},
              success:function(r){
                  $thisItem.slideUp(300,function(){
                      $thisItem.remove();
                  });
              }
        });
        }


});
}); 



</script>

                    <div class="col-12 col-md-12 col-lg order-2 order-md-3 order-lg-2">
                        <div class="tech-middle">
                            <div class="for-title">
                                <div class="title fs-38 fs-mob-25 fs-lg-30 mb-3">
                                    Стоимость услуг | <?echo $user['email']?>
                                </div>
                                <p class="fs-16">Ниже вы можете добавить стоимость Ваших услуг</p>
                            </div>
                            <div class="tm-blocks">
                                
                                    <div class="tm-block">
                                        <div class="tmb-inner">
                                           
                                            <div class="tmb-rows">
                                                <div class="tmb-row">
                                                    <div class="row align-items-center">
                                                        <div class="col-5">
                                                            <div class="inputblock">
                                                                <p>Наименование услуги</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-5">
                                                            <div class="inputblock">
                                                                <p>Цена</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="inputblock">
                                                                <p>Удалить</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php foreach ($price as $item):?>
                                                    <div class="row align-items-center cover" data-id="<?echo $item['id'];?>">
                                                        <div class="col-5">
                                                            <div class="inputblock">
                                                                <div class="icon text"></div>
                                                                <input class="input-text" value="<?echo $item['text']?>" name="text" type="text" required="required">
                                                            </div>
                                                        </div>
                                                        <div class="col-5">
                                                            <div class="inputblock">
                                                                <div class="icon price"></div>
                                                                <input class="input-price" value="<?echo $item['price']?>" name="price" type="text" required="required">
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="inputblock">
                                                                <a class="my-btn" href="#">Удалить</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <? endforeach;?>
                                                </div>
                                                
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tm-block">
                                        
                                      <form action="/cabinet/addprice" method="post">
                                      <div class="row align-items-center cover">
                                                        <div class="col-8">
                                                            <div class="inputblock">
                                                                <div class="icon text"></div>
                                                                <input class="input-text-add" value="" name="text" type="text" required="required" placeholder="Наименование услуги">
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="inputblock">
                                                                <div class="icon price"></div>
                                                                <input class="input-price-add" value="" name="price" type="text" required="required" placeholder="Цена">
                                                            </div>
                                                        </div>
                                                        <div class="col-1">
                                                            <div class="inputblock">
                                                                <div class="icon price"></div>
                                                                руб.
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <br /><br />
                                        <div class="order">
                                            <button class="cl-btn" type="submit" name="save" value="Добавить">
                                                Добавить
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <?$this->load->view("templates/cabinet/fotter_view")?>
