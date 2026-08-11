<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Админ-панель') | Logexim Express</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/favicon.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Nunito', sans-serif; background: #f4f6f9; color: #333; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #1a1a2e; color: #fff; padding: 0; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-logo { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo img { height: 40px; }
        .sidebar-logo span { display: block; font-size: 12px; color: #aaa; margin-top: 5px; }
        .sidebar-menu { list-style: none; padding: 15px 0; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #ccc; text-decoration: none; font-size: 14px; transition: all 0.2s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar-menu li a i { width: 20px; text-align: center; }
        .main-content { margin-left: 250px; flex: 1; }
        .topbar { background: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .topbar-right a { color: #D0171C; text-decoration: none; font-weight: 600; }
        .content { padding: 25px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .card-header { padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: 700; font-size: 16px; }
        .card-body { padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fa; padding: 10px 12px; text-align: left; font-size: 13px; color: #666; border-bottom: 2px solid #eee; }
        table td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        table tr:hover { background: #fafafa; }
        .btn { padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #D0171C; color: #fff; }
        .btn-primary:hover { background: #a21216; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .btn-sm { padding: 5px 12px; font-size: 12px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .form-control:focus { border-color: #D0171C; outline: none; }
        textarea.form-control { resize: vertical; }
        .alert { padding: 12px 18px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .stat-card h3 { font-size: 28px; color: #D0171C; }
        .stat-card p { font-size: 13px; color: #888; margin-top: 5px; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; color: #fff; }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="/images/new-logo.png" alt="Logo">
            <span>Админ-панель</span>
        </div>
        <ul class="sidebar-menu">
            @php $role = session('role'); @endphp
            @if($role === 'admin')
                <li><a href="/admin"><i class="fas fa-home"></i> Дашборд</a></li>
                <li><a href="/admin/users"><i class="fas fa-users"></i> Пользователи</a></li>
                <li><a href="/admin/staff"><i class="fas fa-user-tie"></i> Сотрудники</a></li>
            @endif
            @php
                $newInvoicesCount = \App\Models\Invoice::where('status', 0)
                    ->visibleToStaff(session('role'), (int) session('staff_id'))
                    ->count();
            @endphp
            <li><a href="/admin/invoices"><i class="fas fa-file-invoice"></i> Накладные <span id="invoice-badge" style="background:#D0171C;color:#fff;font-size:11px;padding:2px 7px;border-radius:10px;margin-left:5px;{{ $newInvoicesCount > 0 ? '' : 'display:none' }}">{{ $newInvoicesCount }}</span></a></li>
            @if($role === 'admin')
                <li><a href="/admin/orders"><i class="fas fa-truck"></i> Заказы/Трекинг</a></li>
                <li><a href="/admin/news"><i class="fas fa-newspaper"></i> Новости</a></li>
                <li><a href="/admin/pages"><i class="fas fa-file-alt"></i> Страницы</a></li>
                <li><a href="/admin/cities"><i class="fas fa-city"></i> Города</a></li>
                <li><a href="/admin/tariffs/avto"><i class="fas fa-car"></i> Тарифы Авто</a></li>
                <li><a href="/admin/tariffs/avia"><i class="fas fa-plane"></i> Тарифы Авиа</a></li>
                <li><a href="/admin/tariffs/zh"><i class="fas fa-train"></i> Тарифы Ж/Д</a></li>
            @endif
            <li><a href="/admin/logout" style="color:#ff6b6b"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
        </ul>
    </aside>
    <div class="main-content">
        <div class="topbar">
            <div><strong>@yield('title', 'Админ-панель')</strong></div>
            <div class="topbar-right">
                @php
                    $role = session('role');
                    if ($role === 'admin') {
                        $who = 'Администратор';
                    } elseif (isset(\App\Models\Staff::ROLE_LABELS[$role])) {
                        $who = session('full_name') . ' (' . mb_strtolower(\App\Models\Staff::ROLE_LABELS[$role]) . ')';
                    } else {
                        $who = '';
                    }
                @endphp
                @if($who)<span style="color:#666;margin-right:15px">{{ $who }}</span>@endif
                <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> На сайт</a>
            </div>
        </div>
        <div class="content">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});</script>
<style>@keyframes fadeIn{from{opacity:0;background:#fff3cd}to{opacity:1;background:#fff8e1}}</style>
<script>
(function(){
    @php
        $initialLastId = \App\Models\Invoice::visibleToStaff(session('role'), (int) session('staff_id'))
            ->max('id') ?: 0;
    @endphp
    var lastId = {{ $initialLastId }};
    setInterval(function(){
        $.get('/admin/invoices/check-new', {last_id: lastId}, function(data){
            // Обновляем бейдж
            var badge = document.getElementById('invoice-badge');
            if(data.count > 0){ badge.textContent = data.count; badge.style.display = ''; }
            else { badge.style.display = 'none'; }
            // Если есть новые и мы на странице накладных — добавляем в таблицу
            if(data.has_new && data.html){
                var tbody = document.querySelector('#invoices-table table tbody');
                if(tbody){
                    var temp = document.createElement('table');
                    temp.innerHTML = '<tbody>' + data.html + '</tbody>';
                    var newRows = temp.querySelector('tbody').querySelectorAll('tr');
                    var firstRow = tbody.querySelector('tr');
                    for(var i = newRows.length - 1; i >= 0; i--){
                        if(firstRow){
                            tbody.insertBefore(newRows[i], firstRow);
                        } else {
                            tbody.appendChild(newRows[i]);
                        }
                    }
                }
                lastId = data.max_id;
            }
        });
    }, 10000);
})();
</script>
@stack('scripts')
</body>
</html>
