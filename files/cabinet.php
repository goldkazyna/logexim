<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cabinet extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // Подключаем автозагрузчик библиотеки PhpSpreadsheet


    }
    function getRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}
    function get_list_subcategory(){
        $db1 = $this->load->database();
            $id_category = @intval($_GET['id_category']);

            $regs=mysql_query("SELECT *  FROM f_industries_of_your_interest_subcategory WHERE industries_of_your_interest_category=$id_category");
             
            if ($regs) {
                $num = mysql_num_rows($regs);     
                $i = 0;
                while ($i < $num) {
                   $subcategory[$i] = mysql_fetch_assoc($regs);  
                   $i++;
                }    
                $result = array('subcategory'=>$subcategory); 
            }
            else {
                $result = array('type'=>'error');
            }
            print json_encode($result);

    }
    
    function activate($actcode){
		//$actcode='ce00182bd8e66800879d5296a30baf2abd2d3a6c';
		$this->load->model('cabinet_model');
		$data['result'] = "Не верные данные.";
        $user = $this->cabinet_model->check_user_code($actcode);
		if (!empty($actcode) && $user=$this->cabinet_model->check_user_code($actcode)){
			//print_r($user);
			$data['result'] = "Активация успешно выполнена.";
        
            $ses = array("email" => $user['email']);
            $this->session->set_userdata($ses);
			$this->cabinet_model->activate_user($actcode);
            redirect(base_url().'cabinet');    
		}



        $this->load->view('templates/cabinet/activate_view',$data);

    }
    public function reports() {
        $this->load->model('cabinet_model');
        $bin = $this->session->userdata('bin');
        if (empty($bin)) {
            redirect(base_url());
        }
        $user = $this->cabinet_model->get_user_by_bin($bin);
        // Получение данных из формы фильтрации по датам
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        // Проверка на наличие введенных дат
        if (!empty($start_date) && !empty($end_date)) {
            // Загружаем модель для работы с накладными
  
            
            // Получаем накладные за выбранный диапазон дат
            $data['invoices'] = $this->cabinet_model->get_invoices_by_date($start_date, $end_date, $user['id']);
        } else {
            // Если даты не введены, выводим пустую таблицу
            $data['invoices'] = [];
        }
    
        // Заголовок страницы отчета
        $data['title'] = 'Отчеты по накладным';
            // Передаем даты в представление
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        // Отображаем представление с отчетами
        $this->load->view('templates/cabinet/reports/reports_view', $data);
    }

public function export_invoices() {
    $bin = $this->session->userdata('bin');
    if (empty($bin)) {
            redirect(base_url());
        }
    require_once(APPPATH . 'libraries/PHPExcel-1.8/Classes/PHPExcel.php');
    $this->load->model('cabinet_model');
    $user = $this->cabinet_model->get_user_by_bin($bin);
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);

    // Установка стилей для заголовков (синий фон, белый шрифт)
    $headerStyleArray = array(
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => 'FFFFFF'),
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '005795'),
        ),
    );

    // Установка заголовков столбцов
    $headers = [
        'A1' => 'ID', 'B1' => 'Номер накладной', 'C1' => 'Статус', 'D1' => 'Дата',
        'E1' => 'Отправитель', 'F1' => 'Телефон отправителя', 'G1' => 'Компания отправителя',
        'H1' => 'Город отправителя', 'I1' => 'Страна отправителя', 'J1' => 'Регион отправителя',
        'K1' => 'Район отправителя', 'L1' => 'Адрес отправителя', 'M1' => 'Получатель',
        'N1' => 'Телефон получателя', 'O1' => 'Компания получателя', 'P1' => 'Город получателя',
        'Q1' => 'Страна получателя', 'R1' => 'Регион получателя', 'S1' => 'Район получателя',
        'T1' => 'Адрес получателя', 'U1' => 'Описание', 'V1' => 'Количество мест',
        'W1' => 'Вес', 'X1' => 'Объёмный вес', 'Y1' => 'Хрупкий груз', 'Z1' => 'Объявленная ценность',
        'AA1' => 'Оплата', 'AB1' => 'Оплата отправителем', 'AC1' => 'Оплата получателем',
        'AD1' => 'Оплата по договору', 'AE1' => 'Оплата по счету', 'AF1' => 'Оплата наличными',
        'AG1' => 'Планируемая дата доставки', 'AH1' => 'Фактическая дата доставки', 'AI1' => 'Особые инструкции'
    ];

    foreach ($headers as $cell => $header) {
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $header);
    }

    // Применение стиля к первой строке
    $objPHPExcel->getActiveSheet()->getStyle('A1:AI1')->applyFromArray($headerStyleArray);

    // Получение данных
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $invoices = $this->cabinet_model->get_invoices_by_date_range($start_date, $end_date, $user['id']);

    // Заполнение данными
    $row = 2;
    foreach ($invoices as $invoice) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $invoice['id']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $invoice['invoice_number']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $invoice['status']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, date('d.m.Y', strtotime($invoice['date'])));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $invoice['sender_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $invoice['sender_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $invoice['sender_company']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $invoice['sender_city']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $invoice['sender_country']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $invoice['sender_region']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $invoice['sender_district']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $invoice['sender_address']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $invoice['recipient_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $invoice['recipient_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $invoice['recipient_company']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $invoice['recipient_city']);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $invoice['recipient_country']);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $invoice['recipient_region']);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $invoice['recipient_district']);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $row, $invoice['recipient_address']);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $invoice['description']);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $invoice['quantity']);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $row, $invoice['weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('X' . $row, $invoice['volume_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $invoice['fragile']);
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $invoice['declared_value']);
        if ($invoice['payment']!=0){
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row, $invoice['payment']);
        }
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $row, $invoice['payment_sender']);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $row, $invoice['payment_recipient']);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $row, $invoice['payment_contract']);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $row, $invoice['payment_invoice']);
        $objPHPExcel->getActiveSheet()->setCellValue('AF' . $row, $invoice['payment_cash']);
        
        if ($invoice['plan_date']!='0000-00-00'){
            $objPHPExcel->getActiveSheet()->setCellValue('AG' . $row, date('d.m.Y', strtotime($invoice['plan_date'])));
        }
        if ($invoice['fact_date']!='0000-00-00'){
            $objPHPExcel->getActiveSheet()->setCellValue('AH' . $row, date('d.m.Y', strtotime($invoice['fact_date'])));    
        }
        $objPHPExcel->getActiveSheet()->setCellValue('AI' . $row, $invoice['special']);
        $row++;
    }

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(6);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(21);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(19);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(19);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(19);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(22);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(19);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(21);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(21);
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(7);
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(13);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(22);


    // Форматируем даты для названия файла
    $formatted_start_date = date('d-m-Y', strtotime($start_date));
    $formatted_end_date = date('d-m-Y', strtotime($end_date));
    $filename = "nakladnye_{$formatted_start_date}_-_$formatted_end_date.xlsx";

    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}






