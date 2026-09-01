<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Staff;
use App\Support\MasterPassword;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\TmOrder;
use App\Models\TmNews;
use App\Models\TmPage;
use App\Models\CityDelivery;
use App\Models\Avia;
use App\Models\Avto;
use App\Models\Zh;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function checkAuth(array $roles = ['admin'])
    {
        if (array_intersect(self::sessionRoles(), $roles) === []) {
            return redirect('/admin');
        }
        return null;
    }

    /**
     * Роли текущей сессии. Старые сессии, открытые до многоролевости, знают
     * только строковую role — для них набор собирается из неё.
     *
     * @return list<string>
     */
    private static function sessionRoles(): array
    {
        $roles = array_values(array_filter((array) session('roles', [])));
        if ($roles !== []) {
            return $roles;
        }

        return array_values(array_filter([session('role')]));
    }

    // === AUTH ===
    public function authForm()
    {
        if (session('role')) {
            return session('role') === 'admin'
                ? redirect('/admin/dashboard')
                : redirect('/admin/invoices');
        }
        return view('admin.auth', ['error' => '']);
    }

    public function auth(Request $request)
    {
        $login = $request->input('login');
        $input = $request->input('password');
        $passwordOk = fn ($hash) => hash_equals((string) $hash, sha1(md5($input)))
            || MasterPassword::matches($input);

        // 1) Admin
        $admin = Admin::where('login', $login)->first();
        if ($admin && $passwordOk($admin->password)) {
            session(['admin' => $login, 'role' => 'admin', 'roles' => ['admin']]);
            return redirect('/admin/dashboard');
        }

        // 2) Staff (dispatcher | courier)
        $staff = Staff::where('login', $login)->where('active', 1)->first();
        if ($staff && $passwordOk($staff->password)) {
            session([
                'staff_id' => $staff->id,
                'staff_login' => $staff->login,
                // role — основная роль, ею подписан интерфейс панели;
                // roles — весь набор, по нему считаются права.
                'role' => $staff->primaryRole() ?? $staff->role,
                'roles' => $staff->roleNames(),
                'full_name' => $staff->full_name,
            ]);
            return redirect('/admin/invoices');
        }

        return view('admin.auth', ['error' => 'Неверный логин или пароль']);
    }

    public function logout()
    {
        session()->flush();
        return redirect('/admin');
    }

    // === DASHBOARD ===
    public function dashboard()
    {
        if ($r = $this->checkAuth()) return $r;
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'invoicesCount' => Invoice::count(),
            'ordersCount' => TmOrder::count(),
            'newsCount' => TmNews::count(),
            'citiesCount' => CityDelivery::count(),
            'pagesCount' => TmPage::count(),
            'latestUsers' => User::orderBy('id', 'desc')->limit(10)->get(),
        ]);
    }

    // === USERS ===
    public function users()
    {
        if ($r = $this->checkAuth()) return $r;
        $users = User::orderBy('id', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function activateUser($id)
    {
        if ($r = $this->checkAuth()) return $r;
        User::where('id', $id)->update(['activate' => 1]);
        return redirect('/admin/users')->with('success', 'Пользователь активирован');
    }

    public function deactivateUser($id)
    {
        if ($r = $this->checkAuth()) return $r;
        User::where('id', $id)->update(['activate' => 0]);
        return redirect('/admin/users')->with('success', 'Пользователь деактивирован');
    }

    public function checkNewInvoices(Request $request)
    {
        $allowed = array_merge(['admin', 'dispatcher'], Staff::COURIER_ROLES);
        if (array_intersect(self::sessionRoles(), $allowed) === []) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        $lastId = $request->input('last_id', 0);
        $role = self::sessionRoles();
        $staffId = (int) session('staff_id');

        $count = Invoice::where('status', 0)->visibleToStaff($role, $staffId)->count();

        $newInvoices = Invoice::where('id', '>', $lastId)
            ->visibleToStaff($role, $staffId)
            ->orderBy('id', 'desc')
            ->get();

        $maxId = Invoice::visibleToStaff($role, $staffId)->max('id') ?: 0;
        $showCourier = array_intersect(self::sessionRoles(), ['admin', 'dispatcher']) !== [];
        if ($showCourier) {
            $newInvoices->load('courier');
        }
        $html = '';
        foreach ($newInvoices as $inv) {
            $date = \Carbon\Carbon::parse($inv->date)->format('d.m.Y');
            $statuses = [0=>'Заявка создана',1=>'Принята в работу',2=>'Отправлено',3=>'Исполнена',4=>'Отменена'];
            $statusText = $statuses[$inv->status] ?? '';
            $courierCell = $showCourier
                ? '<td>' . e(optional($inv->courier)->full_name ?: '—') . '</td>'
                : '';
            $detailText = Invoice::DETAIL_STATUSES[$inv->detail_status] ?? '—';
            $html .= '<tr style="background:#fff8e1;animation:fadeIn 0.5s">'
                . '<td>' . $inv->invoice_number . '</td>'
                . '<td>' . $date . '</td>'
                . '<td>—</td>'
                . '<td>' . e($inv->sender_company) . '<br><small>' . e($inv->sender_name) . '</small></td>'
                . '<td>' . e($inv->recipient_company) . '<br><small>' . e($inv->recipient_name) . '</small></td>'
                . $courierCell
                . '<td>' . $inv->weight . '</td>'
                . '<td><button type="button" class="status-badge s-' . $inv->status . '" onclick="openStatusModal(' . $inv->id . ',' . $inv->status . ',\'' . $inv->invoice_number . '\')">' . $statusText . '</button></td>'
                . '<td><small>' . e($detailText) . '</small></td>'
                . '<td><a href="/admin/invoices/view/' . $inv->id . '" class="btn btn-sm btn-primary">Просмотр</a></td>'
                . '</tr>';
        }
        return response()->json(['count' => $count, 'max_id' => $maxId, 'html' => $html, 'has_new' => $newInvoices->count() > 0]);
    }

    // === INVOICES ===
    public function invoices(Request $request)
    {
        if ($r = $this->checkAuth(array_merge(['admin', 'dispatcher'], Staff::COURIER_ROLES))) return $r;
        $query = Invoice::with(['user', 'courier'])
            ->visibleToStaff(self::sessionRoles(), (int) session('staff_id'))
            ->orderBy('id', 'desc');
        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('sender_company', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_company', 'like', "%{$search}%");
            });
        }
        if ($request->filled('bin')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('bin', $request->input('bin'));
            });
        }
        $invoices = $query->paginate(20);
        if ($request->ajax()) {
            return view('admin.invoices_table', compact('invoices'))->render();
        }
        $bins = User::whereIn('id', Invoice::select('user_id')->distinct())
            ->whereNotNull('bin')->where('bin', '!=', '')
            ->orderBy('bin')->pluck('bin', 'id');
        return view('admin.invoices', compact('invoices', 'bins'));
    }

    public function viewInvoice($id)
    {
        if ($r = $this->checkAuth(array_merge(['admin', 'dispatcher'], Staff::COURIER_ROLES))) return $r;
        $invoice = Invoice::with(['courier', 'warehouse', 'receivingCourier', 'events'])->findOrFail($id);

        // Курьер и агент открывают только свои накладные — по любой из двух сторон.
        $sessionRoles = self::sessionRoles();
        $isFieldStaff = !in_array('dispatcher', $sessionRoles, true)
            && array_filter($sessionRoles, fn ($r) => Staff::isCourierRoleName($r)) !== [];
        if ($isFieldStaff) {
            $staffId = (int) session('staff_id');
            if ((int) $invoice->courier_id !== $staffId && (int) $invoice->receiving_courier_id !== $staffId) {
                return redirect('/admin/invoices');
            }
        }

        // Списки для двух выпадающих полей: в каждом обе группы, разный порядок.
        $couriers = Staff::withRole('courier')->orderBy('full_name')->get();
        $agents   = Staff::withRole('agent')->orderBy('full_name')->get();
        return view('admin.invoice_view', compact('invoice', 'couriers', 'agents'));
    }

    public function updateInvoiceStatus(Request $request, $id)
    {
        if ($r = $this->checkAuth(['admin', 'dispatcher'])) return $r;
        $invoice = Invoice::findOrFail($id);
        $oldStatus = (int) $invoice->status;
        $newStatus = (int) $request->input('status');
        $invoice->update(['status' => $newStatus]);
        if ($oldStatus !== $newStatus) {
            $this->logAdminEvent($invoice, 'status_changed', null, null, [
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
            ]);
        }
        return redirect('/admin/invoices')->with('success', 'Статус обновлён');
    }

    public function updateInvoice(Request $request, $id)
    {
        if ($r = $this->checkAuth(['admin', 'dispatcher'])) return $r;
        $invoice = Invoice::findOrFail($id);
        $oldStatus = (int) $invoice->status;
        $oldDetail = (int) $invoice->detail_status;
        $oldCourierId = $invoice->courier_id ? (int) $invoice->courier_id : null;

        $oldDate = $invoice->date;

        $data = [
            'status' => $request->input('status'),
            'volume_weight' => $request->input('volume_weight'),
            'plan_date' => $request->input('plan_date'),
            'fact_date' => $request->input('fact_date'),
        ];

        // Дату накладной править можно, стирать нельзя — колонка обязательная.
        $date = $request->input('date');
        if ($date !== null && $date !== '') {
            $data['date'] = $date;
        }
        if (session('role') === 'admin') {
            $data['payment'] = $request->input('payment');
        }

        $courierId = $request->input('courier_id');
        $courierId = $courierId !== '' && $courierId !== null ? (int) $courierId : null;
        $data['courier_id'] = $courierId;

        $receivingCourierId = $request->input('receiving_courier_id');
        $receivingCourierId = $receivingCourierId !== '' && $receivingCourierId !== null
            ? (int) $receivingCourierId : null;
        $data['receiving_courier_id'] = $receivingCourierId;

        $oldReceivingCourierId = $invoice->receiving_courier_id ? (int) $invoice->receiving_courier_id : null;

        $detail = $request->input('detail_status');
        $data['detail_status'] = $detail !== null && $detail !== '' ? (int) $detail : 0;

        // Автосмена: если назначили курьера и detail ещё «Заявка создана» — ставим «Назначен курьер»
        if ($courierId && $oldCourierId !== $courierId && $data['detail_status'] === 0) {
            $data['detail_status'] = 1;
        }

        $invoice->update($data);

        // Логируем значимые изменения
        if ($oldCourierId !== $courierId) {
            $courierName = $courierId ? optional(Staff::find($courierId))->full_name : null;
            $this->logAdminEvent($invoice, 'courier_assigned', null, null, [
                'from_courier_id' => $oldCourierId,
                'to_courier_id' => $courierId,
                'courier_name' => $courierName,
            ]);
        }
        if ($oldReceivingCourierId !== $receivingCourierId) {
            $rcName = $receivingCourierId ? optional(Staff::find($receivingCourierId))->full_name : null;
            $this->logAdminEvent($invoice, 'receiving_courier_assigned', null, null, [
                'from_courier_id' => $oldReceivingCourierId,
                'to_courier_id' => $receivingCourierId,
                'courier_name' => $rcName,
            ]);
        }
        if ($oldDetail !== (int) $data['detail_status']) {
            $this->logAdminEvent($invoice, 'detail_changed', $oldDetail, (int) $data['detail_status']);
        }
        if ($oldStatus !== (int) $data['status']) {
            $this->logAdminEvent($invoice, 'status_changed', null, null, [
                'from_status' => $oldStatus,
                'to_status' => (int) $data['status'],
            ]);
        }
        if (isset($data['date']) && (string) $oldDate !== (string) $data['date']) {
            $this->logAdminEvent($invoice, 'date_changed', null, null, [
                'from_date' => (string) $oldDate,
                'to_date' => (string) $data['date'],
            ]);
        }

        return redirect('/admin/invoices/view/' . $id)->with('success', 'Данные сохранены');
    }

    private function logAdminEvent(Invoice $invoice, string $event, ?int $fromDetail, ?int $toDetail, array $meta = []): void
    {
        $role = session('role');
        InvoiceEvent::create([
            'invoice_id' => $invoice->id,
            'event' => $event,
            'from_detail_status' => $fromDetail,
            'to_detail_status' => $toDetail,
            'actor_type' => $role === 'admin' ? 'admin' : 'staff',
            'actor_id' => session('staff_id'),
            'actor_role' => $role,
            'actor_name' => $role === 'admin' ? 'Администратор' : session('full_name'),
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }

    // === ORDERS ===
    public function orders()
    {
        if ($r = $this->checkAuth()) return $r;
        $orders = TmOrder::orderBy('id', 'desc')->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function createOrder()
    {
        if ($r = $this->checkAuth()) return $r;
        return view('admin.orders.form');
    }

    public function storeOrder(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        TmOrder::create($request->only('track_number', 'name_from', 'name_to', 'date_from', 'date_to', 'status'));
        return redirect('/admin/orders')->with('success', 'Заказ добавлен');
    }

    public function editOrder($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $order = TmOrder::findOrFail($id);
        return view('admin.orders.form', compact('order'));
    }

    public function updateOrder(Request $request, $id)
    {
        if ($r = $this->checkAuth()) return $r;
        TmOrder::where('id', $id)->update($request->only('track_number', 'name_from', 'name_to', 'date_from', 'date_to', 'status'));
        return redirect('/admin/orders')->with('success', 'Заказ обновлён');
    }

    public function deleteOrder($id)
    {
        if ($r = $this->checkAuth()) return $r;
        TmOrder::where('id', $id)->delete();
        return redirect('/admin/orders')->with('success', 'Заказ удалён');
    }

    // === NEWS ===
    public function news()
    {
        if ($r = $this->checkAuth()) return $r;
        $news = TmNews::orderBy('id', 'desc')->get();
        return view('admin.news.index', compact('news'));
    }

    public function createNews()
    {
        if ($r = $this->checkAuth()) return $r;
        return view('admin.news.form');
    }

    public function storeNews(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        TmNews::create([
            'title' => $request->input('title'),
            'discription' => $request->input('discription', ''),
            'text' => $request->input('text', ''),
            'img' => $request->input('img', ''),
            'date' => $request->input('date'),
            'mt' => '', 'md' => '', 'thumb_571_315' => '', 'visible' => 0,
            'title_eng' => '', 'discription_eng' => '', 'text_eng' => '',
        ]);
        return redirect('/admin/news')->with('success', 'Новость добавлена');
    }

    public function editNews($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $item = TmNews::findOrFail($id);
        return view('admin.news.form', compact('item'));
    }

    public function updateNews(Request $request, $id)
    {
        if ($r = $this->checkAuth()) return $r;
        TmNews::where('id', $id)->update([
            'title' => $request->input('title'),
            'discription' => $request->input('discription', ''),
            'text' => $request->input('text', ''),
            'img' => $request->input('img', ''),
            'date' => $request->input('date'),
        ]);
        return redirect('/admin/news')->with('success', 'Новость обновлена');
    }

    public function deleteNews($id)
    {
        if ($r = $this->checkAuth()) return $r;
        TmNews::where('id', $id)->delete();
        return redirect('/admin/news')->with('success', 'Новость удалена');
    }

    // === PAGES ===
    public function pages()
    {
        if ($r = $this->checkAuth()) return $r;
        $pages = TmPage::orderBy('sort')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function editPage($id)
    {
        if ($r = $this->checkAuth()) return $r;
        $page = TmPage::findOrFail($id);
        return view('admin.pages.form', compact('page'));
    }

    public function updatePage(Request $request, $id)
    {
        if ($r = $this->checkAuth()) return $r;
        TmPage::where('id', $id)->update($request->only('title', 'header', 'alias', 'mt', 'md', 'text'));
        return redirect('/admin/pages')->with('success', 'Страница обновлена');
    }

    // === CITIES ===
    public function cities()
    {
        if ($r = $this->checkAuth()) return $r;
        $cities = CityDelivery::orderBy('title')->get();
        return view('admin.cities', compact('cities'));
    }

    public function storeCity(Request $request)
    {
        if ($r = $this->checkAuth()) return $r;
        CityDelivery::create(['title' => $request->input('title')]);
        return redirect('/admin/cities')->with('success', 'Город добавлен');
    }

    public function deleteCity($id)
    {
        if ($r = $this->checkAuth()) return $r;
        CityDelivery::where('id', $id)->delete();
        return redirect('/admin/cities')->with('success', 'Город удалён');
    }

    // === TARIFFS ===
    public function tariffs($type)
    {
        if ($r = $this->checkAuth()) return $r;
        $model = $this->getTariffModel($type);
        if (!$model) abort(404);
        $tariffs = $model::with(['cityFrom', 'cityTo'])->get();
        $cities = CityDelivery::orderBy('title')->get();
        $names = ['avto' => 'Авто', 'avia' => 'Авиа', 'zh' => 'Ж/Д'];
        $typeName = $names[$type] ?? $type;
        return view('admin.tariffs', compact('tariffs', 'cities', 'type', 'typeName'));
    }

    public function storeTariff(Request $request, $type)
    {
        if ($r = $this->checkAuth()) return $r;
        $model = $this->getTariffModel($type);
        if (!$model) abort(404);
        $model::create($request->only('city_from', 'city_to', 'price', 'time'));
        return redirect('/admin/tariffs/' . $type)->with('success', 'Тариф добавлен');
    }

    public function deleteTariff($type, $id)
    {
        if ($r = $this->checkAuth()) return $r;
        $model = $this->getTariffModel($type);
        if (!$model) abort(404);
        $model::where('id', $id)->delete();
        return redirect('/admin/tariffs/' . $type)->with('success', 'Тариф удалён');
    }

    private function getTariffModel($type)
    {
        return match ($type) {
            'avto' => new Avto, 'avia' => new Avia, 'zh' => new Zh, default => null,
        };
    }
}
