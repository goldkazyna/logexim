<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages_model extends CI_Model {
    function get_users_by_email($email){
        $this->db->where('email',$email);
        
        $query = $this->db->get('users');
        return $query->row_array();
    }

    function setAvator($userId,$update)
    {
        $this->db->where('id',$userId);
        $this->db->update('users',$update);
    }
    function delete_price($id){
        $this->db->where('id',$id);
        $this->db->delete('tm_price_email');
    }
    function update_price($itemid,$update) {
        $this->db->where('id',$itemid);
        $this->db->update('tm_price_email',$update);
    } 
    
function add_portoflio($post, $email){
    if(!empty($post['photo']))
			{
				$photos=json_decode($post['photo'],true);
				$thumbs=json_decode($post['thumb'],true);
				foreach($photos as $k=>$ph)
				{
					$thumb='';
					if(!empty($thumbs[$k])){$thumb=$thumbs[$k];}
					
					$this->db->insert('tm_portfolio',['email'=>$email,'img'=>$ph,'thumb_229_162'=>$thumb]);
				}
			}
}    
  function get_table_by_product_id($id_product){
        $this->db->where('id_product',$id_product);
        $this->db->order_by('id','asc');
        $query = $this->db->get('list_table');
        return $query->result_array();
    }  
function get_bottom_menu(){
    $this->db->order_by('sort','asc');
        $this->db->where('bottom_menu',1);
        $this->db->order_by('id','asc');
        $query = $this->db->get('tm_pages');
        return $query->result_array();
    }
    function get_top_menu(){
        $this->db->order_by('sort','asc');
        $this->db->where('top_menu',1);
        $this->db->order_by('id','asc');
        $query = $this->db->get('tm_pages');
        return $query->result_array();
    }
    function get_order_info($id_order){
        $this->db->where('id',$id_order);
        $query = $this->db->get('order_custom');
        return $query->row_array();
    }
    function get_product_new(){
        $this->load->model('ajax_model');
        $query = $this->db->query("SELECT * FROM `tm_product` WHERE `new` = 1");
        $count=0;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row[$count]){
                $cover_img = $this->ajax_model->get_cover_img_BY_id($row[$count]["id"]);
                if (empty($cover_img)){
                    $cover_img['img'] = '/images/no-image.png';
                }
                $row[$count]["img"] = $cover_img['img'];
                $count++;
            }
            return $row;
        }
    }
    
    function get_product_hit(){
        $this->load->model('ajax_model');
        $query = $this->db->query("SELECT * FROM `tm_product` WHERE `pop` = 1");
        $count=0;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row[$count]){
                $cover_img = $this->ajax_model->get_cover_img_BY_id($row[$count]["id"]);
                if (empty($cover_img)){
                    $cover_img['img'] = '/images/no-image.png';
                }
                $row[$count]["img"] = $cover_img['img'];
                $count++;
            }
            return $row;
        }
    }
    
    function get_product_skidka(){
        $this->load->model('ajax_model');
        $query = $this->db->query("SELECT * FROM `tm_product` WHERE `week` = 1");
        $count=0;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row[$count]){
                $cover_img = $this->ajax_model->get_cover_img_BY_id($row[$count]["id"]);
                if (empty($cover_img)){
                    $cover_img['img'] = '/images/no-image.png';
                }
                $row[$count]["img"] = $cover_img['img'];
                $count++;
            }
            return $row;
        }
    }
    
    
    function get_subcategory_count($id_category){
        $query = $this->db->query("
        SELECT COUNT(*) as count
        FROM tm_subcategory 
        WHERE id_category = '$id_category'
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }
    
function get_brand_by_id($id_brand){
    $this->db->where('id',$id_brand);
    $query=$this->db->get('brand');
    return $query->row_array();
}    
function get_country_by_id($id_brand){
    $this->db->where('id',$id_brand);
    $query=$this->db->get('country');
    return $query->row_array();
}     
    function get_main_category_by_id2($id_main_category){
    $this->db->where('id',$id_main_category);
    $query=$this->db->get('tm_main_category');
    return $query->row_array();
}
    
    function get_main_category_by_id($id_main_category){
    $this->db->where('id',$id_main_category);
    $query=$this->db->get('tm_main_category');
    return $query->result_array();
}
function get_all_products_category($category,$subcategory){
    $this->db->order_by('title','asc');
    $this->db->select('id');
    $this->db->where('id_category',$category);
    $this->db->where('id_subcategory',$subcategory);
    $query=$this->db->get('tm_product');
    return $query->result_array(); 
}
function get_pop(){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product 
        where pop=1
        ORDER BY RAND() LIMIT 15
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   } 
 function get_week(){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product_col 
        where week=1
        ORDER BY RAND() LIMIT 15
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   } 
     function get_new(){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product
        where new=1
        ORDER BY RAND() LIMIT 15
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   } 
   function get_rassprodazha(){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product 
        where rassprodazha=1
        ORDER BY RAND() LIMIT 15
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   } 
    function add_podpiska($add){
        $this->db->insert('podpiska',$add);
    }

    function get_related_products($id_subcategory){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product 
        WHERE id_subcategory='$id_subcategory'
        ORDER BY RAND() LIMIT 4
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }
   function get_related_products_col_detail($id_col){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product_col 
        WHERE id_col='$id_col'
        ORDER BY RAND() LIMIT 6
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }
   function get_products_col($id_col){
        $query = $this->db->query("
        SELECT * 
        FROM tm_product_col 
        WHERE id_col='$id_col'
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   } 
   function get_address_by_city_one($id_city){
    $this->db->where('city_id',$id_city);
    $query=$this->db->get('address');
    return $query->row_array();
}
function get_address_by_city($id_city){
    $this->db->order_by('id','asc');
    $this->db->where('city_id',$id_city);
    $query=$this->db->get('address');
    return $query->result_array();
}
   function get_city_by_category($id_category){
    $this->db->order_by('id','asc');
    $this->db->where('category',$id_category);
    $query=$this->db->get('city');
    return $query->result_array();
}
    function get_subcategory_by_id($id){
    $this->db->where('id',$id);
    $query=$this->db->get('tm_subcategory');
    return $query->row_array();
}
    function get_category_by_id($id){
    $this->db->where('id',$id);
    $query=$this->db->get('tm_category');
    return $query->row_array();
}
    function get_kurs(){
    $this->db->where('id',1);
    $query=$this->db->get('kurs');
    return $query->row_array();
}
   function get_subcategory_by_alias($alias, $category){
    $this->db->where('id_category',$category);
    $this->db->where('alias',$alias);
    $query=$this->db->get('tm_subcategory');
    return $query->row_array();
}
    
    function get_category_by_alias($alias){
    $this->db->where('alias',$alias);
    $query=$this->db->get('tm_category');
    return $query->row_array();
}
    
function consult($consult) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@mdlab.kz', 'Администрация саята MdLab');
        $this->email->to('duddeniska@list.ru'); 
        $this->email->subject('Написать Нам');
        $this->email->message('У вас заявка на Написать нам<br>
         <b>Имя: </b>'.$consult['fio'].'<br/>
        <b>Телефон: </b>'.$consult['phone'].'<br/>
        ');	
        $this->email->send();
    }
function sendform($post) {
    $this->load->library('email');
    $config['mailtype'] = 'html';
    $this->email->initialize($config);
    $this->email->from('support@elite-decor-online.ru', 'Форма обратной связи');
    $this->email->to('office.elite-decor@yandex.ru'); 
    $this->email->subject('Форма обратной связи');
    $this->email->message('Данные:<br>
     <b>Имя: </b>'.$post['name'].'<br/>
    <b>Телефон: </b>'.$post['phone'].'<br/>
    <b>Мессенджер: </b>'.$post['messenger'].'<br/>
    <b>Страница: </b>'.$post['page'].'<br/>
    '); 
    $this->email->send();
}
function sendquiz($post) {
    $this->load->library('email');
    $config['mailtype'] = 'html';
    $this->email->initialize($config);
    $this->email->from('support@elite-decor-online.ru', 'Пройден квиз');
    $this->email->to('office.elite-decor@yandex.ru'); 
    $this->email->subject('Пройден квиз');
    $this->email->message('Данные:<br>
     <b>Вопрос #1: </b>'.$post['quiz1'].'<br/>
    <b>Вопрос #2: </b>'.$post['quiz2'].'<br/>
    <b>Вопрос #3: </b>'.$post['quiz3'].'<br/>
    <b>Вопрос #4: </b>'.$post['quiz4'].'<br/>
    '); 
    $this->email->send();
}
function sendformcol($post) {
    $this->load->library('email');
    $config['mailtype'] = 'html';
    $this->email->initialize($config);
    $this->email->from('support@elite-decor-online.ru', 'Заказ показа');
    $this->email->to('office.elite-decor@yandex.ru'); 
    $this->email->subject('Заказ показа');
    $this->email->message('Данные:<br>
     <b>Имя: </b>'.$post['name'].'<br/>
    <b>Телефон: </b>'.$post['phone'].'<br/>
    <b>Мессенджер: </b>'.$post['messenger'].'<br/>
    <b>Страница: </b>'.$post['page'].'<br/>
    '); 
    $this->email->send();
}
function sendformproduct($post) {
    $this->load->library('email');
    $config['mailtype'] = 'html';
    $this->email->initialize($config);
    $this->email->from('support@elite-decor-online.ru', 'Форма заказа в один клик');
    $this->email->to('office.elite-decor@yandex.ru'); 
    $this->email->subject('Форма заказа в один клик');
    $this->email->message('Данные:<br>
     <b>Имя: </b>'.$post['name'].'<br/>
    <b>Телефон: </b>'.$post['phone'].'<br/>
    <b>Страница: </b>'.$post['page'].'<br/>
    '); 
    $this->email->send();
}
function order_master($order_master) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@mdlab.kz', 'Администрация саята VSBC');
        $this->email->to('duddeniska@list.ru'); 
        $this->email->subject('Вызвать замерщика');
        $this->email->message('У вас заявка на Вызвать замерщика<br>
         <b>Имя: </b>'.$order_master['fio'].'<br/>
        <b>Телефон: </b>'.$order_master['phone'].'<br/>
        <b>Адрес: </b>'.$order_master['adress'].'<br/>
        ');	
        $this->email->send();
    }
function calculate($calculate) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@mdlab.kz', 'Администрация саята VSBC');
        $this->email->to('duddeniska@list.ru'); 
        $this->email->subject('Рассчитать по моим размерам');
        $this->email->message('У вас заявка на Рассчитать по моим размерам<br>
         <b>Имя: </b>'.$calculate['fio'].'<br/>
        <b>Телефон: </b>'.$calculate['phone'].'<br/>
        <b>Email: </b>'.$calculate['email'].'<br/>
        ');	
        $this->email->send();
    }
function order($post) {
        $fio = '-';
        $phone = '-';
        $email = '-';
        $question = '-';
        $type_page = '-';
        $type_form = '-';
        if (isset($post['fio'])){
        $fio = $post['fio'];
        }
        if (isset($post['phone'])){
        $phone = $post['phone'];
        }
        if (isset($post['email'])){
        $email = $post['email'];
        }
        if (isset($post['type_page'])){
        $type_page = $post['type_page'];
        }
        if (isset($post['type_form'])){
        $type_form = $post['type_form'];
        }
        if (isset($post['question'])){
        $question = $post['question'];
        }
        
        
        
        
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('sales@deutscher-electric.com', 'Администрация сайта Deutscher-electric.com/');
        $this->email->to('info@deutscher-electric.com'); 
        $this->email->subject('Заявка с сайта');
        $this->email->message('У вас заявка с сайта<br>
        <b>ФИО: </b>'.$fio.'<br/>
        <b>Телефон: </b>'.$phone.'<br/>
        <b>Сообщение: </b>'.$question.'<br/>

        ');	
        $this->email->send();
    }
function sendorder($post) {
        $phone = '-';
        $email = '-';
        $service = '-';
        $comment = '-';
        if(isset($post['phone'])) {
            $phone = $post['phone'];
        }
        if (isset($post['email'])){
            $email = $post['email'];
        }
        if (isset($post['service'])){
            $service = $post['service'];
        }
        if (isset($post['comment'])){
           $comment = $post['comment'];
        }
        $this->load->model("pages_model");
        $userinfo = $this->pages_model->get_user_by_id($post['user_id']);
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('support@elite-decor-online.ru', 'Администрация сайта EliteDecor');
        $this->email->to('office.elite-decor@yandex.ru'); 
        $this->email->subject('Заявка с формы предложите заказ');
        $this->email->message('<b>Получен новый заказ '.date('d.m.Y H:i').'</b><br>
        <b>Сайт: </b> elite-decor-online.ru<br/>
        <b>Специалист: </b>'.$userinfo['email'].'<br/>
        <b>Телефон: </b>'.$phone.'<br/>
        <b>Email: </b>'.$email.'<br/>
        <b>Услуга: </b>'.$service.'<br/>
        <b>Сообщение: </b>'.$comment.'<br/>
        ');	
        $this->email->send();
}
function sendphone($post) {
        $phone = '-';
        $name = '-';
        if(isset($post['phone'])) {
            $phone = $post['phone'];
        }
        if (isset($post['name'])){
            $name = $post['name'];
        }
        $this->load->model("pages_model");
        $userinfo = $this->pages_model->get_user_by_id($post['user_id']);
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('support@elite-decor-online.ru', 'Администрация сайта EliteDecor');
        $this->email->to('office.elite-decor@yandex.ru'); 
        $this->email->subject('Заявка с формы заказать звонок');
        $this->email->message('<b>Запрошен звонок '.date('d.m.Y H:i').'</b><br>
        <b>Сайт: </b> elite-decor-online.ru<br/>
        <b>Специалист: </b>'.$userinfo['email'].'<br/>
        <b>Телефон: </b>'.$phone.'<br/>
        <b>Имя: </b>'.$name.'<br/>
        '); 
        $this->email->send();
}
function order_package($order_package) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@mdlab.kz', 'Администрация саята VSBC');
        $this->email->to('duddeniska@list.ru'); 
        $this->email->subject('Тип пакета');
        $this->email->message('У вас заявка на Тип пакета<br>
         <b>Имя: </b>'.$order_package['fio'].'<br/>
        <b>Телефон: </b>'.$order_package['phone'].'<br/>
        <b>Наименование пакета: </b>'.$order_package['title'].'<br/>
        ');	
        $this->email->send();
    }
  function complekt_for_window($complekt_for_window) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@mdlab.kz', 'Администрация саята VSBC');
        $this->email->to('duddeniska@list.ru'); 
        $this->email->subject('КОМПЛЕКТУЮЩИЕ ДЛЯ ОКОН');
        $this->email->message('У вас заявка на КОМПЛЕКТУЮЩИЕ ДЛЯ ОКОН<br>
         <b>Имя: </b>'.$complekt_for_window['fio'].'<br/>
        <b>Телефон: </b>'.$complekt_for_window['phone'].'<br/>
        <b>Тип комплектации: </b>'.$complekt_for_window['title'].'<br/>
        ');	
        $this->email->send();
    }
    
    function order_call($order_call) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@hhmebel.kz', 'Hhmebel');
        $this->email->to('Householdmebel@inbox.ru'); 
        $this->email->subject('Заказать звонок');
        $this->email->message('У вас заявка на заказать звонок<br>
        <b>Телефон: </b>'.$order_call['phone'].'<br/>
        
        ');	
        $this->email->send();
    }  
      function order_window($order_window) {
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@mdlab.kz', 'Администрация саята VSBC');
        $this->email->to('duddeniska@list.ru'); 
        $this->email->subject('Заказать окна');
        $this->email->message('У вас заявка на Заказать окна<br>
         <b>Имя: </b>'.$order_window['fio'].'<br/>
        <b>Телефон: </b>'.$order_window['phone'].'<br/>
        <b>Что сделать: </b>'.$order_window['what_do'].'<br/>
        ');	
        $this->email->send();
    }
    function question($question) {
        
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@hhmebel.kz', 'Hhmebel');
        $this->email->to('Householdmebel@inbox.ru'); 
        $this->email->subject('Вопрос');
        $this->email->message('У вас новый вопрос<br>
        <b>Фио: </b>'.$question['fio'].'<br/>
        <b>Телефон: </b>'.$question['phone'].'<br/>
        <b>Email: </b>'.$question['email'].'<br/>


        ');	
        $this->email->send();

    }
    
    function message($consult) {
            $this->load->library('email');
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->from('info@feya.kz', 'Администрация сайта Feya');
            $this->email->to('shop_feya@mail.ru');
            $this->email->subject('У вас сообщение с сайта');
            $this->email->message('<b>Имя: </b>'.$consult['name'].'<br/>
             <b>Телефон: </b>'.$consult['phone'].'<br/>
             <b>Message: </b>'.$consult['message'].'<br/>
            ');	
            $this->email->send();
        }
   
   
    
    
function get_user_info_by_email($email){
    $this->db->where('email',$email);
    $query=$this->db->get('users');
    return $query->row_array();
}
function get_user_by_status($status){
    $this->db->where('view',1);
    $this->db->where('status',$status);
    $query=$this->db->get('users');
    return $query->result_array();
}
function get_user_by_id($id){
    $this->db->where('id',$id);
    $query=$this->db->get('users');
    return $query->row_array();
}
function get_gallery(){
        $this->db->order_by('id','desc');
        $this->db->where('view',0);
        $query = $this->db->get('tm_gallery_category');
        return $query->result_array();
    }

function get_product_by_category($id_category){
        $this->db->order_by('id','desc');
        $this->db->where('id_category',$id_category);
        $query = $this->db->get('tm_product');
        return $query->result_array();
    }
function get_product_by_category_by_limit($id_category){
    $this->db->limit(6);
        $this->db->order_by('id','desc');
        $this->db->where('id_category',$id_category);
        $query = $this->db->get('tm_product');
        return $query->result_array();
    }
function get_product_by_id($id){
        $this->db->where('id',$id);
        $query = $this->db->get('tm_product');
        return $query->row_array();
    }
function get_gallery_by_alias($alias){
        $this->db->where('alias',$alias);
        $query = $this->db->get('tm_gallery_category');
        return $query->row_array();
    }
 function get_gallery_img_count($id_gallery_category){
        $query = $this->db->query("
        SELECT COUNT(*)
        FROM tm_gallery_images 
        WHERE id_gallery_category = '$id_gallery_category'
        ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }
function get_gallery_img($id_gallery_category){
        $this->db->where('id_gallery_category',$id_gallery_category);
        $this->db->order_by('id','desc');
        $query = $this->db->get('tm_gallery_images');
        return $query->result_array();
    }
    
function get_city(){

    $this->db->order_by('id','asc');
    $query=$this->db->get('city');
    return $query->result_array();
}
    
    function add_gallery_post($img){
        $this->db->insert('tm_gallery',$img);
    }
    function add_post($post){
    $this->db->insert('tm_product',$post);
} 

function get_service_by_id($id){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_service WHERE id = '$id' and visible=0");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row){
            $count++;
        }
        return $row;
        }
   }


     function get_news_by_id($id){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_news WHERE id = '$id' and visible=0");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row){
            $count++;
        }
        return $row;
        }
   }
   
   
   function get_pr_by_id($id){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_pr WHERE id = '$id'");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row){
            $count++;
        }
        return $row;
        }
   }
   function get_stati_by_id($id){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_stati WHERE id = '$id'");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row){
            $count++;
        }
        return $row;
        }
   }
 function get_news_home(){
       
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d/%m/%Y')  as date1, DATE_FORMAT(date,'%H:%i')  as time, DATE_FORMAT(date,'%d')  as day, DATE_FORMAT(date,'%Y')  as year, DATE_FORMAT(date,'%m')  as month
        FROM tm_news ORDER BY id DESC LIMIT 0,3");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }

function get_stati_home(){
       
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d/%m/%Y')  as date1
        FROM tm_stati ORDER BY date DESC LIMIT 0,4");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }
function get_otzyv_home(){
       
        $query = $this->db->query("
        SELECT * 
        FROM otzyv ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }   
   function get_service(){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_service WHERE visible=0 ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }
 function get_news(){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_news WHERE visible=0 ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }
function get_brand(){
        $query = $this->db->query("
        SELECT *
        FROM brand ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }   
   
   
    function get_pr(){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_pr ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }
function get_stati(){
        $query = $this->db->query("
        SELECT * ,DATE_FORMAT(date,'%d.%m.%Y')  as date1
        FROM tm_stati ORDER BY id DESC");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
   }

   function get_review($id){
    $query = $this->db->query("
    SELECT *, DATE_FORMAT(date,'%d.%m.%Y')  as date1
    FROM rating
    WHERE id_partner = $id
    ");
    $count=0;
    if ($query->num_rows() > 0) {
    foreach ($query->result_array() as $row[$count]){
        $count++;
    }
    return $row;
    }
}

function get_reviews_by_productId($id){
    
        
        $query = $this->db->query("
        SELECT 
           *, DATE_FORMAT(date,'%H:%i %d.%m.%Y')  as date
        FROM 
            tm_reviews r
        
		WHERE 
            r.id_product='$id'
        ORDER BY
            r.id DESC
       
        
 ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
    
}

function get_message_by_user($email){
    
        
        $query = $this->db->query("
        SELECT 
           *, DATE_FORMAT(date,'%H:%i %d.%m.%Y')  as date
        FROM 
            tm_user_order r
        
		WHERE 
            r.email_partner='$email'
        ORDER BY
            r.id DESC
       
        
 ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
    
}

function get_orders_by_user($email){
    
        
        $query = $this->db->query("SELECT * FROM `order_custom` WHERE `email` = '{$email}'");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
    
}

function get_reviews_by_user($email){
    
        
        $query = $this->db->query("
        SELECT 
           *, DATE_FORMAT(date,'%H:%i %d.%m.%Y')  as date
        FROM 
            tm_reviews r
        
		WHERE 
            r.email_partner='$email'
        ORDER BY
            r.id DESC
       
        
 ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
    
}
function get_price_email($email){
    $this->db->where('email',$email);
    $query=$this->db->get('tm_price_email');
    return $query->result_array();
}
function portfolio_view($email){
    $this->db->where('email',$email);
    $query=$this->db->get('tm_portfolio');
    return $query->result_array();
}
  
function get_user_by_login($email){
    $this->db->where('email',$email);
    $query=$this->db->get('users');
    return $query->row_array();
}     
function update_profile($id,$edit) {
       $this->db->where('id',$id);
       $this->db->update('users',$edit);
   }
function add_review($add){
    $this->db->insert('tm_reviews',$add);
} 
function add_price($add){
    $this->db->insert('tm_price_email',$add);
} 
function add_review2($add){
    $this->db->insert('rating',$add);
} 


function get_category(){
    $this->db->order_by('sort','asc');
    $query=$this->db->get('tm_main_category');
    return $query->result_array();
}

function get_category_by_main_category_id($id_main_category){
    $this->db->where('id_main_category',$id_main_category);
    $this->db->where('view',1);
    $query=$this->db->get('tm_category');
    return $query->result_array();
}

function get_subcategory_by_category_id($id_category){
    $this->db->where('id_category',$id_category);
    $query=$this->db->get('tm_subcategory');
    return $query->result_array(); 
}

function get_product_day(){
    $this->db->order_by('id','desc');
    $query=$this->db->get('tm_product_day');
    return $query->row_array(); 
}

function get_pages_by_id($id){
    $this->db->where('id',$id);
    $query=$this->db->get('tm_pages');
    return $query->row_array(); 
}

function get_pages_by_alias($alias){
    $this->db->where('alias',$alias);
    $query=$this->db->get('tm_pages');
    return $query->row_array(); 
}

function get_hot_product(){
    $this->db->where('hot',1);
    $query=$this->db->get('tm_product');
    return $query->result_array(); 
}

function get_discount_product(){
    $this->db->where('discount',1);
    $query=$this->db->get('tm_product');
    return $query->result_array(); 
}





























function get_slider(){
    $query=$this->db->get('slide');
    return $query->result_array();
}


function get_all_action(){
    $this->db->order_by('id','desc');
    $query=$this->db->get('action');
    return $query->result_array();
}

function get_action_byID($id){
    $this->db->where('id',$id);
    $query=$this->db->get('action');
    return $query->row_array();
}



function tour_info($tour){
    $this->db->where('alias',$tour);
    $query=$this->db->get('pages');
    return $query->row_array();
}


function list_tour($tour){
    $this->db->where('category',$tour);
    $query=$this->db->get('tour');
    return $query->result_array();
}


function get_tour_byID($id){
    $this->db->where('id',$id);
    $query=$this->db->get('tour');
    return $query->row_array();
}




function get_current_category($category){
    $this->db->where('alias',$category);
    $query=$this->db->get('category');
    return $query->row_array();
}



function get_products_info($boss_category,$category,$sort,$num,$offset){
    if ($sort=='title') {
        $this->db->order_by('title','asc');
    }
    if ($sort=='price') {
        $this->db->order_by('price','asc');
    }
    if ($sort=='date') {
        $this->db->order_by('id','desc');
    }
    $this->db->where('view','1');
    $this->db->where('boss_category',$boss_category);
    $this->db->where('category',$category);
    $query=$this->db->get('product',$num,$offset);
    return $query->result_array();
}









function get_opticals(){
        $query=$this->db->get('optical');
        return $query->result_array();
    }


    function get_avg_rating($id){
        $query = $this->db->query("
        SELECT AVG(rating) AS ratingAVG, COUNT(1) AS count_view
        FROM rating
        WHERE id_partner = $id
        ");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
    }
function get_optical_byID($id_product){
        $query = $this->db->query("
        SELECT lo.title FROM optical o, tm_product p, list_optical lo WHERE o.id_product = '$id_product' AND p.id = '$id_product' AND o.id_optical = lo.id");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }




function get_radius_byID($id_product){
        $query = $this->db->query("
        SELECT lr.title FROM radius r, tm_product p, list_radius lr WHERE r.id_product = '$id_product' AND p.id = '$id_product' AND r.id_radius = lr.id");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }
    
    
    
    
    function get_optical_power_byID($id_product){
        $query = $this->db->query("
        SELECT lr.title FROM optical_power r, product p, list_optical_power lr WHERE r.id_product = '$id_product' AND p.id = '$id_product' AND r.id_optical_power = lr.id");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }
    
    
    function get_os_grad_byID($id_product){
        $query = $this->db->query("
        SELECT lr.title FROM os_grad r, product p, list_os_grad lr WHERE r.id_product = '$id_product' AND p.id = '$id_product' AND r.id_os_grad = lr.id");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }
    
    
    
    
    function get_color_byID($id_product){
        $query = $this->db->query("
        SELECT lr.title FROM color r, product p, list_color lr WHERE r.id_product = '$id_product' AND p.id = '$id_product' AND r.id_color = lr.id");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    



function get_brend_info($brend){
    $this->db->where('alias',$brend);
    $query=$this->db->get('brend');
    return $query->row_array();
}



function calc_discount($price){

    $email=$this->session->userdata('email');
    
    if (!empty($email)){
        $dicount = $price/100*0;
        $new_price = $price-$dicount;    
    } else {
        $new_price = $price;
    }
    return $new_price;

}



function get_discount(){
    $email=$this->session->userdata('email');
    if (!empty($email)){
        $discount = 5;   
    } else {
        $discount = 0 ;
    }
    return $discount;
}

















function get_product_info($id){
    $this->db->where('id',$id);
    $query=$this->db->get('tm_product');
    return $query->row_array();
}




function get_current_brend($boss_brend, $brend){
     $this->db->where('boss_brend',$boss_brend);
    $this->db->where('alias',$brend);
    $query=$this->db->get('brend');
    return $query->row_array();
}




function get_products_info_brend($boss_brend,$brend,$sort,$start,$num){
    
  //if ($sort=='title') {
  //     $query = $this->db->query("SELECT * FROM product GROUP BY title HAVING brend = '$brend' ORDER_BY id=desc");
  //  }
  //  if ($sort=='price') {
  //      $query = $this->db->query("SELECT * FROM product GROUP BY title HAVING brend = '$brend' ORDER_BY id=desc");
  //  }
  //  if ($sort=='date') {
   //     $query = $this->db->query("SELECT * FROM product GROUP BY title HAVING brend = '$brend' ORDER_BY id=desc");
   // }
   if ($sort=='date') {
        $query = $this->db->query("SELECT * FROM product GROUP BY title HAVING brend = '$brend' and view = 1 and boss_brend = '$boss_brend' ORDER BY id DESC LIMIT $start, $num");
   }
   if ($sort=='price') {
        $query = $this->db->query("SELECT * FROM product GROUP BY title HAVING brend = '$brend' and view = 1 and boss_brend = '$boss_brend' ORDER BY price ASC LIMIT $start, $num");
   }
   if ($sort=='title'){
        $query = $this->db->query("SELECT * FROM product GROUP BY title HAVING brend = '$brend' and view = 1 and boss_brend = '$boss_brend' ORDER BY title ASC LIMIT $start, $num");
   }
        
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
        
    
    
    
    
}


function get_brend(){
    $query=$this->db->get('brend');
    return $query->result_array();
}


function add_order($order){
     $this->db->insert('order_custom',$order); 
}




function add_order_product($order_product){
     $this->db->insert('order_product',$order_product); 
}











function isset_products_category($boss_category, $category){
        $this->db->where('category',$category);
        $this->db->where('boss_category',$boss_category);
        $query=$this->db->get('product');
        return $query->result_array();
    }
    
    
    
    
    function isset_products_brend($boss_brend, $brend){
        $this->db->where('boss_brend',$boss_brend);
        $this->db->where('brend',$brend);
        $query=$this->db->get('product');
        return $query->result_array();
    }
    
    
    
    
    function get_pages_content($alias){
        $this->db->where('alias',$alias);
        
        $query=$this->db->get('pages');
        return $query->row_array();
    }




function get_hot(){
    $this->db->where('hot',1);
        
        $query=$this->db->get('product');
        return $query->result_array();
}


function get_articles(){
    $query=$this->db->get('articles');
    return $query->result_array();
}


function get_article_info($id){
    $this->db->where('id',$id);
        
        $query=$this->db->get('articles');
        return $query->row_array();
}

function update_order($edit,$id) {
        $this->db->where('id',$id);
        $this->db->update('order_custom',$edit);
    }   
function get_order_product($order_id){
    $this->db->where('id_order',$order_id);
     $query=$this->db->get('order_product');
    return $query->result_array();
}

 

function send_order($order,$send_order_product,$total){
   
        $test1 = 'Добрый день! К вам поступил заказ на покупку:<br>
        <b>ФИО: </b>'.$order['fio'].'<br>
        <b>Телефон: </b>'.$order['phone'].'<br>
        <b>Email: </b>'.$order['email'].'<br>
        <b>Адрес доставки: </b>'.$order['address_building'].' '.$order['street'].' Дом '.$order['house'].' Кв '.$order['address_flat'].'<br>
        <b>Примечание к заказу: </b>'.$order['comment'].'<br>
        <hr/>
       ';
        if ($order['face']==1){
           $test1 = $test1.'
           <br/>
           <b>Юридическое лицо</b><br>
           <b>БИН</b> '.$order['iin'].'<br>
           <b>Наименование компании</b> '.$order['title_company'].'<br>
           <b>ФИО диреторка компании</b> '.$order['fio_director'].'<br>
           ';
        }
        $test2 = '<table cellpadding="0" cellspacing="0" width="100%" border="1" class="order_product_table">
        <tr>
        
        <td align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Наименование</b></td>
            
            <td width="100px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Цена</b></td>
            <td width="100px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Количество</b></td>
            <td width="80px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>К оплате</b></td>
        </tr> 
        ';
        $this->load->model("catalog_model");
         foreach ($send_order_product as $item){
            
            $test2 = $test2.'
            <tr>
            <td><p style="font-size: 13px;" align="center">'.$item['name'].'</p></td>
            
            <td width="100px"><p style="font-size: 13px;" align="center">'.$item['price'].'</p></td>
            <td width="100px"><p style="font-size: 13px;" align="center">'.$item['qty'].'</p></td>
            <td width="80px"><p style="font-size: 13px;" align="center">'.$item['price']*$item['qty'].'</p></td>
           </tr>
            ';
         }
         $test2 = $test2.'</table>';
         $test2 = $test2.'<br><p style="font-size:16px; font-weight:bold; text-decoration:underline;">Итого: '.$total.' тг</p>';
         $test2 = $test2.'<p style="font-size:16px; font-weight:bold; text-decoration:underline;">Доставка: '.($order['total'] - $total).' тг</p>';
         $test2 = $test2.'<p style="color:red; font-size:16px; font-weight:bold; text-decoration:underline;">Итоговая сумма: '.$order['total'].' тг</p>';
         $payment_type = "";
         switch($order['payment_type']) {
            case "1":
                $payment_type = "Наличный расчет";
                break;
            case "2":
                $payment_type = "Банковским переводом";
                break;
            case "3":
                $payment_type = "Онлайн оплата картой";
                break;
         }
         $test2 = $test2.'<br><p style="color:green; font-size:16px; font-weight:bold; text-decoration:underline;">Способ оплаты: '.$payment_type.' </p>';
       
        
         
   
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@deutscher-electric.com', 'Администрация сайта Deutscher-electric');
        $this->email->to('sales@deutscher-electric.com');
        $this->email->subject('Заявка на покупку');
        $this->email->message($test1.' '.$test2);	
        
        $this->email->send();
}
function send_copy_order($order,$send_order_product,$total,$order_id){
   
        $test1 = '
        <table cellpadding="0" cellspacing="0" width="100%" border="0">
        <tr>
        <td align="center">
            <img src="https://feya.kz/images/logo.png" alt="">
        </td>
        </tr>
        </table>
        <p>Здравствуйте '.$order['fio'].'!</p>
        <p>Спасибо, что Вы выбрали наш интернет- магазин! Ваш номер заказа № '.$order_id.'</p>
        <br/>
        <table cellpadding="0" cellspacing="0" width="40%" border="1" height="50px">
        <tr>
        <td align="center">
            ВАШ ЗАКАЗ ПОСТУПИЛ В ОБРАБОТКУ
        </td>
        </tr>
        </table>
        <br/>

        <p>
            Описание заявки:
        </p>
        
        
       ';
       
        $test2 = '<table cellpadding="0" cellspacing="0" width="100%" border="1" class="order_product_table">
        <tr>
  
            <td align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Наименование</b></td>
            
            <td width="100px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Цена</b></td>
            <td width="100px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Количество</b></td>
            <td width="80px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>К оплате</b></td>
        </tr> 
        ';
        $this->load->model("catalog_model");
         foreach ($send_order_product as $item){
            
            $test2 = $test2.'
            <tr>
            
            <td><p style="font-size: 13px;" align="center">'.$item['name'].'</p></td>
            
            <td width="100px"><p style="font-size: 13px;" align="center">'.$item['price'].'</p></td>
            <td width="100px"><p style="font-size: 13px;" align="center">'.$item['qty'].'</p></td>
            <td width="80px"><p style="font-size: 13px;" align="center">'.$item['price']*$item['qty'].'</p></td>
           </tr>
            ';
         }
         $test2 = $test2.'</table>';
         $test2 = $test2.'<br><p style="font-size:16px; font-weight:bold; text-decoration:underline;">Итого: '.$total.' тг</p>';
         $test2 = $test2.'<p style="font-size:16px; font-weight:bold; text-decoration:underline;">Доставка: '.($order['total'] - $total).' тг</p>';
         $test2 = $test2.'<p style="color:red; font-size:16px; font-weight:bold; text-decoration:underline;">К оплате: '.$order['total'].' тг</p>';
         $payment_type = "";
         switch($order['payment_type']) {
            case "1":
                $payment_type = "Наличный расчет";
                break;
            case "2":
                $payment_type = "Банковским переводом";
                break;
            case "3":
                $payment_type = "Онлайн оплата картой";
                break;
         }
         $test2 = $test2.'<br><p style="color:green; font-size:16px; font-weight:bold; text-decoration:underline;">Способ оплаты: '.$payment_type.'</p>';
        
         $test2 = $test2.'<br>
         Вы всегда можете задать нам любой вопрос в разделе «Контакты» на <a href="https://elite-decor-online.ru/">Главной странице</a>.
         ';
   
      $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('sales@deutscher-electric.com', 'Администрация сайта Deutscher-electric');
        $this->email->to($order['email']); 
        $this->email->subject('Ваш заказ подтвержден и оформлен');
        $this->email->message($test1.' '.$test2);	
        
        $this->email->send();
}











































function send_order1($order,$send_order_product,$total){
   
        $test1 = 'Добрый день! К вам поступил заказ на покупку:<br>
        <b>ФИО: </b>'.$order['fio'].'<br>
        <b>E-mail: </b>'.$order['email'].'<br>
        <b>Телефон: </b>'.$order['phone'].'<br>
        <b>Адресс: </b>'.$order['address'].'<br>
        <b>Дата: </b>'.$order['date'].'<br>
        <b>Тип оплаты: </b>'.$order['payment'].'<br>
        <b>Тип плательщика: </b>'.$order['type_people'].'<br>
        <b>Тип доставки: </b>'.$order['type_ship'].'<br>
       ';
       if(!empty($order['bin'])){
            $test1 = $test1.'<b>БИН: </b>'.$order['bin'].'<br>';
       }
       if(!empty($order['title'])){
            $test1 = $test1.'<b>Наименование компании: </b>'.$order['title'].'<br>';
       }
       if(!empty($order['iik'])){
            $test1 = $test1.'<b>ИИК: </b>'.$order['iik'].'<br>';
       }
       if(!empty($order['bank'])){
            $test1 = $test1.'<b>Банк: </b>'.$order['bank'].'<br>';
       }
        $test2 = '<table cellpadding="0" cellspacing="0" width="100%" border="1" class="order_product_table">
        <tr>
            <td align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Наименование</b></td>
            
            <td width="100px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Цена</b></td>
            <td width="100px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>Количество</b></td>
            <td width="80px" align="center" style="font-size: 12px; border-bottom: 1px solid #dcdcdc;"><b>К оплате</b></td>
        </tr> 
        ';
         foreach ($send_order_product as $item){
            $test2 = $test2.'
            <tr>
            <td><p style="font-size: 13px;" align="center">'.$item['title'].'</p></td>
            
            <td width="100px"><p style="font-size: 13px;" align="center">'.$item['price'].'</p></td>
            <td width="100px"><p style="font-size: 13px;" align="center">'.$item['count'].'</p></td>
            <td width="80px"><p style="font-size: 13px;" align="center">'.$item['price']*$item['count'].'</p></td>
           </tr>
            ';
         }
         $test2 = $test2.'</table>';
         $test2 = $test2.'<br><p style="font-size:16px; font-weight:bold; text-decoration:underline;">Итоговая сумма: '.$total.' тг.</p>';
       
        
         
   
      $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@technomarket.kz', 'Администрация сайта Technomarket');
        $this->email->to('makgroupmg@gmail.com'); 
        $this->email->subject('Заявка на покупку');
        $this->email->message($test1.' '.$test2);	
        
        $this->email->send();
}



function get_boss_category(){
    
        
        $query=$this->db->get('boss_category');
        return $query->result_array();
}










function get_boss_brend(){
    
        
        $query=$this->db->get('boss_brend');
        return $query->result_array();
}



function get_category_BY_boss_category($boss_category_id){
        $this->db->where('boss_category',$boss_category_id);
        
        $query=$this->db->get('category');
        return $query->result_array();
}




function get_brendy_BY_boss_brend($boss_brend_id){
        $this->db->order_by('title','ASC');
        $this->db->where('boss_brend',$boss_brend_id);
        
        $query=$this->db->get('brend');
        return $query->result_array();
}





function get_type_glasses(){
    $query=$this->db->get('type_glasses');
        return $query->result_array();
}


function get_material_glasses(){
    $query=$this->db->get('material_glasses');
        return $query->result_array();
}



function get_structure_glasses(){
    $query=$this->db->get('structure_glasses');
        return $query->result_array();
}









function get_glasses_sort($condition,$start,$num){
   // $type= $select['type'];
    //if (empty($type)) {
    ///    $type=' ';
    //}
    
    if (empty($condition)){
        $category = 'p.boss_category=2';
    } else {
        $category = ' and p.boss_category=2';
    }
    
    
    
    $query = $this->db->query("
        SELECT * FROM product p WHERE ".$condition.$category." ORDER BY id DESC LIMIT $start, $num");
        $count=0;
        if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row[$count]){
            $count++;
        }
        return $row;
        }
}
function get_banner(){
        $this->db->order_by('id','desc');
        $query = $this->db->get('banner');
        return $query->result_array();
    }
    function get_left_banner(){
        $query = $this->db->get('left_banner');
        return $query->row_array();
    }
function get_slide(){
        $this->db->order_by('id','desc');
        $query = $this->db->get('slide');
        return $query->result_array();
    }


//Получает кол-во постов в определенной категории и подкатегории
function get_posts_count($args) {
    $default = array(
        'category' => 0,
        'subcategory' => 0,
        'price_from' => '',
        'price_to' => '',
        'photo_only' => false,
        'hot' => ''
    );
    foreach($args as $k => $v) {
        $default[$k] = $v;
    }
  
    //Популярные
    if($default['hot']===true)
      $where = 'p.hot = 1';
    elseif($default['hot']===false)
      $where = 'p.hot = 0';
    else
      $where = '1=1';
    //Категории
    if($default['category'] != 0) {
      $where .= " AND p.main_category = {$default['category']}";
      if($default['subcategory'] != 0) {
        $where .= " AND p.category = {$default['subcategory']}";
      }
    }
    //Цена
    if($default['price_from']!=='') {
      if($default['price_to']!=='')
        $where .= " AND (p.price BETWEEN {$default['price_from']} AND {$default['price_to']})";
      else
        $where .= " AND p.price >= {$default['price_from']}";
    } elseif($default['price_to']!=='')
      $where .= " AND p.price <= {$default['price_to']}";
    //С фото
    $having = '';
    if($default['photo_only'])
      $having = "HAVING photo_count > 0";
    
    $query = $this->db->query("
      SELECT COUNT(*) as count
      FROM (
        SELECT
        COUNT(g.id) as photo_count
        FROM tm_product p
        LEFT OUTER JOIN tm_gallery g ON g.id_post = p.id
        WHERE $where
        GROUP BY p.id
        $having
      ) s
    ");
    return $query->row_array()['count'];
}
    
//Получает посты с превью (вся нужная информация для отображения в списке объявлений)
function get_posts_full($args) {
    $default = array(
        'category' => 0,
        'subcategory' => 0,
        'price_from' => '',
        'price_to' => '',
        'photo_only' => false,
        'sort' => 'new',
        'hot' => '',
        'page' => 1,
        'posts_per_page' => 0,
        'short_text' => false
    );
    foreach($args as $k => $v) {
        $default[$k] = $v;
    }

    //Популярные
    if($default['hot']===true)
      $where = 'p.hot = 1';
    elseif($default['hot']===false)
      $where = 'p.hot = 0';
    else
      $where = '1=1';
    //Категории
    if($default['category'] != 0) {
      $where .= " AND p.main_category = {$default['category']}";
      if($default['subcategory'] != 0) {
        $where .= " AND p.category = {$default['subcategory']}";
      }
    }
    //Цена
    if($default['price_from']!=='') {
      if($default['price_to']!=='')
        $where .= " AND (p.price BETWEEN {$default['price_from']} AND {$default['price_to']})";
      else
        $where .= " AND p.price >= {$default['price_from']}";
    } elseif($default['price_to']!=='')
      $where .= " AND p.price <= {$default['price_to']}";
    //С фото
    $having = '';
    if($default['photo_only'])
      $having = "HAVING photo_count > 0";
    //Сортировка
    $orderby = '';
    if($default['sort'] == 'price-asc')
      $orderby.= 'p.price ASC';
    elseif($default['sort'] == 'price-desc')
      $orderby.= 'p.price DESC';
    else
      $orderby.= 'p.date DESC';
    $limit = $default['posts_per_page'] ? "LIMIT ".($default['page']-1)*$default['posts_per_page'].",{$default['posts_per_page']}" : '';
    
    $query = $this->db->query("
      SELECT p.id, p.date, p.title, p.price, p.count, p.city, p.text, p.fio, p.phone, p.email,
      o.thumb_174, COUNT(g.id) as photo_count,
      c.title as city_title
      FROM tm_product p
      LEFT OUTER JOIN tm_gallery g ON g.id_post = p.id
      LEFT OUTER JOIN tm_gallery o ON o.id_post = p.id AND o.cover = 1
      LEFT OUTER JOIN city c ON c.id = p.city
      WHERE $where
      GROUP BY p.id
      $having
      ORDER BY $orderby
      $limit
    ");
    $result = $query->result_array();
    //Сокращенный текст объявления
    if($default['short_text']) {
      $this->load->helper('text');
      foreach($result as &$v) {
        $v['text'] = character_limiter($v['text'], 220);
      }
    }

    return $result;
}


}