// Функция для получения статуса накладной в текстовом виде
private function get_invoice_status($status) {
    switch ($status) {
        case 0:
            return 'Заявка создана';
        case 1:
            return 'Принята в работу';
        case 2:
            return 'Отправлено';
        case 3:
            return 'Исполнена';
        default:
            return 'Неизвестный статус';
    }
}
    
    
    
    
    private $telegram_api_url = "https://api.telegram.org/bot7328907732:AAGhdEnWZfpOp22hczseO1TjiKoB19pZB4g/";
    function registration(){
        $data['error']="";
        $data['d'] = "Регистрация";
        $data['title'] = "Регистрация";
        $data['title_seo'] = "Регистрация";
        $this->load->helper('date');
        $this->load->helper('string');
        $this->load->model('cabinet_model');
        if($this->input->post('registration')){
            if ($this->input->post('password') != $this->input->post('re-password')){
                $data['error']['password']="Пароли не совпадают";
                $this->load->view('templates/cabinet/registration_view',$data);
            } else {
                $datestring = "%Y-%m-%d %H:%i:%a";
                $time = time();
                $users['bin']=$this->input->post('bin');
                $users['password'] = sha1(md5($this->input->post('password')));
                $users['date'] = mdate($datestring, $time);
                $users['ip'] = $this->input->ip_address();
                $users['activate'] = 0;
                $users['activate_code']=random_string('numeric',22);
                $users['company_name']=$this->input->post('company_name');
                $users['phone']=$this->input->post('phone');
                $users['email']=$this->input->post('email');
                $users['director_name']=$this->input->post('director_name');
                $users['address']=$this->input->post('address');
                if (!$this->cabinet_model->check_user($users['bin'])){
                    $this->cabinet_model->add_user($users);
                    $this->send_telegram_message(-1002187416306, "У вас новая регистрация, БИН: " . $users['bin']);
                    //$this->cabinet_model->send_new_user($users);
                    //$this->cabinet_model->send_active_code($users['name'],$users['email'],$users['activate_code']);
                    redirect(base_url().'cabinet/success');    
                } else {
                        $data['error']['bin'] = "Этот БИН уже зарегистрирован";
                        $this->load->view('templates/cabinet/registration_view',$data);
                    }
            }
        } else {
            $this->load->view('templates/cabinet/registration_view',$data);
        }
    }
    private $allowed_users = array(
            381314146,  // Пример chat_id пользователя 1
            -1002187416306,  // Пример chat_id пользователя 2
            // Добавьте сюда другие chat_id пользователей, которым разрешён доступ
        );
    private function is_user_allowed($chat_id) {
        return in_array($chat_id, $this->allowed_users);
    }
    private function send_telegram_message($chat_id, $message) {
        // Проверка, разрешён ли пользователь
        if (!$this->is_user_allowed($chat_id)) {
            echo "Этот пользователь не имеет доступа.";
            return; // Прерываем выполнение, если пользователь не разрешён
        }

        // URL для отправки сообщения
        $url = "https://api.telegram.org/bot7328907732:AAGhdEnWZfpOp22hczseO1TjiKoB19pZB4g/sendMessage";
        
        // Параметры запроса
        $post_fields = array(
            'chat_id' => $chat_id,
            'text'    => $message
        );

        // Инициализация cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Отключаем проверку SSL для простоты

        // Выполнение запроса
        $result = curl_exec($ch);

        // Проверка на ошибки
        if ($result === false) {
            echo 'Ошибка cURL: ' . curl_error($ch);
        }

        // Закрытие cURL
        curl_close($ch);
    }
    function auth(){
        
         $data['']='';
         $data['error']='';
         $data['d'] = "Авторизация";
         $data['k'] = "Easy Авторизация";
         $this->load->model('cabinet_model');
         if($this->input->post('auth')){
            $bin=$this->input->post('bin');
            $password=$this->input->post('password');
            $password=sha1(md5($password));
                  if ($this->cabinet_model->auth_user($bin,$password)) {
                    $this->session->unset_userdata('bin');
                    $ses = array(
                       'bin'  => $bin
                    );
                    $this->session->set_userdata($ses);
                    redirect(base_url().'cabinet');
                } else {
                    $data['error'] = "Данные введены некорректно";
                    $this->load->view('templates/cabinet/auth_view',$data);
                }
         } else {
         $this->load->view('templates/cabinet/auth_view',$data);
         }
    }
    
    
    function index(){

       $bin=$this->session->userdata('bin');
        if (empty($bin)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        //$data['user'] = $this->pages_model->get_user_by_login($email);
        
        $this->load->view('templates/cabinet/cabinet_view',$data);
      }
    }
    
    
