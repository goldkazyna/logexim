<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AjaxController;

Route::get('/', [HomeController::class, 'index']);

// Privacy policy (App Store requirement) — must be before the /{alias} catch-all
Route::view('/privacy', 'privacy');

// AJAX routes
Route::post('/ajax/searchCityDelivery', [AjaxController::class, 'searchCityDelivery']);
Route::post('/ajax/calcDeliveryCar', [AjaxController::class, 'calcDeliveryCar']);
Route::post('/ajax/calcDeliveryAir', [AjaxController::class, 'calcDeliveryAir']);
Route::post('/ajax/calcDeliveryZd', [AjaxController::class, 'calcDeliveryZd']);
Route::post('/ajax/searchTrack', [AjaxController::class, 'searchTrack']);
Route::post('/ajax/send_from', [AjaxController::class, 'sendFrom']);

// Cabinet
use App\Http\Controllers\CabinetController;
Route::get('/cabinet/auth', [CabinetController::class, 'authForm']);
Route::post('/cabinet/auth', [CabinetController::class, 'auth']);
Route::get('/cabinet/registration', [CabinetController::class, 'registrationForm']);
Route::post('/cabinet/registration', [CabinetController::class, 'registration']);
Route::get('/cabinet/success', [CabinetController::class, 'success']);
Route::get('/cabinet/logout', [CabinetController::class, 'logout']);
Route::get('/cabinet/recovery', [CabinetController::class, 'recoveryForm']);
Route::post('/cabinet/recovery', [CabinetController::class, 'recovery']);
Route::get('/cabinet/success_recovery', [CabinetController::class, 'successRecovery']);
Route::get('/cabinet/newpassword/{restore_code}', [CabinetController::class, 'newPasswordForm']);
Route::post('/cabinet/newpassword/{restore_code}', [CabinetController::class, 'newPassword']);
Route::get('/cabinet', [CabinetController::class, 'index']);
Route::get('/cabinet/profile_settings', [CabinetController::class, 'profileSettings']);
Route::post('/cabinet/update_profile', [CabinetController::class, 'updateProfile']);
Route::get('/cabinet/invoices', [CabinetController::class, 'invoices']);
Route::get('/cabinet/create_invoice', [CabinetController::class, 'createInvoice']);
Route::post('/cabinet/save_invoices', [CabinetController::class, 'saveInvoice']);
Route::get('/cabinet/view_invoice/{id}', [CabinetController::class, 'viewInvoice']);
Route::get('/cabinet/print_invoice/{id}', [CabinetController::class, 'printInvoice']);
Route::get('/cabinet/print_invoices', [CabinetController::class, 'printInvoices']);
Route::get('/cabinet/recipient_templates', [CabinetController::class, 'recipientTemplates']);
Route::get('/cabinet/add_recipient_templates', [CabinetController::class, 'addRecipientTemplate']);
Route::post('/cabinet/save_recipient_template', [CabinetController::class, 'saveRecipientTemplate']);
Route::get('/cabinet/edit_recipient_templates/{id}', [CabinetController::class, 'editRecipientTemplate']);
Route::post('/cabinet/update_recipient_template', [CabinetController::class, 'updateRecipientTemplate']);
Route::post('/cabinet/delete_recipient_template', [CabinetController::class, 'deleteRecipientTemplate']);
Route::post('/cabinet/get_recipient_template', [CabinetController::class, 'getRecipientTemplate']);
Route::get('/cabinet/description_templates', [CabinetController::class, 'descriptionTemplates']);
Route::get('/cabinet/add_description_templates', [CabinetController::class, 'addDescriptionTemplate']);
Route::post('/cabinet/save_description_template', [CabinetController::class, 'saveDescriptionTemplate']);
Route::get('/cabinet/edit_description_templates/{id}', [CabinetController::class, 'editDescriptionTemplate']);
Route::post('/cabinet/update_description_template', [CabinetController::class, 'updateDescriptionTemplate']);
Route::post('/cabinet/delete_description_template', [CabinetController::class, 'deleteDescriptionTemplate']);
Route::post('/cabinet/get_description_template', [CabinetController::class, 'getDescriptionTemplate']);
Route::get('/cabinet/reports', [CabinetController::class, 'reports']);
Route::get('/cabinet/export_invoices', [CabinetController::class, 'exportInvoices']);

