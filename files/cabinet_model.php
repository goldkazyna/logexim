<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Cabinet_model extends CI_Model {
     function update_password($restore_code,$user) {
        $this->db->where('restore_code',$restore_code);
        $this->db->update('users',$user);
    }
   function send_recovery_password($info_user,$users){
    $test1 = '
    <div style="background:#2c304b;padding:30px;">
	<div style="background:#fff;padding: 15px 20px;width: 550px;border: 1px solid #e3e3e5;border-radius:3px;margin: 0 auto;font: normal 13px/19px Verdana;box-shadow: 0 3px 7px rgba(0,0,0,.1);">
		<h2 style="font:normal 21px/48px Arial;color: #222;padding: 0 0 0 68px;background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAABKVBMVEVHcEyBo6GBo6GBoqCBo6CCoqCCoZ+Co6GBoZ+LoqJ/oKCCo6CAp52Co6GAo5+CpKCBoaGAoZ+BoqCDoqGCo6GBoqGBoqCCo6GAo6CCo6KAoqGCo6GCoaGAoZ+Co6GCoaGCoqCBop9/oJ6CoaF/n5+Co5+CoqGCo6Hx69GAoJ6BoqDw6tDv6c9/n53m48pSXXPp5czDzLvr582mt618nJq2wbKRq6SWrqjJz73i4Mlwi428x7dcb3mIpaLd2cJ3lJGJjpRSXXTRz7lqhIWrvLCeq6B0kY+xvrB5mJZUYnTQ1MGns6awtKqSp595j4ydsKaWoJRecXpmgIJnf4XW2MOEpKHM0r9uioqIo59SX3KsrqiFmZGkrqDDw7CnraNXaXHX2sXHyLTX0rwvKqOYAAAAJ3RSTlMAZPjW9Y3zE/0FF24bk80jUO7palTJ3rpadPH9SvZ7KYez9rQQxc8DeY0YAAACb0lEQVR4Xo3UZ0PqMBiG4QgtbdkiDkQ9LnyStsw93Hvvcfb6/z/iNBI4panA9fl+Y2JCiSS6EJufm9YD03PzsYUwGUOLxDEkHtFG5NFYAJJ1NUr8GSsB+FIyhl//KYgPbSXkPq1ghFDS28/oGCk75ekxwg64oYn0qPUvX7vHLwzZpOu8I/Zv/qaU2vvnwOLg5EZwTE/3K3cA4oYYWBnbU3rGAGTE/QbG97RbAxAKEy42QU9/8gGovNcCE/Rft004FP4SI5P0Bw8M3IwzEJ+kL27nweUIiU7U9wegkYUJ+l+PT3voSZPY2L548WaiTyXzY/vtc4aBDTLnSU3vfg6e4BIk0/ivUS2XW+Vq09V/uSgema4kRXQMlKnFo4LtWv+xc1qDyzIJQGBtKrjWfzjHMGWwJdN299e99Y/y8EgNDt2mbuK8Yvsuwf6/tSv3xYOLI3itiYvbqXj7ym2n85aHlyqeRlOcwKJ9hRsGH0kSBld/rwvP7YqYpFYdPnRNPO8yT46BZrXQn6jnIcv1f0At3puOz1clsa3rGmT8a6atAzizacF8d3MoBq4YJKFN4lABVCl9hsmxH70B+xtkq4SLKkDj0mo3TS4v/kJpDxLxmSEZAF27dLzDB+5L9N13BkmE9BhbAHu12tUGY/e7vf7wFJJZgwiJEF4al3bh759Dsf6u9IrEx1hIZp2Jlm1Zlljfp9eXiMsUgFq9RfmTquye7Jn+V+CeyMK5tbuT+tnJ7SmDROf9kOQiHHnGmAnZ4hKRJOL40GyC+DAyIfgKRQziL6wqPvlqmHxMm8nBTc9NbZIxtLS6EUwtK6ngmprUiNc/tS4oi11zJywAAAAASUVORK5CYII=) no-repeat 0 50%;margin: 0;">
			Восстановление пароля
		</h2>

		<div style="padding: 15px 0;"> 
			Уважаемый <b>'.$info_user['company_name'].'</b>. Вы отправили запрос на восстановления пароля на сайте Logeximexpress. Чтобы получить новый пароль, перейдите по ссылке ниже.:
		</div>
		<a href="'.base_url().'cabinet/newpassword/'.$users['restore_code'].'" style="width: 400px;margin:0 auto;display: block;background: #0074c5;color: #fff;font-weight:bold; line-height: 44px;text-align: center;text-transform: uppercase;text-decoration: none;border-radius: 3px;text-shadow: 0 1px 3px rgba(0,0,0,.35);border: 1px solid #388E3C;box-shadow: inset 0 1px rgba(255,255,255,.4);">
			Восстановить пароль
		</a>
		<div style="padding: 15px 0;"> 
			Если вы не делали запрос на получение пароля, то просто удалите это письмо. Ваш пароль хранится в надежном месте и недоступен для посторонних лиц.
		</div>
		
	</div>
</div>
';
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('support@logeximexpress.kz', 'Поддержка Logeximexpress');
        $this->email->to($info_user['email']); 
        $this->email->subject('Восстановление пароля');
        $this->email->message($test1);	
        $this->email->send();
}
    function update_user_info($bin, $user) {
        $this->db->where('bin',$bin);
        $this->db->update('users',$user);
    }
    
    function get_users_by_email($bin){
        $this->db->where('bin',$bin);
        
        $query = $this->db->get('users');
        return $query->row_array();
    }
    
   function check_user($bin){
        $this->db->where('bin',$bin);
        $query = $this->db->get('users');
        if ($query->num_rows() >0) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
public function get_invoices_by_date($start_date, $end_date, $user_id) {
    $this->db->where('date >=', $start_date);
    $this->db->where('date <=', $end_date);
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('invoices');
    return $query->result_array();
}
public function get_invoices_by_date_range($start_date, $end_date, $user_id) {
    $this->db->where('date >=', $start_date);
    $this->db->where('date <=', $end_date);
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('invoices');
    return $query->result_array();
}

public function insert_invoice($invoice_data)
{
    $this->db->insert('invoices', $invoice_data);
}
public function get_invoice_by_id($invoice_id)
{
    $this->db->where('id', $invoice_id);
    $query = $this->db->get('invoices');
    
    if ($query->num_rows() > 0) {
        return $query->row_array();
    } else {
        return false;
    }
}

public function get_last_invoice_number()
{
    $this->db->select('invoice_number');
    $this->db->from('invoices');
    $this->db->order_by('invoice_number', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        $row = $query->row();
        return $row->invoice_number;
    } else {
        return null; // Если накладных еще нет, вернем null
    }
}

public function get_all_invoices($user_id)
{
    // Получаем все накладные для пользователя с заданным user_id
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('invoices');
    
    return $query->result_array();
}

     public function get_user_by_bin($bin) {
        $this->db->where('bin', $bin);
        $query = $this->db->get('users');
        return $query->row_array();
    }
    public function update_user_by_bin($bin, $user_data) {
        // Обновляем данные в таблице users по bin
        $this->db->where('bin', $bin);
        $this->db->update('users', $user_data);
    }
public function get_recipient_templates($user_id)
{
    // Получаем все шаблоны отправителей для пользователя с заданным user_id
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('recipient_templates');

    // Возвращаем результат запроса в виде массива
    return $query->result_array();
}

// Метод для сохранения шаблона отправителя
    public function save_template($data)
    {
        return $this->db->insert('recipient_templates', $data);
    }
public function delete_template($template_id)
{
    // Удаление шаблона по ID
    $this->db->where('id', $template_id);
    return $this->db->delete('recipient_templates');
}    
public function get_templates_by_user($user_id)
{
    // Запрос для получения шаблонов отправителей текущего пользователя
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('recipient_templates');
    return $query->result_array();
}
public function get_template_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('recipient_templates');
        return $query->row_array();
    }

    // Метод для обновления шаблона
    public function update_template($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('recipient_templates', $data);
    }
    function delete_portfolio($id,$email) {
        $this->db->where('id',$id);
        $this->db->where('email',$email);
        $query = $this->db->get('tm_portfolio');
        if($query->num_rows() > 0) {
            $this->db->where('id',$id);
            $this->db->where('email',$email);
            $this->db->delete('tm_portfolio');
            return true;
        } else {
            return false;
        }
    }
    
    function add_user($user){
        $this->db->insert('users',$user);     
    }
    
    
    
    function check_registration_user($email){
        $this->db->where('email',$email);
        $query = $this->db->get('users');
        if ($query->num_rows() >0) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
    function update_about_me_executor($executor,$id_userExecutor) {
        
        $this->db->where('id_userExecutor',$id_userExecutor);
        $this->db->update('infoExecutor',$executor);
    }
    function update_about_me($customer,$id_userCustomer) {
        
        $this->db->where('id_userCustomer',$id_userCustomer);
        $this->db->update('infoCustomer',$customer);
    }
    
    function register_users($users){
        $this->db->insert('users',$users);
    }
   
    function register_users_customer($users){
        $this->db->insert('userCustomer',$users);     
    }
    function add_infoCustomer($customer){
        $this->db->insert('infoCustomer',$customer);     
    }
   function add_infoExecutor($executor){
        $this->db->insert('infoExecutor',$executor);     
    }

    function send_new_user($user) {
        $email = $user['email'];
        $fio = $user['fio'];
        $name = $user['name'];
        $phone = $user['phone'];
        $status = $user['status'];
        if($status == 1) {
            $status = 'Специалист';
        } elseif($status == 0) {
            $status = 'Покупатель';
        }
        $city = $user['city'];
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('support@elite-decor-online.ru', 'Администрация сайта EliteDecor');
        $this->email->to('office.elite-decor@yandex.ru'); 
        $this->email->subject('Зарегистрировался новый пользователь');
        $this->email->message('У вас новый пользователь<br>
        <b>Телефон: </b>'.$phone.'<br/>
        <b>Email: </b>'.$email.'<br/>
        <b>Имя: </b>'.$name.'<br/>
        <b>Фамилия: </b>'.$fio.'<br/>
        <b>Покупатель/Специалист: </b>'.$status.'<br/>
        <b>Город: </b>'.$city.'<br/>
        '); 
        $this->email->send();
    }
   function send_email_and_password($email,$password) {
        
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@website.com.kz', 'Администрация сайта EasyMoney');
        $this->email->to($email); 
        $this->email->subject('Автоматическая регистрация');
        $this->email->message('Добрый день! <br>
        Вы были автоматически зарегестрированы на сайте EasyMoney.<br/>
        Логин: '.$email.'<br/>
        Пароль: '.$password.'<br/>
        
        Теперь вы можете <a href="'.base_url().'cabinet/auth">Войти</a> в личный кабинет.
        
        <hr><br>С уважением Администрация сайта EasyMoney.');	

        $this->email->send();

    }
   
    function send_active_code($name,$email,$activate){
    $test1 = '
    
    <table cellpadding="0" cellspacing="0" width="100%" border="0">
        <tr>
            <td align="center">
                <img src="https://elite-decor-online.ru/images/logo.png" alt="">
            </td>
        </tr>
    </table>
    <br/>

    <br/>
    <p>Здравствуйте '.$name.'!</p>
    <p>Спасибо, что Вы выбрали наш интернет - магазин!</p>
    <br/>
    <p>
    Вы успешно зарегистрировались на сайте. <br/>
    Пожалуйста перейдите по <a href="'.base_url().'cabinet/activate/'.$activate.'">ссылке для активации учетной записи.</a>
    </p>
    <br>
    <p>
    Вы всегда можете задать нам любой вопрос в разделе «Контакты» на <a href="https://elite-decor-online.ru/kontakty">Главной странице</a>.
    </p>
    ';
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('support@elite-decor-online.ru', 'Администрация сайта Elite Decor');
        $this->email->to($email); 
        $this->email->subject('Успешная регистрация');
        $this->email->message($test1);	
        $this->email->send();
}
    
    function send_password($email,$password) {
        
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@website.com.kz', 'Администрация сайта Аренда');
        $this->email->to($email); 
        $this->email->subject('Автоматическая регистрация на сайте');
        $this->email->message('Добрый день! <br>
        Вы подали объявление на портале Аренда и прокат. Вы были автоматически зарегестрированы, теперь вы можете войти в личный кабинет для управлениями вашими объявлениями.<br>
        <b>Логин:</b> '.$email.'<br/>
        <b>Пароль:</b> '.$password.'<br/> <br/>
        Перейдите по <a href="'.base_url().'cabinet/registration">ссылке</a> для входа в личный кабинет.
        <hr><br>С уважением Администрация сайта Аренда.');	

        $this->email->send();

    }
    
    
    
    function send_active_code_customer($email,$active_code) {
        
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('info@website.com.kz', 'Администрация сайта EasyMoney');
        $this->email->to($email); 
        $this->email->subject('Активация учетной записи');
        $this->email->message('Добрый день! <br>
        Вы зарегестрировались на сайте Easy money как Заказчик. Пожалуйста потвердите регистацию.<br>
         
        Перейдите по ссылке для потверждения.'.base_url().'cabinet/active_account/'.$active_code.
        '<hr><br>С уважением Администрация сайта EasyMoney.');	

        $this->email->send();

    }
    
    
    function check_registration_users($email){
        $this->db->where('email',$email);
        $query = $this->db->get('users');
        if ($query->num_rows() >0) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
    function activate_user($activatecode) {
        $this->db->where('activate_code',$activatecode);
		//$this->db->where('id',1);
		$data=['activate'=>1];
        $this->db->update('users',$data);
    }
    function check_user_code($actcode){
        $this->db->where('activate_code',$actcode);
        $query = $this->db->get('users');
        if ($query->num_rows() >0) {
            //return TRUE;
			return $query->row_array();
        } else {
            return FALSE;
        }
        
    }
    
    function check_registration_user_customer($email){
        $this->db->where('email',$email);
        $query = $this->db->get('userCustomer');
        if ($query->num_rows() >0) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
    
    
    function auth_user($bin,$password){
        $this->db->where('activate',1);
        $this->db->where('bin',$bin);
        $this->db->where('password',$password);
        $query = $this->db->get('users');
        if ($query->num_rows() >0) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
    
    
    function auth_user_customer($email,$password){
        $this->db->where('email',$email);
        $this->db->where('password',$password);
        $query = $this->db->get('userCustomer');
        if ($query->num_rows() >0) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
    
    
    
    
    
    
    
    
    
    

}