public function invoices()
{
    // Проверка авторизации пользователя по bin
    $bin = $this->session->userdata('bin');
    if (empty($bin)) {
        redirect(base_url());
    }

    // Загружаем модель
    $this->load->model('cabinet_model');

    // Получаем данные пользователя по bin
    $user = $this->cabinet_model->get_user_by_bin($bin);

    if (!$user) {
        show_error('Пользователь не найден.');
    }

    // Получаем список всех накладных для данного пользователя
    $data['invoices'] = $this->cabinet_model->get_all_invoices($user['id']);

    // Передаем данные в представление и загружаем его
    $data['d'] = "Список накладных";
    $data['title'] = "Мои накладные";
    $this->load->view('templates/cabinet/invoices/invoices_list_view', $data);
}


    
  public function create_invoice() {
        // Проверяем авторизацию пользователя
        $bin = $this->session->userdata('bin');
        
        if (empty($bin)) {
            redirect(base_url());
        }

        // Загружаем модель для работы с пользователями
        $this->load->model('cabinet_model');

        // Получаем данные пользователя по bin
        $data['user'] = $this->cabinet_model->get_user_by_bin($bin);
        $data['templates'] = $this->cabinet_model->get_recipient_templates($data['user']['id']);
        // Отображаем страницу настройки профиля
        $data['d'] = "Накладные";
        $data['title'] = "Накладные";

        $this->load->view('templates/cabinet/invoices/create_invoice_view', $data);
    }  
public function save_invoices()
{
    // Проверка авторизации пользователя
    $bin = $this->session->userdata('bin');
    if (empty($bin)) {
        redirect(base_url());
    }

    // Загружаем модель
    $this->load->model('cabinet_model');

    // Получаем данные пользователя
    $user = $this->cabinet_model->get_user_by_bin($bin);
    $user_id = $user['id'];

    // Генерация номера накладной
    $last_invoice_number = $this->cabinet_model->get_last_invoice_number();
    $new_invoice_number = $last_invoice_number ? $last_invoice_number + 1 : 900001;

    // Получаем данные из POST-запроса
    $invoice_data = [
        'user_id' => $user_id,
        'status' => 0,
        'invoice_number' => $new_invoice_number, // Присваиваем новый номер накладной
        'date' => $this->input->post('date'),
        'sender_name' => $this->input->post('sender_name'),
        'sender_phone' => $this->input->post('sender_phone'),
        'sender_company' => $this->input->post('sender_company'),
        'sender_city' => $this->input->post('sender_city'),
        'sender_country' => $this->input->post('sender_country'),
        'sender_region' => $this->input->post('sender_region'),
        'sender_district' => $this->input->post('sender_district'),
        'sender_address' => $this->input->post('sender_address'),
        'recipient_name' => $this->input->post('recipient_name'),
        'recipient_phone' => $this->input->post('recipient_phone'),
        'recipient_company' => $this->input->post('recipient_company'),
        'recipient_city' => $this->input->post('recipient_city'),
        'recipient_country' => $this->input->post('recipient_country'),
        'recipient_region' => $this->input->post('recipient_region'),
        'recipient_district' => $this->input->post('recipient_district'),
        'recipient_address' => $this->input->post('recipient_address'),
        'description' => $this->input->post('description'),
        'quantity' => $this->input->post('quantity'),
        'weight' => $this->input->post('weight'),
        'volume_weight' => $this->input->post('volume_weight'),
        'fragile' => $this->input->post('fragile') ? 1 : 0, // Хрупкий груз - да или нет
        'declared_value' => $this->input->post('declared_value'),
        'payment_sender' => $this->input->post('payment_sender') ? 1 : 0,
        'payment_recipient' => $this->input->post('payment_recipient') ? 1 : 0,
        'payment_contract' => $this->input->post('payment_contract') ? 1 : 0,
        'payment_invoice' => $this->input->post('payment_invoice') ? 1 : 0,
        'payment_cash' => $this->input->post('payment_cash') ? 1 : 0,
        'special' => $this->input->post('special'),
    ];

    // Сохраняем накладную через модель
    $this->cabinet_model->insert_invoice($invoice_data);
    $this->send_telegram_message(-1002187416306, "У вас новая накладная, №: " . $new_invoice_number);
    // Устанавливаем сообщение об успешном сохранении
    $this->session->set_flashdata('success_message', 'Накладная успешно добавлена!');

    // Редирект на страницу со списком накладных
    redirect(base_url('cabinet/invoices'));
}

