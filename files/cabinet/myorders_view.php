<?php if($user['status'] != '0') { header("Location: /cabinet"); } ?>
<?$this->load->view("templates/cabinet/left_menu_view")?>
<style>
.review-block{
    padding:20px;
    background-color: #ffffff;
    border-radius:10px;
    border:1px solid #2C4659;
}
</style>
<div class="col-12 col-md-12 col-lg order-2 order-md-3 order-lg-2">
    <div class="tech-middle">
        <div class="for-title">
            <div class="title fs-38 fs-mob-25 fs-lg-30 mb-3">
                Мои заказы | <?echo $user['email']?>
            </div>
        </div>
        <?if (!empty($orders)){?>
        <?php foreach ($orders as $item):
            $id = $item['id'];
            $query = $this->db->query("SELECT * FROM `order_product` WHERE `id_order` = '{$id}'");
            $query->result_array();
            ?>
        <div class="row review-block">
            <div class="col-lg-12 col-xs-12">
                <b>#</b> <?echo $id?>
            </div>
            <div class="col-lg-12 col-xs-12">
                <b>Дата:</b> <?echo $item['date']?>
            </div>
            <div class="col-lg-12 col-xs-12">
                <b>Адрес:</b> <?echo $item['address_building'].', '.$item['street'].', '.$item['house'];?>
            </div>
            <?php if ($query->num_rows() > 0) { ?>
            <div class="col-lg-12 col-xs-12">
                <b>Товар(ы):</b> <?echo $query->result_array[0]['title'];?>
            </div>
            <?php } ?>
            <div class="col-lg-12 col-xs-12">
                <b>Цена:</b> <?echo $item['total']?>
            </div>
        </div>
        <? endforeach;?>
        <?;} else {?>
        <div class="row review-block">
            
            <div class="col-lg-12 col-xs-12 review-text">
            У вас нет заказов
            </div>
        </div>
        <?;}?>
        
    </div>
</div>
</div>
</div>
</div>
<?$this->load->view("templates/cabinet/fotter_view")?>
