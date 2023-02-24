<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

// Auth::routes();
Route::get('/login/admin', [App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/login/admin', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin']);
Route::get('/admin/logout', [App\Http\Controllers\Auth\LoginController::class, 'admin_logout'])->name('admin.logout');

Route::group(['middleware' => 'Language'], function () {
    Route::get('/admin', [App\Http\Controllers\Admin\AdminController::class, 'Index'])->name('admin.Index');
    Route::get('/change-language/{lang}', [App\Http\Controllers\Admin\AdminController::class, 'changeLang'])->name('admin.changeLang');

    //------------------Admin/Administrators--
    Route::get('/admin/list', [App\Http\Controllers\Admin\AdminController::class, 'listAdmin'])->name('admin.list');
    Route::get('/admin/create', [App\Http\Controllers\Admin\AdminController::class, 'create'])->name('admin.create');
    Route::post('/admin/create', [App\Http\Controllers\Admin\AdminController::class, 'store'])->name('admin.create.store');
    Route::get('/admin/view/{id}', [App\Http\Controllers\Admin\AdminController::class, 'show'])->name('admin.show');
    Route::get('/admin/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admin/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'update'])->name('admin.edit');
    Route::post('/admin/delete/{id}', [App\Http\Controllers\Admin\AdminController::class, 'destroy'])->name('admin.delete');

    //------------------Admin/Administrators/Roles--
    Route::get('/admin/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles');
    Route::get('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.create');
    Route::get('/roles/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.edit');
    Route::post('/roles/delete/{id}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.delete');

    //------------------Admin/Administrators/Permissions--
    Route::get('/admin/permissions', [App\Http\Controllers\Admin\PermissionsController::class, 'index'])->name('admin.permissions');
    Route::get('/permissions/create', [App\Http\Controllers\Admin\PermissionsController::class, 'create'])->name('permissions.create');
    Route::post('/permissions/create', [App\Http\Controllers\Admin\PermissionsController::class, 'store'])->name('permissions.create');
    Route::get('/permissions/edit/{id}', [App\Http\Controllers\Admin\PermissionsController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/edit/{id}', [App\Http\Controllers\Admin\PermissionsController::class, 'update'])->name('permissions.edit');
    Route::post('/permissions/delete/{id}', [App\Http\Controllers\Admin\PermissionsController::class, 'destroy'])->name('permissions.delete');

    //------------------Admin/Profile--
    Route::get('/admin/profile', [App\Http\Controllers\Admin\AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/profile', [App\Http\Controllers\Admin\AdminController::class, 'updateprofile'])->name('admin.updateprofile');
    Route::post('/admin/change-password', [App\Http\Controllers\Admin\AdminController::class, 'changePassword'])->name('admin.changePassword');
    Route::get('/access_restricted', [App\Http\Controllers\Admin\AdminController::class, 'access_restricted'])->name('admin.access_restricted');

    //------------------Admin/GeneralSettings--
    Route::get('/admin/settings', [App\Http\Controllers\Admin\GeneralsettingsController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [App\Http\Controllers\Admin\GeneralsettingsController::class, 'storesettings'])->name('admin.settings');
    Route::post('/admin/settings/removeimage', [App\Http\Controllers\Admin\GeneralsettingsController::class, 'remove_image'])->name('admin.settings.removeImage');

    //------------------Admin/Products/services--
    Route::get('/admin/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('admin.services');
    Route::get('/services/create', [App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
    Route::post('/services/create', [App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.create');
    Route::get('/services/edit/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('services.edit');
    Route::post('/services/edit/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('services.update');
    Route::post('/services/delete/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/services/removeimage', [App\Http\Controllers\Admin\ServiceController::class, 'remove_image'])->name('services.removeImage');
    Route::get('/countries/search', [App\Http\Controllers\Admin\ServiceController::class, 'searchContries'])->name('countries.search');
    Route::get('/services/view/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'show'])->name('services.view');
    Route::post('/service/changestatus', [App\Http\Controllers\Admin\ServiceController::class, 'changestatus'])->name('service.changestatus');

    Route::get('/admin/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers');
    Route::post('/customers/changeStatus', [App\Http\Controllers\Admin\CustomerController::class, 'changeStatus'])->name('customers.changeStatus');
    Route::get('/customers/view/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.view');

    //..........................Admin/Packages
    Route::post('/admin/packages/store', [App\Http\Controllers\Admin\ServiceController::class, 'packages_store'])->name('store.packages');
    // Route::post('/admin/packages/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'packages_update'])->name('update.packages');
    Route::post('package/delete/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'package_destroy'])->name('package.destroy');
    Route::post('package/update/(id}', [App\Http\Controllers\Admin\ServiceController::class, 'package_update'])->name('update.package');

    //...................Admin/subservice
    Route::get('/admin/sub_services', [App\Http\Controllers\Admin\ServiceController::class, 'sub_services_index'])->name('admin.sub_services');
    Route::get('/services/sub_services', [App\Http\Controllers\Admin\ServiceController::class, 'sub_services_create'])->name('sub_services.create');
    Route::post('/services/sub_services', [App\Http\Controllers\Admin\ServiceController::class, 'sub_services_store'])->name('sub_services.store');

    //...................Admin/document
    Route::post('/admin/document/store', [App\Http\Controllers\Admin\ServiceController::class, 'document_store'])->name('store.document');
    Route::post('document/update/(id}', [App\Http\Controllers\Admin\ServiceController::class, 'document_update'])->name('update.document');
    Route::post('document/delete/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'document_destroy'])->name('document.destroy');

    //...................Admin/service man
    Route::get('/admin/service_man', [App\Http\Controllers\Admin\ServiceManController::class, 'index'])->name('admin.service_man');
    Route::post('/service_man/changeStatus', [App\Http\Controllers\Admin\ServiceManController::class, 'changeStatus'])->name('serviceman.changeStatus');
    Route::get('/service_man/view/{id}', [App\Http\Controllers\Admin\ServiceManController::class, 'show'])->name('serviceman.view');

    //...................Admin/couponcode
    Route::get('admin/coupons', [App\Http\Controllers\Admin\CouponCodeController::class, 'index'])->name('admin.coupons');
    Route::get('admin/coupons/create', [App\Http\Controllers\Admin\CouponCodeController::class, 'create'])->name('coupons.create');
    Route::post('admin/coupons/store', [App\Http\Controllers\Admin\CouponCodeController::class, 'store'])->name('coupons.store');
    Route::get('admin/coupons/{id}', [App\Http\Controllers\Admin\CouponCodeController::class, 'edit'])->name('coupons.edit');
    Route::post('admin/coupons/{id}', [App\Http\Controllers\Admin\CouponCodeController::class, 'update'])->name('coupons.update');
    Route::post('coupons/delete/{id}', [App\Http\Controllers\Admin\CouponCodeController::class, 'destroy'])->name('coupons.delete');

    //...................Admin/tax
    Route::get('/admin/taxes', [App\Http\Controllers\Admin\TaxController::class, 'index'])->name('admin.taxes');
    Route::post('/taxes/create', [App\Http\Controllers\Admin\TaxController::class, 'store'])->name('taxes.create');
    Route::post('/taxes/update', [App\Http\Controllers\Admin\TaxController::class, 'update'])->name('taxes.update');
    Route::post('/taxes/changestatus', [App\Http\Controllers\Admin\TaxController::class, 'changestatus'])->name('taxes.changestatus');

    //...................Admin/home slider
    Route::get('admin/homesliders', [App\Http\Controllers\Admin\HomesliderController::class, 'index'])->name('admin.homesliders');
    Route::get('homesliders/create', [App\Http\Controllers\Admin\HomesliderController::class, 'create'])->name('homesliders.create');
    Route::post('homesliders/create', [App\Http\Controllers\Admin\HomesliderController::class, 'store'])->name('homesliders.create');
    Route::get('homesliders/edit/{id}', [App\Http\Controllers\Admin\HomesliderController::class, 'edit'])->name('homesliders.edit');
    Route::post('homesliders/edit/{id}', [App\Http\Controllers\Admin\HomesliderController::class, 'update'])->name('homesliders.update');
    Route::get('homesliders/view/{id}', [App\Http\Controllers\Admin\HomesliderController::class, 'show'])->name('homesliders.show');
    Route::post('homesliders/removeMedia', [App\Http\Controllers\Admin\HomesliderController::class, 'removeMedia'])->name('homesliders.removeMedia');
    Route::post('homesliders/delete/{id}', [App\Http\Controllers\Admin\HomesliderController::class, 'destroy'])->name('homesliders.destroy');
    Route::post('/homesliders/changestatus', [App\Http\Controllers\Admin\HomesliderController::class, 'changestatus'])->name('homesliders.changestatus');

    //........................Admin/order
    Route::get('/admin/order', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.order');
    Route::get('/order/view/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('order.show');

    //.........................Admin/Subscription
    Route::get('/admin/subscription', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('admin.subscription');
    Route::get('/subscription/view/{id}', [App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('subscription.show');
    Route::post('/subscription/changestatus', [App\Http\Controllers\Admin\SubscriptionController::class, 'changestatus'])->name('subscription.changestatus');

    Route::get('/reported/customer', [App\Http\Controllers\Admin\CustomerController::class, 'reported_customer'])->name('admin.reported.customer');
//--------------------Admin/reports
    Route::get('admin/reports/customerreports', [App\Http\Controllers\Admin\ReportsController::class, 'customer_reports'])->name('admin.reports.customer');
    Route::get('admin/reports/servicemanreports', [App\Http\Controllers\Admin\ReportsController::class, 'serviceman_reports'])->name('admin.reports.serviceman');
    Route::get('admin/reports/subscriptionreports', [App\Http\Controllers\Admin\ReportsController::class, 'subscription_reports'])->name('admin.reports.subscription');

});