public function view_invoice($invoice_id)
{
    // Проверка авторизации пользователя
    $bin = $this->session->userdata('bin');
    if (empty($bin)) {
        redirect(base_url());
    }

    // Загружаем модель
    $this->load->model('cabinet_model');

    // Получаем данные накладной по ID
    $data['invoice'] = $this->cabinet_model->get_invoice_by_id($invoice_id);

    // Проверяем, существует ли накладная
    if (!$data['invoice']) {
        show_404(); // Если накладная не найдена
    }

    // Отображаем страницу с подробной информацией о накладной
    $data['d'] = "Детали накладной";
    $data['title'] = "Просмотр накладной";
    
    $this->load->view('templates/cabinet/invoices/view_invoice', $data);
}

public function print_invoice($invoice_id) {
    // Проверяем авторизацию пользователя
    $bin = $this->session->userdata('bin');
    
    if (empty($bin)) {
        redirect(base_url());
    }

    // Загружаем модель для работы с накладными
    $this->load->model('cabinet_model');
    
    // Получаем данные накладной по ID
    $data['invoice'] = $this->cabinet_model->get_invoice_by_id($invoice_id);

    // Загружаем страницу с шаблоном печати
    $this->load->view('templates/cabinet/invoices/print_invoice_view', $data);
}

    
public function get_recipient_template()
{
    $template_id = $this->input->post('id');

    // Загружаем модель
    $this->load->model('cabinet_model');

    // Получаем данные компании по ID
    $template = $this->cabinet_model->get_template_by_id($template_id);

    if ($template) {
        echo json_encode(['status' => 'success', 'company' => $template['company'], 'recipient_name' => $template['recipient_name'], 'recipient_phone' => $template['recipient_phone'], 'address' => $template['address'], 'city' => $template['city'], 'country' => $template['country'], 'region' => $template['region'], 'district' => $template['district']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Шаблон не найден.']);
    }
}    
    
    
    
    
    
    
    
 public function profile_settings() {
        // Проверяем авторизацию пользователя
        $bin = $this->session->userdata('bin');
        
        if (empty($bin)) {
            redirect(base_url());
        }

        // Загружаем модель для работы с пользователями
        $this->load->model('cabinet_model');

        // Получаем данные пользователя по bin
        $data['user'] = $this->cabinet_model->get_user_by_bin($bin);

        // Отображаем страницу настройки профиля
        $data['d'] = "Настройка профиля";
        $data['title'] = "Редактировать профиль";

        $this->load->view('templates/cabinet/profile/profile_settings_view', $data);
    }   
    
    
public function update_profile() {
        // Проверяем авторизацию пользователя
        $bin = $this->session->userdata('bin');
        
        if (empty($bin)) {
            redirect(base_url());
        }

        // Получаем данные из формы
        $user_data = array(
            'director_name' => $this->input->post('director_name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'company_name' => $this->input->post('company_name'),
            'city' => $this->input->post('city'),
            'country' => $this->input->post('country'),
            'region' => $this->input->post('region'),
            'district' => $this->input->post('district'),
            'address' => $this->input->post('address')
        );

        // Загружаем модель
        $this->load->model('cabinet_model');

        // Обновляем данные пользователя по bin
        $this->cabinet_model->update_user_by_bin($bin, $user_data);

        // Уведомление об успешном обновлении
        $this->session->set_flashdata('success', 'Ваш профиль успешно обновлен!');

        // Перенаправляем на страницу настроек профиля
        redirect('cabinet/profile_settings');
    }    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
 /////////////////////////////////////////////////////////////////////////ШАБЛОНЫ ОТПРАВИТЕЛЕЙ   /////////////////////////////////////////////////////////////////////////
    public function recipient_templates()
    {
        // Получаем bin из сессии
        $bin = $this->session->userdata('bin');
        $this->load->model('cabinet_model');
        // Проверяем, авторизован ли пользователь
        if (empty($bin)) {
            // Если bin пустой, перенаправляем на главную страницу
            redirect(base_url());
        } else {
            // Если пользователь авторизован, подготавливаем данные для отображения
            $data['d'] = "Личный кабинет";
            $data['title'] = "Личный кабинет";
            $data['title_seo'] = "Личный кабинет";
            $users = $this->cabinet_model->get_users_by_email($bin);
            $data['templates'] = $this->cabinet_model->get_templates_by_user($users['id']);
            // Подгружаем представление для шаблонов отправителей
            $this->load->view('templates/cabinet/recipient_templates/recipient_templates_view', $data);
        }
    }
    
    public function add_recipient_templates()
{
    // Проверяем авторизацию пользователя
    $bin = $this->session->userdata('bin');
    
    if (empty($bin)) {
        redirect(base_url());
    } else {
        $data['d'] = "Добавить отправителя";
        $data['title'] = "Добавить отправителя";
        $data['title_seo'] = "Добавить отправителя";

        // Загрузка страницы для добавления нового отправителя
        $this->load->view('templates/cabinet/recipient_templates/add_recipient_view', $data);
    }
}

public function save_recipient_template()
{
    // Проверяем, авторизован ли пользователь
    $bin = $this->session->userdata('bin');
     $this->load->model('cabinet_model');
    if (empty($bin)) {
        redirect(base_url());
    } else {
            $users = $this->cabinet_model->get_users_by_email($bin);
            // Получаем данные из формы
            $recipient_data = array(
                'user_id'          => $users['id'], // Получаем ID пользователя из сессии
                'recipient_name'   => $this->input->post('recipient_name'),
                'recipient_phone'  => $this->input->post('recipient_phone'),
                'company'          => $this->input->post('company'),
                'city'             => $this->input->post('city'),
                'country'          => $this->input->post('country'),
                'region'           => $this->input->post('region'),
                'district'         => $this->input->post('district'),
                'address'          => $this->input->post('address')
            );


            
            // Сохраняем данные в базе
            if ($this->cabinet_model->save_template($recipient_data)) {
                // Если данные успешно сохранены, перенаправляем пользователя на страницу шаблонов
                $this->session->set_flashdata('success_message', 'Шаблон отправителя успешно добавлен!');
                redirect('cabinet/recipient_templates');
            } else {
                // Обработка ошибки сохранения данных
                echo "Ошибка при сохранении данных";
            }
        
    }
}

public function delete_recipient_template()
{
    // Проверяем, авторизован ли пользователь
    $bin = $this->session->userdata('bin');
    
    if (empty($bin)) {
       echo json_encode(array('status' => 'error', 'message' => 'Необходима авторизация.'));
        return;
    }

    // Получаем ID шаблона из POST запроса
    $template_id = $this->input->post('id');

    // Загружаем модель и удаляем шаблон
    $this->load->model('cabinet_model');
    $deleted = $this->cabinet_model->delete_template($template_id);

    if ($deleted) {
        echo json_encode(array('status' => 'success', 'message' => 'Шаблон отправителя удален.'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Ошибка при удалении шаблона.'));
    }
}

public function delete_template($template_id)
{
    // Удаление шаблона по ID
    $this->db->where('id', $template_id);
    return $this->db->delete('recipient_templates');
}
public function edit_recipient_templates($id)
{
    // Проверяем, авторизован ли пользователь
    $bin = $this->session->userdata('bin');
    
    if (empty($bin)) {
        redirect(base_url());
    }

    // Загружаем модель
    $this->load->model('cabinet_model');

    // Получаем данные шаблона по ID
    $data['template'] = $this->cabinet_model->get_template_by_id($id);

    if (empty($data['template'])) {
        show_404(); // Если шаблон не найден, показываем ошибку 404
    }

    // Отображаем форму для редактирования
    $data['d'] = "Редактирование шаблона отправителя";
    $data['title'] = "Редактировать шаблон";
    
    $this->load->view('templates/cabinet/recipient_templates/edit_recipient_view', $data);
}

public function update_recipient_template()
{
    // Проверяем, авторизован ли пользователь
    $bin = $this->session->userdata('bin');
    
    if (empty($bin)) {
        redirect(base_url());
    }

    // Получаем данные из формы
    $id = $this->input->post('id');
    $recipient_data = array(
        'recipient_name'   => $this->input->post('recipient_name'),
        'recipient_phone'  => $this->input->post('recipient_phone'),
        'company'          => $this->input->post('company'),
        'city'             => $this->input->post('city'),
        'country'          => $this->input->post('country'),
        'region'           => $this->input->post('region'),
        'district'         => $this->input->post('district'),
        'address'          => $this->input->post('address')
    );

    // Загружаем модель
    $this->load->model('cabinet_model');
    
    // Обновляем данные в базе
    $updated = $this->cabinet_model->update_template($id, $recipient_data);

    if ($updated) {
        $this->session->set_flashdata('success_message', 'Шаблон успешно обновлен.');
        redirect('cabinet/recipient_templates');
    } else {
        echo "Ошибка при обновлении шаблона";
    }
}
/////////////////////////////////////////////////////////////////////////ШАБЛОНЫ ОТПРАВИТЕЛЕЙ   /////////////////////////////////////////////////////////////////////////
    
    function save(){

       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        
        if($this->input->post('save')){
            $u['fio'] = $this->input->post('fio');
            $u['name'] = $this->input->post('name');
            $u['phone'] = $this->input->post('phone');
            $u['dolzh'] = $this->input->post('dolzh');
            $u['company'] = $this->input->post('company');
            $u['bin'] = $this->input->post('bin');
            $u['indeks'] = $this->input->post('indeks');
            $u['country'] = $this->input->post('country');
            $u['city'] = $this->input->post('city');
            $u['address'] = $this->input->post('address');
            $this->pages_model->update_profile($data['user']['id'], $u);
            redirect(base_url().'cabinet?save=1');
        }
        //$this->load->view('templates/cabinet/cabinet_view',$data);
      }
    }
    function addprice(){

       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        
        if($this->input->post('save')){
            $u['text'] = $this->input->post('text');
            $u['price'] = $this->input->post('price');
            $u['email'] = $email;
            
            $this->pages_model->add_price($u);
            redirect(base_url().'cabinet/price?save=1');
        }
        //$this->load->view('templates/cabinet/cabinet_view',$data);
      }
    }
function add_avatar(){
    $email=$this->session->userdata('email');
    $data['user'] = $this->pages_model->get_user_by_login($email);
    if(!empty($_FILES)) 
    {
        $config['upload_path'] = './images/profile';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['overwrite'] = TRUE;
        $config['max_size']	= '5000';
        $config['max_width']  = '5024';
        $config['max_height']  = '5068';
        $this->load->library('upload', $config);
        if(!empty($_FILES['avaphoto']))
        {
            
            //print "avaphoto<br/>";
            include('SimpleImage.php'); //модуль подключаем
            //$config['file_name']="usrava".$data['user']['id'];
            $fn=$this->getRandomString();
            $config['file_name']=$fn=$this->getRandomString();
            
            //unlink(FCPATH.'images/profile/'.$config['file_name']);
            $this->upload->initialize($config);
            if ($this->upload->do_upload('avaphoto')) 
            {
                //print "upload<br/>";
                $upldata=$this->upload->data();
                $thumbname=str_replace('.','_thumb.',$upldata['orig_name']);
                $uploadthumb='images/profile/thumbs/'.$thumbname;
                
                $this->load->helper('date');
                $this->load->helper('string');
                
                $path=FCPATH.'images/profile/'.$upldata['orig_name'];
                $urlpath='/images/profile/'.$upldata['orig_name'];
                
                $img_test = new abeautifulsite\SimpleImage($path);
                $img_test->thumbnail(300, 300);
                $img_test->save($uploadthumb);
                
                
                $photo=$upldata['orig_name'];
                $thumb=$thumbname;
                $update['ava'] = $urlpath;
                $update['thumb_ava'] = $uploadthumb;
                $this->pages_model->setAvator($data['user']['id'], $update);
            }
           // die();
            redirect(base_url().'cabinet/');
        }
    }
    redirect(base_url().'cabinet/');
}
    function add_portfolio(){
        $email=$this->session->userdata('email');
        
        if(!empty($_FILES)) 
    		{
    			$config['upload_path'] = './images/post';
    			$config['allowed_types'] = 'gif|jpg|png';
    			$config['max_size']	= '5000';
    			$config['max_width']  = '5024';
    			$config['max_height']  = '5068';
    			$this->load->library('upload', $config);
    			
    			
    			//print_r($_FILES['photo']);
    			//
    			if(!empty($_FILES['photo']))
    			{
    				//print_r($_FILES['photo']);	
    				include('SimpleImage.php'); //модуль подключаем
    				$photos=[];
    				$thumbs=[];
    				foreach($_FILES['photo']['name'] as $key=>$file)
    				{
    					if(!empty($file))
    					{
    						//print_r($file);
    						$_FILES['images[]']['name']		= $_FILES['photo']['name'][$key];
    						$_FILES['images[]']['type']  	= $_FILES['photo']['type'][$key];
    						$_FILES['images[]']['tmp_name']= $_FILES['photo']['tmp_name'][$key];
    						$_FILES['images[]']['error']= $_FILES['photo']['error'][$key];
    						$_FILES['images[]']['size']= $_FILES['photo']['size'][$key];
    									
    						$fn= $this->getRandomString();
    						$config['file_name']=$fn;
							
						
    						//echo "<p>".$file.' = '.$fn."</p>";
    						//$photos[]=$fn;
    						
    						
    						$this->upload->initialize($config);
    						if ($this->upload->do_upload('images[]')) {
    							$upldata=$this->upload->data();
    							//print_r($upldata);
    							//echo "<p>ok-$fn</p>";
								
								$thumbname=str_replace('.','_thumb.',$upldata['orig_name']);
								$uploadthumb='images/post/thumbs/'.$thumbname;
								
								$this->load->helper('date');
								$this->load->helper('string');
								
								$path=FCPATH.'images/post/'.$upldata['orig_name'];
								
								$img_test = new abeautifulsite\SimpleImage($path);
								$img_test->thumbnail(300, 300);
								$img_test->save($uploadthumb);
								
								
    							$photos[]=$upldata['orig_name'];
    							$thumbs[]=$thumbname;
    						} else {
    							$errors = $this->upload->display_errors();
    							
    							print_r($errors);
    							return false;
    						}
    					}
    					
    				}
    				$post['photo']=json_encode($photos);
    				$post['thumb']=json_encode($thumbs);
                    $this->pages_model->add_portoflio($post, $email);
    				//die();
    			}
    		}
            redirect(base_url().'cabinet/portfolio');
    }
    function portfolio(){
       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        $data['user_portfolio'] = $this->pages_model->portfolio_view($email);
        $this->load->view('templates/cabinet/portfolio_view',$data);
      }
    }
    function reviews(){

       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        $data['reviews'] = $this->pages_model->get_reviews_by_user($email);
        $this->load->view('templates/cabinet/reviews_view',$data);
      }
    }

    function myorders(){
        $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        $data['orders'] = $this->pages_model->get_orders_by_user($email);
        $this->load->view('templates/cabinet/myorders_view',$data);
      }
    }
    
    function message(){

       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        $data['message'] = $this->pages_model->get_message_by_user($email);
        $this->load->view('templates/cabinet/message_view',$data);
      }
    }
     function price(){

       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $data['user'] = $this->pages_model->get_user_by_login($email);
        $data['price'] = $this->pages_model->get_price_email($email);
        $this->load->view('templates/cabinet/price_view',$data);
      }
    }
    function shop(){
        
       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $this->load->view('templates/cabinet/shop/shop_view',$data);
      }
    }
    
    function category(){
        
       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $this->load->view('templates/cabinet/shop/category_view',$data);
      }
    }
    function archive(){
        
       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $this->load->view('templates/cabinet/archive_view',$data);
      }
    }
    
    
     function spec(){
        
       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $this->load->view('templates/cabinet/spec_view',$data);
      }
    }
    
    
    function news(){
        
       $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $this->load->view('templates/cabinet/news_view',$data);
      }
    }
    
    
    function cart(){
         $email=$this->session->userdata('email');
        if (empty($email)) {
            redirect(base_url());
      } else {
        $this->load->helper('text');
        $data['d'] = "Личный кабинет";
        $data['title'] = "Личный кабинет";
        $data['title_seo'] = "Личный кабинет";
        $this->load->view('templates/cabinet/cart_view',$data);
      }
    }
    
    
    
    
    
    
    
    
    
    function recovery(){
         $this->load->helper('date');
         $this->load->helper('string');
         $data['']='';
         $data['error']='';
         $this->load->model('cabinet_model');
         if($this->input->post('send')){
            $bin=$this->input->post('bin');
            $info_user = $this->cabinet_model->get_users_by_email($bin);
            if (!empty($info_user)){
                $users['restore_code']=random_string('numeric',22);
                $this->cabinet_model->update_user_info($bin,$users);
                $this->cabinet_model->send_recovery_password($info_user,$users);
                redirect(base_url().'cabinet/success_recovery');
            } else {
                $data['error'] = "Такой пользователь не зарегестрирован в системе";
                $this->load->view('templates/cabinet/recovery_view',$data);
            }
         } else {
            $this->load->view('templates/cabinet/recovery_view',$data);
         }
         
         
    }
    
    function newpassword($restore_code){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
         $data['']='';
         $data['error']='';
         $this->load->model('cabinet_model');
         if($this->input->post('restore')){
                if ($this->input->post('password')!=$this->input->post('re-password')) {
                    $data['error']='Пароли не совпадают';
                    $data['restore_code']=$restore_code;
                    $this->load->view('templates/cabinet/newpassword_view',$data);
                } else {
                    $user['password'] = sha1(md5($this->input->post('password')));
                    $this->cabinet_model->update_password($restore_code,$user);
                    redirect(base_url().'cabinet/success_restore');
                }
         } else {
            $data['restore_code']=$restore_code;
            $this->load->view('templates/cabinet/newpassword_view',$data);
         }  
    }
    
    function add_proposition(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $data['']='';
        $this->load->view('templates/cabinet/add_proposition_view',$data);
    }
    function add_proposition_investor(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $this->load->helper('date');
        $this->load->helper('string');
        $this->load->model('cabinet_model');
        $email=$this->session->userdata('email');
       // if (empty($email)) {
      //      redirect(base_url().'cabinet/logout');
      //} else {
            $data['']='';
            if($this->input->post('send')){
                $lang = $this->session->userdata('lang');
           
                if ($lang=="eng") $add['name_your_deal'] = $this->input->post('name_your_deal');
                if ($lang=="ru") $add['name_your_deal_ru'] = $this->input->post('name_your_deal');
                $add['industries_of_your_interest_category'] = $this->input->post('industries_of_your_interest_category');
                $add['industries_of_your_interest_subcategory'] = $this->input->post('industries_of_your_interest_subcategory');
                if ($lang=="eng") $add['company_headquarters'] = $this->input->post('company_headquarters');
                if ($lang=="ru") $add['company_headquarters_ru'] = $this->input->post('company_headquarters');
                $add['investment_size_from_ot'] = $this->input->post('investment_size_from_ot');
                $add['investment_size_from_do'] = $this->input->post('investment_size_from_do');
                $add['valute'] = $this->input->post('valute');
                $add['detail_company'] = $this->input->post('detail_company');
                $add['person_responsable'] = $this->input->post('person_responsable');
                $add['phone'] = $this->input->post('phone');
                $add['email'] = $this->input->post('email');
                $add['web_site'] = $this->input->post('web_site');
                $add['comments'] = $this->input->post('comments');
                if ($lang=="eng") $add['details_of_the_deal'] = $this->input->post('details_of_the_deal');
                if ($lang=="ru") $add['details_of_the_deal_ru'] = $this->input->post('details_of_the_deal');
                $datestring = "%Y-%m-%d %H:%i:%a";
                $time = time();
                $add['date'] = mdate($datestring, $time);
                $add['ip'] = $this->input->ip_address();
                $add['user'] = $email;
                $add['moder'] = 0;
                $this->pages_model->add_deal($add);
                $id_deal = mysql_insert_id();
                $who_are_you = $this->input->post('who_are_you');
                if (!empty($who_are_you)) {
                    foreach($who_are_you as $item) {
                        $add_who_are_you['id_who_are_you'] = $item;
                        $add_who_are_you['id_deal'] = $id_deal;
                        $this->pages_model->add_who_are_you($add_who_are_you);
                    }
                }
                $areas_of_your_interest = $this->input->post('areas_of_your_interest');
                if (!empty($areas_of_your_interest)) {
                    foreach($areas_of_your_interest as $item) {
                        $add_areas_of_your_interest['id_areas_of_your_interest'] = $item;
                        $add_areas_of_your_interest['id_deal'] = $id_deal;
                        $this->pages_model->add_areas_of_your_interest($add_areas_of_your_interest);
                    }
                }
                $profile = $this->input->post('profile');
                if (!empty($profile)) {
                        $add_profile['id_profile'] = $profile;
                        $add_profile['id_deal'] = $id_deal;
                        $this->pages_model->add_profile($add_profile);
                }
                $investment_strategy = $this->input->post('investment_strategy');
                if (!empty($investment_strategy)) {
                    foreach($investment_strategy as $item) {
                        $add_investment_strategy['id_investment_strategy'] = $item;
                        $add_investment_strategy['id_deal'] = $id_deal;
                        $this->pages_model->add_investment_strategy($add_investment_strategy);
                    }
                }
              redirect(base_url().'cabinet/success_add');      
            } else {
            $this->load->view('templates/cabinet/add_proposition_investor_view',$data);
            }
        //}
    }
    function add_proposition_capital(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $this->load->helper('date');
        $this->load->helper('string');
        $this->load->model('cabinet_model');
        $email=$this->session->userdata('email');
       // if (empty($email)) {
      //      redirect(base_url().'cabinet/logout');
      ///} else {
            $data['']='';
            if($this->input->post('send')){
                $lang = $this->session->userdata('lang');
                if ($lang=="eng") $add['name_your_deal'] = $this->input->post('name_your_deal');
                if ($lang=="ru") $add['name_your_deal_ru'] = $this->input->post('name_your_deal');
                $add['industries_of_your_interest_category'] = $this->input->post('industries_of_your_interest_category');
                $add['industries_of_your_interest_subcategory'] = $this->input->post('industries_of_your_interest_subcategory');
                if ($lang=="eng") $add['company_headquarters'] = $this->input->post('company_headquarters');
                if ($lang=="ru") $add['company_headquarters_ru'] = $this->input->post('company_headquarters');
                $add['acceptable_investment_size_from_ot'] = $this->input->post('acceptable_investment_size_from_ot');
                $add['acceptable_investment_size_from_do'] = $this->input->post('acceptable_investment_size_from_do');
                $add['valute'] = $this->input->post('valute');
                $add['detail_company'] = $this->input->post('detail_company');
                $add['person_responsable'] = $this->input->post('person_responsable');
                $add['phone'] = $this->input->post('phone');
                $add['email'] = $this->input->post('email');
                $add['file_1'] = $this->input->post('image_1');
                $add['file_2'] = $this->input->post('image_2');
                $add['file_3'] = $this->input->post('image_3');
                $add['file_4'] = $this->input->post('image_4');
                $add['file_5'] = $this->input->post('image_5');
                $add['web_site'] = $this->input->post('web_site');
                $add['comments'] = $this->input->post('comments');
                if ($lang=="eng") $add['details_of_the_deal'] = $this->input->post('details_of_the_deal');
                if ($lang=="ru") $add['details_of_the_deal_ru'] = $this->input->post('details_of_the_deal');
                $datestring = "%Y-%m-%d %H:%i:%a";
                $time = time();
                $add['date'] = mdate($datestring, $time);
                $add['ip'] = $this->input->ip_address();
                $add['user'] = $email;
                $add['moder'] = 0;
                $this->pages_model->add_capital($add);
                $id_deal = mysql_insert_id();
                $who_are_you = $this->input->post('who_are_you');
                if (!empty($who_are_you)) {
                   
                        $add_who_are_you['id_who_are_you'] = $who_are_you;
                        $add_who_are_you['id_deal'] = $id_deal;
                        $this->pages_model->add_who_are_you_c($add_who_are_you);
                    
                }
                $areas_of_your_operation = $this->input->post('areas_of_your_operation');
                if (!empty($areas_of_your_operation)) {
                    foreach($areas_of_your_operation as $item) {
                        $add_areas_of_your_operation['id_areas_of_your_operation'] = $item;
                        $add_areas_of_your_operation['id_deal'] = $id_deal;
                        $this->pages_model->add_areas_of_your_operation_c($add_areas_of_your_operation);
                    }
                }
                $preferred_investment_type = $this->input->post('preferred_investment_type');
                if (!empty($preferred_investment_type)) {
                    foreach($preferred_investment_type as $item) {
                        $add_preferred_investment_type['id_preferred_investment_type'] = $item;
                        $add_preferred_investment_type['id_deal'] = $id_deal;
                        $this->pages_model->add_preferred_investment_type_c($add_preferred_investment_type);
                    }
                }
                $preferred_investor_type = $this->input->post('preferred_investor_type');
                if (!empty($preferred_investor_type)) {
                    foreach($preferred_investor_type as $item) {
                        $add_preferred_investor_type['id_preferred_investor_type'] = $item;
                        $add_preferred_investor_type['id_deal'] = $id_deal;
                        $this->pages_model->add_preferred_investor_type_c($add_preferred_investor_type);
                    }
                }
              redirect(base_url().'cabinet/success_add');      
            } else {
        $this->load->view('templates/cabinet/add_proposition_capital_view',$data);
        }
        //}
    }
    function success(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $data['']='';
        $this->load->view("templates/cabinet/success_view",$data);
    }
    function success_add(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $data['']='';
        $this->load->view("templates/cabinet/success_add_view",$data);
    }
    function success_restore(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $data['']='';
        $this->load->view('templates/cabinet/restore_success_view',$data);
    }
    
    function success_recovery(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
        $data['']='';
        $this->load->view('templates/cabinet/recovery_success_view',$data);
    }
     function logout(){
        $lang=$this->session->userdata('lang');
        if (empty($lang)){
            $d['lang'] = "eng";
            $this->session->set_userdata($d);
        }
            $this->session->unset_userdata('email');
            redirect(base_url());
        }
}