// Admin
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
Route::get('/admin', [AdminController::class, 'authForm']);
Route::post('/admin/auth', [AdminController::class, 'auth']);
Route::get('/admin/logout', [AdminController::class, 'logout']);
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/users', [AdminController::class, 'users']);
Route::get('/admin/users/activate/{id}', [AdminController::class, 'activateUser']);
Route::get('/admin/users/deactivate/{id}', [AdminController::class, 'deactivateUser']);
Route::get('/admin/invoices/check-new', [AdminController::class, 'checkNewInvoices']);
Route::get('/admin/invoices', [AdminController::class, 'invoices']);
Route::get('/admin/invoices/view/{id}', [AdminController::class, 'viewInvoice']);
Route::post('/admin/invoices/status/{id}', [AdminController::class, 'updateInvoiceStatus']);
Route::post('/admin/invoices/update/{id}', [AdminController::class, 'updateInvoice']);
Route::get('/admin/orders', [AdminController::class, 'orders']);
Route::get('/admin/orders/create', [AdminController::class, 'createOrder']);
Route::post('/admin/orders/store', [AdminController::class, 'storeOrder']);
Route::get('/admin/orders/edit/{id}', [AdminController::class, 'editOrder']);
Route::post('/admin/orders/update/{id}', [AdminController::class, 'updateOrder']);
Route::get('/admin/orders/delete/{id}', [AdminController::class, 'deleteOrder']);
Route::get('/admin/news', [AdminController::class, 'news']);
Route::get('/admin/news/create', [AdminController::class, 'createNews']);
Route::post('/admin/news/store', [AdminController::class, 'storeNews']);
Route::get('/admin/news/edit/{id}', [AdminController::class, 'editNews']);
Route::post('/admin/news/update/{id}', [AdminController::class, 'updateNews']);
Route::get('/admin/news/delete/{id}', [AdminController::class, 'deleteNews']);
Route::get('/admin/pages', [AdminController::class, 'pages']);
Route::get('/admin/pages/edit/{id}', [AdminController::class, 'editPage']);
Route::post('/admin/pages/update/{id}', [AdminController::class, 'updatePage']);
Route::get('/admin/cities', [AdminController::class, 'cities']);
Route::post('/admin/cities/store', [AdminController::class, 'storeCity']);
Route::get('/admin/cities/delete/{id}', [AdminController::class, 'deleteCity']);
Route::get('/admin/tariffs/{type}', [AdminController::class, 'tariffs'])->where('type', 'avto|avia|zh');
Route::post('/admin/tariffs/{type}/store', [AdminController::class, 'storeTariff'])->where('type', 'avto|avia|zh');
Route::get('/admin/tariffs/{type}/delete/{id}', [AdminController::class, 'deleteTariff'])->where('type', 'avto|avia|zh');

// Admin / Staff (доступ только для role=admin)
Route::get('/admin/staff', [StaffController::class, 'index']);
Route::get('/admin/staff/create', [StaffController::class, 'create']);
Route::post('/admin/staff', [StaffController::class, 'store']);
Route::get('/admin/staff/{id}/edit', [StaffController::class, 'edit'])->whereNumber('id');
Route::post('/admin/staff/{id}', [StaffController::class, 'update'])->whereNumber('id');
Route::post('/admin/staff/{id}/toggle', [StaffController::class, 'toggle'])->whereNumber('id');
Route::post('/admin/staff/{id}/delete', [StaffController::class, 'destroy'])->whereNumber('id');

// News
use App\Http\Controllers\NewsController;
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/detail/{id}', [NewsController::class, 'detail']);

// Static pages (catch-all, must be last)
use App\Http\Controllers\PageController;
Route::get('/{alias}', [PageController::class, 'show'])->where('alias', '[a-z0-9\-]+');
