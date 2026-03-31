<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Models\RecipientTemplate;
use App\Models\DescriptionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CabinetController extends Controller
{
    private $telegramBotToken = '7328907732:AAGhdEnWZfpOp22hczseO1TjiKoB19pZB4g';
    private $telegramChatId = -1002187416306;

    private function getUser()
    {
        $bin = session('bin');
        if (!$bin) return null;
        return User::where('bin', $bin)->first();
    }

    private function checkAuth()
    {
        if (!session('bin')) {
            return redirect('/');
        }
        return null;
    }

    // === AUTH ===
    public function authForm()
    {
        if (session('bin')) return redirect('/cabinet');
        return view('cabinet.auth', ['error' => '']);
    }

    public function auth(Request $request)
    {
        $bin = $request->input('bin');
        $password = sha1(md5($request->input('password')));
        $user = User::where('bin', $bin)->where('password', $password)->where('activate', 1)->first();
        if ($user) {
            session(['bin' => $bin]);
            return redirect('/cabinet');
        }
        return view('cabinet.auth', ['error' => 'Данные введены некорректно']);
    }

    // === REGISTRATION ===
    public function registrationForm()
    {
        return view('cabinet.registration');
    }

    public function registration(Request $request)
    {
        if ($request->input('password') !== $request->input('re-password')) {
            return redirect('/cabinet/registration')->withErrors(['password' => 'Пароли не совпадают'])->withInput();
        }
        $bin = $request->input('bin');
        if (User::where('bin', $bin)->exists()) {
            return redirect('/cabinet/registration')->withErrors(['bin' => 'Этот БИН уже зарегистрирован'])->withInput();
        }
        User::create([
            'bin' => $bin, 'password' => sha1(md5($request->input('password'))),
            'date' => now(), 'ip' => $request->ip(), 'activate' => 0,
            'activate_code' => str_pad(random_int(0, 9999999999), 22, '0', STR_PAD_LEFT),
            'company_name' => $request->input('company_name'), 'phone' => $request->input('phone'),
            'email' => $request->input('email'), 'director_name' => $request->input('director_name'),
            'address' => $request->input('address'), 'city' => '', 'region' => '', 'country' => '', 'district' => '', 'restore_code' => '',
        ]);
        $this->sendTelegram("У вас новая регистрация, БИН: " . $bin);
        return redirect('/cabinet/success');
    }

    public function success() { return view('cabinet.success'); }

    // === DASHBOARD ===
    public function index()
    {
        if ($r = $this->checkAuth()) return $r;
        return view('cabinet.dashboard');
    }

    // === PROFILE ===
    public function profileSettings()
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        return view('cabinet.profile_settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        User::where('bin', session('bin'))->update([
            'director_name' => $request->input('director_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'company_name' => $request->input('company_name'),
            'city' => $request->input('city'),
            'country' => $request->input('country'),
            'region' => $request->input('region'),
            'district' => $request->input('district'),
            'address' => $request->input('address'),
        ]);
        return redirect('/cabinet/profile_settings')->with('success', 'Ваш профиль успешно обновлен!');
    }

    // === INVOICES ===
    public function invoices(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $query = Invoice::where('user_id', $user->id)->orderBy('id', 'desc');

        if ($request->ajax() && $request->has('search')) {
            $search = $request->input('search');
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('sender_name', 'like', "%{$search}%")
                      ->orWhere('sender_company', 'like', "%{$search}%")
                      ->orWhere('recipient_name', 'like', "%{$search}%")
                      ->orWhere('recipient_company', 'like', "%{$search}%");
                });
            }
            $invoices = $query->paginate(15);
            return view('cabinet.invoices._table', compact('invoices'))->render();
        }

        $invoices = $query->paginate(15);
        return view('cabinet.invoices.index', compact('invoices'));
    }

    public function createInvoice()
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $templates = RecipientTemplate::where('user_id', $user->id)->get();
        $descriptionTemplates = DescriptionTemplate::where('user_id', $user->id)->get();
        return view('cabinet.invoices.create', compact('user', 'templates', 'descriptionTemplates'));
    }

    public function saveInvoice(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $lastNumber = Invoice::max('invoice_number');
        $newNumber = $lastNumber ? $lastNumber + 1 : 900001;

        Invoice::create([
            'user_id' => $user->id, 'status' => 0, 'invoice_number' => $newNumber,
            'date' => $request->input('date'),
            'sender_name' => $request->input('sender_name'), 'sender_phone' => $request->input('sender_phone'),
            'sender_company' => $request->input('sender_company'), 'sender_city' => $request->input('sender_city'),
            'sender_country' => $request->input('sender_country'), 'sender_region' => $request->input('sender_region'),
            'sender_district' => $request->input('sender_district'), 'sender_address' => $request->input('sender_address'),
            'recipient_name' => $request->input('recipient_name'), 'recipient_phone' => $request->input('recipient_phone'),
            'recipient_company' => $request->input('recipient_company'), 'recipient_city' => $request->input('recipient_city'),
            'recipient_country' => $request->input('recipient_country'), 'recipient_region' => $request->input('recipient_region'),
            'recipient_district' => $request->input('recipient_district'), 'recipient_address' => $request->input('recipient_address'),
            'description' => $request->input('description', ''), 'quantity' => $request->input('quantity', 1),
            'weight' => $request->input('weight', 0), 'volume_weight' => $request->input('volume_weight'),
            'fragile' => $request->has('fragile') ? 1 : 0, 'declared_value' => $request->input('declared_value', 0),
            'payment' => 0,
            'payment_sender' => $request->has('payment_sender') ? 1 : 0,
            'payment_recipient' => $request->has('payment_recipient') ? 1 : 0,
            'payment_contract' => $request->has('payment_contract') ? 1 : 0,
            'payment_invoice' => $request->has('payment_invoice') ? 1 : 0,
            'payment_cash' => $request->has('payment_cash') ? 1 : 0,
            'special' => $request->input('special', ''),
        ]);

        $this->sendTelegram("У вас новая накладная, №: " . $newNumber);
        return redirect('/cabinet/invoices')->with('success_message', 'Накладная успешно добавлена!');
    }

    public function viewInvoice($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $invoice = Invoice::findOrFail($id);
        return view('cabinet.invoices.view', compact('invoice'));
    }

    public function printInvoice($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $invoice = Invoice::findOrFail($id);
        return view('cabinet.invoices.print', compact('invoice'));
    }

    public function exportInvoices(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $invoices = Invoice::where('user_id', $user->id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Стили заголовков
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '005795']],
        ];

        // Заголовки
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
            'AG1' => 'Планируемая дата доставки', 'AH1' => 'Фактическая дата доставки', 'AI1' => 'Особые инструкции',
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }
        $sheet->getStyle('A1:AI1')->applyFromArray($headerStyle);

        // Данные
        $row = 2;
        foreach ($invoices as $inv) {
            $sheet->setCellValue('A' . $row, $inv->id);
            $sheet->setCellValue('B' . $row, $inv->invoice_number);
            $sheet->setCellValue('C' . $row, $inv->status);
            $sheet->setCellValue('D' . $row, \Carbon\Carbon::parse($inv->date)->format('d.m.Y'));
            $sheet->setCellValue('E' . $row, $inv->sender_name);
            $sheet->setCellValue('F' . $row, $inv->sender_phone);
            $sheet->setCellValue('G' . $row, $inv->sender_company);
            $sheet->setCellValue('H' . $row, $inv->sender_city);
            $sheet->setCellValue('I' . $row, $inv->sender_country);
            $sheet->setCellValue('J' . $row, $inv->sender_region);
            $sheet->setCellValue('K' . $row, $inv->sender_district);
            $sheet->setCellValue('L' . $row, $inv->sender_address);
            $sheet->setCellValue('M' . $row, $inv->recipient_name);
            $sheet->setCellValue('N' . $row, $inv->recipient_phone);
            $sheet->setCellValue('O' . $row, $inv->recipient_company);
            $sheet->setCellValue('P' . $row, $inv->recipient_city);
            $sheet->setCellValue('Q' . $row, $inv->recipient_country);
            $sheet->setCellValue('R' . $row, $inv->recipient_region);
            $sheet->setCellValue('S' . $row, $inv->recipient_district);
            $sheet->setCellValue('T' . $row, $inv->recipient_address);
            $sheet->setCellValue('U' . $row, $inv->description);
            $sheet->setCellValue('V' . $row, $inv->quantity);
            $sheet->setCellValue('W' . $row, $inv->weight);
            $sheet->setCellValue('X' . $row, $inv->volume_weight);
            $sheet->setCellValue('Y' . $row, $inv->fragile);
            $sheet->setCellValue('Z' . $row, $inv->declared_value);
            if ($inv->payment != 0) {
                $sheet->setCellValue('AA' . $row, $inv->payment);
            }
            $sheet->setCellValue('AB' . $row, $inv->payment_sender);
            $sheet->setCellValue('AC' . $row, $inv->payment_recipient);
            $sheet->setCellValue('AD' . $row, $inv->payment_contract);
            $sheet->setCellValue('AE' . $row, $inv->payment_invoice);
            $sheet->setCellValue('AF' . $row, $inv->payment_cash);
            if ($inv->plan_date && $inv->plan_date != '0000-00-00') {
                $sheet->setCellValue('AG' . $row, \Carbon\Carbon::parse($inv->plan_date)->format('d.m.Y'));
            }
            if ($inv->fact_date && $inv->fact_date != '0000-00-00') {
                $sheet->setCellValue('AH' . $row, \Carbon\Carbon::parse($inv->fact_date)->format('d.m.Y'));
            }
            $sheet->setCellValue('AI' . $row, $inv->special);
            $row++;
        }

        // Ширина колонок
        $widths = ['A'=>3,'B'=>18,'C'=>6,'D'=>11,'E'=>18,'F'=>21,'G'=>22,'H'=>19,'I'=>19,'J'=>20,'K'=>19,'L'=>22,'M'=>19,'N'=>20,'O'=>21,'P'=>18,'Q'=>18,'R'=>20,'S'=>17,'T'=>18,'U'=>21,'V'=>16,'W'=>7,'X'=>15,'Y'=>13,'Z'=>22];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $formattedStart = \Carbon\Carbon::parse($start_date)->format('d-m-Y');
        $formattedEnd = \Carbon\Carbon::parse($end_date)->format('d-m-Y');
        $filename = "nakladnye_{$formattedStart}_-_{$formattedEnd}.xlsx";

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // === RECIPIENT TEMPLATES ===
    public function recipientTemplates()
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $templates = RecipientTemplate::where('user_id', $user->id)->get();
        return view('cabinet.recipient_templates.index', compact('templates'));
    }

    public function addRecipientTemplate()
    {
        if ($r = $this->checkAuth()) return $r;
        return view('cabinet.recipient_templates.add');
    }

    public function saveRecipientTemplate(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        RecipientTemplate::create([
            'user_id' => $user->id,
            'recipient_name' => $request->input('recipient_name'),
            'recipient_phone' => $request->input('recipient_phone'),
            'company' => $request->input('company'),
            'city' => $request->input('city'),
            'country' => $request->input('country'),
            'region' => $request->input('region'),
            'district' => $request->input('district'),
            'address' => $request->input('address'),
        ]);
        return redirect('/cabinet/recipient_templates')->with('success_message', 'Шаблон отправителя успешно добавлен!');
    }

    public function editRecipientTemplate($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $template = RecipientTemplate::findOrFail($id);
        return view('cabinet.recipient_templates.edit', compact('template'));
    }

    public function updateRecipientTemplate(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        RecipientTemplate::where('id', $request->input('id'))->update([
            'recipient_name' => $request->input('recipient_name'),
            'recipient_phone' => $request->input('recipient_phone'),
            'company' => $request->input('company'),
            'city' => $request->input('city'),
            'country' => $request->input('country'),
            'region' => $request->input('region'),
            'district' => $request->input('district'),
            'address' => $request->input('address'),
        ]);
        return redirect('/cabinet/recipient_templates')->with('success_message', 'Шаблон успешно обновлен.');
    }

    public function deleteRecipientTemplate(Request $request)
    {
        if (!session('bin')) return response()->json(['status' => 'error', 'message' => 'Необходима авторизация.']);
        RecipientTemplate::where('id', $request->input('id'))->delete();
        return response()->json(['status' => 'success', 'message' => 'Шаблон отправителя удален.']);
    }

    public function getRecipientTemplate(Request $request)
    {
        $template = RecipientTemplate::find($request->input('id'));
        if ($template) {
            return response()->json(['status' => 'success', 'company' => $template->company, 'recipient_name' => $template->recipient_name, 'recipient_phone' => $template->recipient_phone, 'address' => $template->address, 'city' => $template->city, 'country' => $template->country, 'region' => $template->region, 'district' => $template->district]);
        }
        return response()->json(['status' => 'error', 'message' => 'Шаблон не найден.']);
    }

    // === DESCRIPTION TEMPLATES ===
    public function descriptionTemplates()
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $templates = DescriptionTemplate::where('user_id', $user->id)->get();
        return view('cabinet.description_templates.index', compact('templates'));
    }

    public function addDescriptionTemplate()
    {
        if ($r = $this->checkAuth()) return $r;
        return view('cabinet.description_templates.add');
    }

    public function saveDescriptionTemplate(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        DescriptionTemplate::create([
            'user_id' => $user->id,
            'template_name' => $request->input('template_name'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity'),
            'weight' => $request->input('weight'),
            'volume_weight' => $request->input('volume_weight'),
        ]);
        return redirect('/cabinet/description_templates')->with('success_message', 'Шаблон описания успешно добавлен!');
    }

    public function editDescriptionTemplate($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $template = DescriptionTemplate::findOrFail($id);
        return view('cabinet.description_templates.edit', compact('template'));
    }

    public function updateDescriptionTemplate(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        DescriptionTemplate::where('id', $request->input('id'))->update([
            'template_name' => $request->input('template_name'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity'),
            'weight' => $request->input('weight'),
            'volume_weight' => $request->input('volume_weight'),
        ]);
        return redirect('/cabinet/description_templates')->with('success_message', 'Шаблон успешно обновлен.');
    }

    public function deleteDescriptionTemplate(Request $request)
    {
        if (!session('bin')) return response()->json(['status' => 'error', 'message' => 'Необходима авторизация.']);
        DescriptionTemplate::where('id', $request->input('id'))->delete();
        return response()->json(['status' => 'success', 'message' => 'Шаблон описания удален.']);
    }

    public function getDescriptionTemplate(Request $request)
    {
        $template = DescriptionTemplate::find($request->input('id'));
        if ($template) {
            return response()->json(['status' => 'success', 'description' => $template->description, 'quantity' => $template->quantity, 'weight' => $template->weight, 'volume_weight' => $template->volume_weight]);
        }
        return response()->json(['status' => 'error', 'message' => 'Шаблон не найден.']);
    }

    // === REPORTS ===
    public function reports(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        $user = $this->getUser();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $invoices = [];
        if ($start_date && $end_date) {
            $invoices = Invoice::where('user_id', $user->id)->whereBetween('date', [$start_date, $end_date])->get();
        }
        return view('cabinet.reports.index', compact('invoices', 'start_date', 'end_date'));
    }

    // === RECOVERY ===
    public function recoveryForm()
    {
        return view('cabinet.recovery', ['error' => '']);
    }

    public function recovery(Request $request)
    {
        $bin = $request->input('bin');
        $user = User::where('bin', $bin)->first();
        if (!$user) {
            return view('cabinet.recovery', ['error' => 'Такой пользователь не зарегистрирован в системе']);
        }
        $restoreCode = str_pad(random_int(0, 9999999999), 22, '0', STR_PAD_LEFT);
        $user->update(['restore_code' => $restoreCode]);
        // TODO: send email with restore link
        return redirect('/cabinet/success_recovery');
    }

    public function successRecovery()
    {
        return view('cabinet.success');
    }

    public function newPasswordForm($restore_code)
    {
        return view('cabinet.newpassword', ['restore_code' => $restore_code, 'error' => '']);
    }

    public function newPassword(Request $request, $restore_code)
    {
        if ($request->input('password') !== $request->input('re-password')) {
            return view('cabinet.newpassword', ['restore_code' => $restore_code, 'error' => 'Пароли не совпадают']);
        }
        User::where('restore_code', $restore_code)->update(['password' => sha1(md5($request->input('password')))]);
        return redirect('/cabinet/auth');
    }

    // === LOGOUT ===
    public function logout()
    {
        session()->forget('bin');
        return redirect('/');
    }

    // === TELEGRAM ===
    private function sendTelegram($message)
    {
        try {
            Http::post("https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage", [
                'chat_id' => $this->telegramChatId, 'text' => $message,
            ]);
        } catch (\Exception $e) {}
    }
}
