<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\LeadStageController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\MdfController;
use App\Http\Controllers\MdfStatusController;
use App\Http\Controllers\MdfTypeController;
use App\Http\Controllers\MdfSubTypeController;
use App\Http\Controllers\PaymentWallController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\PaytmPaymentController;
use App\Http\Controllers\MercadoPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\CoingatePaymentController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripePaymentController;




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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


Route::get('/', [HomeController::class, 'index'])->middleware('XSS')->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('XSS', 'auth');   
Route::get('/check', [HomeController::class, 'check'])->middleware('XSS','auth')->name('check');

Route::controller(UserController::class)->middleware('auth','XSS')->group(function(){

    Route::get('profile', 'profile')->name('profile');
    Route::post('profile', 'updateProfile')->name('update.profile');
    Route::post('profile/password', 'updatePassword')->name('update.password');
    Route::delete('profile', 'deleteAvatar')->name('delete.avatar');
    Route::get('users', 'index')->name('users');
    
});
Route::controller(UserController::class)->middleware('auth','XSS')->group(function(){
    Route::post('users', 'store')->name('users.store');
    Route::get('users/create', 'create')->name('users.create');
    Route::get('users/edit/{id}', 'edit')->name('users.edit');
    Route::get('users/{id}dit', 'show')->name('users.show');
});

Route::any('/users/{id}', [UserController::class, 'update'])->middleware('auth','XSS')->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('auth','XSS')->name('users.destroy');
Route::get('/lang/create', [UserController::class, 'createLang'])->middleware('auth','XSS')->name('lang.create');
Route::get('/lang/{lang}', [UserController::class, 'lang'])->middleware('auth','XSS')->name('lang');
Route::post('/lang', [UserController::class, 'storeLang'])->middleware('auth','XSS')->name('lang.store');
Route::post('/lang/data/{lang}', [UserController::class, 'storeLangData'])->middleware('auth','XSS')->name('lang.store.data');
Route::get('/lang/change/{lang}', [UserController::class, 'changeLang'])->middleware('auth','XSS')->name('lang.change');
Route::delete('/lang/{id}', [UserController::class, 'destroyLang'])->middleware('auth','XSS')->name('lang.destroy');

Route::get('/settings', [SettingsController::class, 'index'])->middleware('auth','XSS')->name('settings');
Route::post('/settings', [SettingsController::class, 'store'])->middleware('auth','XSS')->name('settings.store');
Route::post('/test', [SettingsController::class, 'testEmail'])->middleware('auth','XSS')->name('test.email');
Route::get('/test', [SettingsController::class, 'testEmail'])->middleware('auth','XSS')->name('test.email');
Route::post('/test/send', [SettingsController::class, 'testEmailSend'])->middleware('auth','XSS')->name('test.email.send');
Route::post('/business-setting', [SettingsController::class, 'saveBusinessSettings'])->middleware('auth','XSS')->name('business.setting');
Route::post('/template-setting', [SettingsController::class, 'saveTemplateSettings'])->middleware('auth','XSS')->name('template.setting');
Route::post('/payment-settings', [SettingsController::class, 'adminPaymentSettings'])->middleware('auth','XSS')->name('payment.settings');
Route::post('/email-settings', [SettingsController::class, 'emailSettingStore'])->middleware('auth','XSS')->name('email.settings.store');
Route::post('/pusher-settings', [SettingsController::class, 'pusherSettingStore'])->middleware('auth','XSS')->name('pusher.settings.store');

// Deal Module
Route::post('/deals/user', [DealController::class, 'jsonUser'])->name('deal.user.json');
Route::post('/deals/order', [DealController::class, 'order'])->middleware('auth','XSS')->name('deals.order');
Route::post('/deals/change-pipeline', [DealController::class, 'changePipeline'])->middleware('auth','XSS')->name('deals.change.pipeline');
Route::post('/deals/change-deal-status/{id}', [DealController::class, 'changeStatus'])->middleware('auth','XSS')->name('deals.change.status');
Route::get('/deals/{id}/labels', [DealController::class, 'labels'])->middleware('auth','XSS')->name('deals.labels');
Route::post('/deals/{id}/labels', [DealController::class, 'labelStore'])->middleware('auth','XSS')->name('deals.labels.store');
Route::get('/deals/{id}/users', [DealController::class, 'userEdit'])->middleware('auth','XSS')->name('deals.users.edit');
Route::put('/deals/{id}/users', [DealController::class, 'userUpdate'])->middleware('auth','XSS')->name('deals.users.update');
Route::delete('/deals/{id}/users/{uid}', [DealController::class, 'userDestroy'])->middleware('auth','XSS')->name('deals.users.destroy');
Route::get('/deals/{id}/clients', [DealController::class, 'clientEdit'])->middleware('auth','XSS')->name('deals.clients.edit');
Route::put('/deals/{id}/clients', [DealController::class, 'clientUpdate'])->middleware('auth','XSS')->name('deals.clients.update');
Route::delete('/deals/{id}/clients/{uid}', [DealController::class, 'clientDestroy'])->middleware('auth','XSS')->name('deals.clients.destroy');
Route::get('/deals/{id}/products', [DealController::class, 'productEdit'])->middleware('auth','XSS')->name('deals.products.edit');
Route::put('/deals/{id}/products', [DealController::class, 'productUpdate'])->middleware('auth','XSS')->name('deals.products.update');
Route::delete('/deals/{id}/products/{uid}', [DealController::class, 'productDestroy'])->middleware('auth','XSS')->name('deals.products.destroy');
Route::get('/deals/{id}/sources', [DealController::class, 'sourceEdit'])->middleware('auth','XSS')->name('deals.sources.edit');
Route::put('/deals/{id}/sources', [DealController::class, 'sourceUpdate'])->middleware('auth','XSS')->name('deals.sources.update');
Route::delete('/deals/{id}/sources/{uid}', [DealController::class, 'sourceDestroy'])->middleware('auth','XSS')->name('deals.sources.destroy');
Route::post('/deals/{id}/file', [DealController::class, 'fileUpload'])->middleware('auth','XSS')->name('deals.file.upload');
Route::get('/deals/{id}/file/{fid}', [DealController::class, 'fileDownload'])->middleware('auth','XSS')->name('deals.file.download');
Route::delete('/deals/{id}/file/delete/{fid}', [DealController::class, 'fileDelete'])->middleware('auth','XSS')->name('deals.file.delete');
Route::post('/deals/{id}/note', [DealController::class, 'noteStore'])->middleware('auth','XSS')->name('deals.note.store');
Route::get('/deals/{id}/task', [DealController::class, 'taskCreate'])->middleware('auth','XSS')->name('deals.tasks.create');
Route::post('/deals/{id}/task', [DealController::class, 'taskStore'])->middleware('auth','XSS')->name('deals.tasks.store');
Route::get('/deals/{id}/task/{tid}/show', [DealController::class, 'taskShow'])->middleware('auth','XSS')->name('deals.tasks.show');
Route::get('/deals/{id}/task/{tid}/edit', [DealController::class, 'taskEdit'])->middleware('auth','XSS')->name('deals.tasks.edit');
Route::put('/deals/{id}/task/{tid}', [DealController::class, 'taskUpdate'])->middleware('auth','XSS')->name('deals.tasks.update');
Route::put('/deals/{id}/task_status/{tid}', [DealController::class, 'taskUpdateStatus'])->middleware('auth','XSS')->name('deals.tasks.update_status');
Route::delete('/deals/{id}/task/{tid}', [DealController::class, 'taskDestroy'])->middleware('auth','XSS')->name('deals.tasks.destroy');
Route::get('/deals/{id}/discussions', [DealController::class, 'discussionCreate'])->middleware('auth','XSS')->name('deals.discussions.create');
Route::post('/deals/{id}/discussions', [DealController::class, 'discussionStore'])->middleware('auth','XSS')->name('deals.discussion.store');
Route::get('/deals/{id}/permission/{cid}', [DealController::class, 'permission'])->middleware('auth','XSS')->name('deals.client.permission');
Route::put('/deals/{id}/permission/{cid}', [DealController::class, 'permissionStore'])->middleware('auth','XSS')->name('deals.client.permissions.store');
Route::get('/deals/list', [DealController::class, 'deal_list'])->middleware('auth','XSS')->name('deals.list');

// Deal Calls
Route::get('/deals/{id}/call', [DealController::class, 'callCreate'])->middleware('auth','XSS')->name('deals.calls.create');
Route::post('/deals/{id}/call', [DealController::class, 'callStore'])->middleware('auth','XSS')->name('deals.calls.store');
Route::get('/deals/{id}/call/{cid}/edit', [DealController::class, 'callEdit'])->middleware('auth','XSS')->name('deals.calls.edit');
Route::put('/deals/{id}/call/{cid}', [DealController::class, 'callUpdate'])->middleware('auth','XSS')->name('deals.calls.update');
Route::delete('/deals/{id}/call/{cid}', [DealController::class, 'callDestroy'])->middleware('auth','XSS')->name('deals.calls.destroy');

// Deal Email
Route::get('/deals/{id}/email', [DealController::class, 'emailCreate'])->middleware('auth','XSS')->name('deals.emails.create');
Route::post('/deals/{id}/email', [DealController::class, 'emailStore'])->middleware('auth','XSS')->name('deals.emails.store');
Route::resource('deals', DealController::class)->middleware('auth','XSS');

// end Deal Module

Route::get('/search', [UserController::class, 'search'])->name('search.json');
Route::post('/stages/order', [StageController::class, 'order'])->name('stages.order');
Route::post('/stages/json', [StageController::class, 'json'])->name('stages.json');

Route::resource('stages', StageController::class);
Route::resource('pipelines', PipelineController::class);
Route::resource('labels', LabelController::class);
Route::resource('sources', SourceController::class);
Route::resource('payments', PaymentController::class);
Route::resource('expense_categories', ExpenseCategoryController::class);
Route::resource('custom_fields', CustomFieldController::class);
Route::resource('products', ProductController::class);
Route::resource('expenses', ExpenseController::class);

Route::get('/invoices/payments', [InvoiceController::class, 'payments'])->middleware('auth','XSS')->name('invoices.payments');
Route::get('/invoices/{id}/products/{pid}', [InvoiceController::class, 'productEdit'])->middleware('auth','XSS')->name('invoices.products.edit');
Route::put('/invoices/{id}/products/{pid}', [InvoiceController::class, 'productUpdate'])->middleware('auth','XSS')->name('invoices.products.update');
Route::delete('/invoices/{id}/products/{pid}', [InvoiceController::class, 'productDelete'])->middleware('auth','XSS')->name('invoices.products.delete');
Route::get('/invoices/{id}/products', [InvoiceController::class, 'productAdd'])->middleware('auth','XSS')->name('invoices.products.add');
Route::post('/invoices/{id}/products', [InvoiceController::class, 'productStore'])->middleware('auth','XSS')->name('invoices.products.store');
Route::get('/invoices/{id}/payments', [InvoiceController::class, 'paymentAdd'])->middleware('auth','XSS')->name('invoices.payments.add');
Route::post('/invoices/{id}/payments', [InvoiceController::class, 'paymentStore'])->middleware('auth','XSS')->name('invoices.payments.store');
Route::get('/invoices/{id}/get_invoice', [InvoiceController::class, 'printInvoice'])->name('get.invoice');
Route::get('/invoices/pay/pdf/{id}', [InvoiceController::class, 'pdffrominvoice'])->name('invoice.download.pdf');
Route::get('/invoices/preview/{template}/{color}', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
Route::get('/invoice/pay/{invoice}', [InvoiceController::class, 'payinvoice'])->name('pay.invoice');


// Estimation
Route::get('/estimations/{id}/products/{pid}', [EstimationController::class, 'productEdit'])->middleware('auth','XSS')->name('estimations.products.edit');
Route::put('/estimations/{id}/products/{pid}', [EstimationController::class, 'productUpdate'])->middleware('auth','XSS')->name('estimations.products.update');
Route::delete('/estimations/{id}/products/{pid}', [EstimationController::class, 'productDelete'])->middleware('auth','XSS')->name('estimations.products.delete');
Route::get('/estimations/{id}/products', [EstimationController::class, 'productAdd'])->middleware('auth','XSS')->name('estimations.products.add');
Route::post('/estimations/{id}/products', [EstimationController::class, 'productStore'])->middleware('auth','XSS')->name('estimations.products.store');
Route::get('/estimations/{id}/get_estimation', [EstimationController::class, 'printEstimation'])->name('get.estimation');
Route::get('/estimations/preview/{template}/{color}', [EstimationController::class, 'previewEstimation'])->name('estimations.preview');
Route::get('/estimation/pay/{estimation}', [EstimationController::class, 'payestimation'])->name('pay.estimation');
Route::get('estimation/pay/pdf/{id}', [EstimationController::class, 'pdffromestimation'])->name('estimation.download.pdf');

// end Estimation

// Leads Module
Route::post('/lead_stages/order', [LeadStageController::class, 'order'])->name('lead_stages.order');
Route::resource('lead_stages', LeadStageController::class)->middleware('auth');

Route::post('/leads/json', [LeadController::class, 'json'])->name('leads.json');
Route::post('/leads/order', [LeadController::class, 'json'])->middleware('auth','XSS')->name('leads.order');
Route::get('/leads/list', [LeadController::class, 'lead_list'])->middleware('auth','XSS')->name('leads.list');
Route::post('/leads/{id}/file', [LeadController::class, 'fileUpload'])->middleware('auth','XSS')->name('leads.file.upload');
Route::get('/leads/{id}/file/{fid}', [LeadController::class, 'fileDownload'])->middleware('auth','XSS')->name('leads.file.download');
Route::delete('/leads/{id}/file/delete/{fid}', [LeadController::class, 'fileDelete'])->middleware('auth','XSS')->name('leads.file.delete');
Route::post('/leads/{id}/note', [LeadController::class, 'noteStore'])->middleware('auth')->name('leads.note.store');
Route::get('/leads/{id}/labels', [LeadController::class, 'labels'])->middleware('auth','XSS')->name('leads.labels');
Route::post('/leads/{id}/labels', [LeadController::class, 'labelStore'])->middleware('auth','XSS')->name('leads.labels.store');
Route::get('/leads/{id}/users', [LeadController::class, 'userEdit'])->middleware('auth','XSS')->name('leads.users.edit');
Route::put('/leads/{id}/users', [LeadController::class, 'userUpdate'])->middleware('auth','XSS')->name('leads.users.update');
Route::delete('/leads/{id}/users/{uid}', [LeadController::class, 'userDestroy'])->middleware('auth','XSS')->name('leads.users.destroy');
Route::get('/leads/{id}/products', [LeadController::class, 'productEdit'])->middleware('auth','XSS')->name('leads.products.edit');
Route::put('/leads/{id}/products', [LeadController::class, 'productUpdate'])->middleware('auth','XSS')->name('leads.products.update');
Route::delete('/leads/{id}/products/{uid}', [LeadController::class, 'productDestroy'])->middleware('auth','XSS')->name('leads.products.destroy');
Route::get('/leads/{id}/sources', [LeadController::class, 'sourceEdit'])->middleware('auth','XSS')->name('leads.sources.edit');
Route::put('/leads/{id}/sources', [LeadController::class, 'sourceUpdate'])->middleware('auth','XSS')->name('leads.sources.update');
Route::delete('/leads/{id}/sources/{uid}', [LeadController::class, 'sourceDestroy'])->middleware('auth','XSS')->name('leads.sources.destroy');
Route::get('/leads/{id}/discussions', [LeadController::class, 'discussionCreate'])->middleware('auth','XSS')->name('leads.discussions.create');
Route::post('/leads/{id}/discussions', [LeadController::class, 'discussionStore'])->middleware('auth','XSS')->name('leads.discussion.store');
Route::get('/leads/{id}/show_convert', [LeadController::class, 'showConvertToDeal'])->middleware('auth','XSS')->name('leads.convert.deal');
Route::post('/leads/{id}/convert', [LeadController::class, 'convertToDeal'])->middleware('auth','XSS')->name('leads.convert.to.deal');



// Lead Calls
Route::get('/leads/{id}/call', [LeadController::class, 'callCreate'])->middleware('auth','XSS')->name('leads.calls.create');
Route::post('/leads/{id}/call', [LeadController::class, 'callStore'])->middleware('auth')->name('leads.calls.store');
Route::get('/leads/{id}/call/{cid}/edit', [LeadController::class, 'callEdit'])->middleware('auth','XSS')->name('leads.calls.edit');
Route::put('/leads/{id}/call/{cid}', [LeadController::class, 'callUpdate'])->middleware('auth')->name('leads.calls.update');
Route::delete('/leads/{id}/call/{cid}', [LeadController::class, 'callDestroy'])->middleware('auth','XSS')->name('leads.calls.destroy');


// Lead Email
Route::get('/leads/{id}/email', [LeadController::class, 'emailCreate'])->middleware('auth','XSS')->name('leads.emails.create');
Route::post('/leads/{id}/email', [LeadController::class, 'emailStore'])->middleware('auth')->name('leads.emails.store');
Route::resource('leads', LeadController::class)->middleware('auth','XSS');

// end Leads Module

Route::get('/{uid}/notification/seen', [UserController::class, 'notificationSeen'])->name('notification.seen');

// Email Templates
Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->middleware('auth','XSS')->name('manage.email.language');
Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLangindex'])->middleware('auth','XSS')->name('manageemail.lang');
Route::put('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->middleware('auth')->name('store.email.language');
Route::put('email_template_status/{pid}', [EmailTemplateController::class, 'updateStatus'])->middleware('auth')->name('status.email.language');
Route::resource('email_template', EmailTemplateController::class)->middleware('auth','XSS');

// End Email Templates

Route::resource('invoices', InvoiceController::class)->middleware('auth','XSS');
Route::resource('taxes', TaxController::class)->middleware('auth','XSS');
Route::resource('clients', ClientController::class)->middleware('auth','XSS');
Route::resource('roles', RoleController::class)->middleware('auth','XSS');
Route::resource('permissions', PermissionController::class)->middleware('auth','XSS');
Route::resource('contract_type', ContractTypeController::class)->middleware('auth','XSS');
Route::resource('contract', ContractController::class)->middleware('auth','XSS');


Route::post('/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->middleware('auth','XSS')->name('contract.status');
Route::get('/contract/copy/{id}', [ContractController::class, 'copycontract'])->middleware('auth','XSS')->name('contracts.copy');
Route::post('/contract/copy/store', [ContractController::class, 'copycontractstore'])->middleware('auth','XSS')->name('contracts.copy.store');
Route::post('/contract/{id}/description', [ContractController::class, 'descriptionStore'])->middleware('auth')->name('contracts.description.store');
Route::post('/contract/{id}/file', [ContractController::class, 'fileUpload'])->middleware('auth','XSS')->name('contracts.file.upload');
Route::post('/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload'])->middleware('auth','XSS')->name('contracts.file.download');
Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class, 'fileDelete'])->middleware('auth','XSS')->name('contracts.file.delete');
Route::post('/contract/{id}/comment', [ContractController::class, 'commentStore'])->name('comment.store');
Route::get('/contract/{id}/comment', [ContractController::class, 'commentDestroy'])->name('comment.destroy');
Route::post('/contract/{id}/note', [ContractController::class, 'noteStore'])->middleware('auth')->name('contracts.note.store');
Route::get('/contract/{id}/note', [ContractController::class, 'noteDestroy'])->middleware('auth')->name('contracts.note.destroy');
Route::get('contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract');
Route::get('contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf');
Route::get('/signature/{id}', [ContractController::class, 'signature'])->middleware('auth','XSS')->name('signature');
Route::post('/signaturestore', [ContractController::class, 'signatureStore'])->middleware('auth','XSS')->name('signaturestore');
Route::get('/contract/{id}/mail', [ContractController::class, 'sendmailContract'])->name('send.mail.contract');

Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->middleware('auth','XSS')->name('apply.coupon');
Route::resource('coupons', CouponController::class);
Route::resource('estimations', EstimationController::class)->middleware('auth','XSS');

Route::post('/invoices/{id}/payment', [InvoiceController::class, 'addPayment'])->middleware('XSS')->name('client.invoice.payment');
Route::post('pay-with-paypal/{id}', [PaypalController::class, 'clientPayWithPaypal'])->middleware('XSS')->name('client.pay.with.paypal');
Route::get('get-payment-status/{id}/{amount}', [PaypalController::class, 'clientGetPaymentStatus'])->middleware('XSS')->name('client.get.payment.status');

// Form Builder
Route::resource('form_builder', FormBuilderController::class)->middleware('auth','XSS');

// Form link base view
Route::get('/form/{code}', [FormBuilderController::class, 'formView'])->middleware('XSS')->name('form.view');
Route::any('/form_view_store', [FormBuilderController::class, 'formViewStore'])->middleware('XSS')->name('form.view.store');

// Form Response
Route::get('/form_response/{id}', [FormBuilderController::class, 'viewResponse'])->middleware('auth','XSS')->name('form.response');
Route::get('/response/{id}', [FormBuilderController::class, 'responseDetail'])->middleware('auth','XSS')->name('response.detail');

// Form Field
Route::get('/form_builder/{id}/field', [FormBuilderController::class, 'fieldCreate'])->middleware('auth','XSS')->name('form.field.create');
Route::post('/form_builder/{id}/field', [FormBuilderController::class, 'fieldStore'])->middleware('auth','XSS')->name('form.field.store');
Route::get('/form_builder/{id}/field/{fid}/show', [FormBuilderController::class, 'fieldShow'])->middleware('auth','XSS')->name('form.field.show');
Route::get('/form_builder/{id}/field/{fid}/edit', [FormBuilderController::class, 'fieldEdit'])->middleware('auth','XSS')->name('form.field.edit');
Route::put('/form_builder/{id}/field/{fid}', [FormBuilderController::class, 'fieldUpdate'])->middleware('auth','XSS')->name('form.field.update');
Route::delete('/form_builder/{id}/field/{fid}', [FormBuilderController::class, 'fieldDestroy'])->middleware('auth','XSS')->name('form.field.destroy');

// Form Field Bind
Route::get('/form_field/{id}', [FormBuilderController::class, 'formFieldBind'])->middleware('auth','XSS')->name('form.field.bind');
Route::post('/form_field_store/{id}', [FormBuilderController::class, 'bindStore'])->middleware('auth','XSS')->name('form.bind.store');

// end Form Builder

// MDF Module
Route::delete('/mdf/{id}/products/{pid}', [MdfController::class, 'productDelete'])->middleware('auth','XSS')->name('mdf.products.delete');
Route::get('/mdf/{id}/products', [MdfController::class, 'productAdd'])->middleware('auth','XSS')->name('mdf.products.add');
Route::post('/mdf/{id}/products', [MdfController::class, 'productStore'])->middleware('auth','XSS')->name('mdf.products.store');
Route::get('/mdf/approved/{id}/payments/{type}', [MdfController::class, 'paymentApproved'])->middleware('auth','XSS')->name('mdf.payments.approved');
Route::post('/mdf/approved/{id}/payments', [MdfController::class, 'paymentApprovedStore'])->middleware('auth','XSS')->name('mdf.payments.approved.store');
Route::get('/mdf/{id}/get_mdf', [MdfController::class, 'printMDF'])->middleware('auth','XSS')->name('get.mdf');
Route::get('/mdf/preview/{template}/{color}', [MdfController::class, 'previewMDF'])->name('mdf.preview');
Route::post('/mdf/change-mdf-complete/{id}', [MdfController::class, 'changeComplete'])->middleware('auth','XSS')->name('mdf.change.complete');
Route::resource('mdf', MdfController::class)->middleware('auth','XSS');
Route::post('/mdf/event_type', [MdfController::class, 'jsonEvent'])->name('mdf.event.json');
Route::resource('mdf_status', MdfStatusController::class)->middleware('auth','XSS');
Route::resource('mdf_type', MdfTypeController::class)->middleware('auth','XSS');
Route::resource('mdf_sub_type', MdfSubTypeController::class)->middleware('auth','XSS');

// End MDF Module 
    

//================================= Invoice Payment Gateways  ====================================//


Route::post('/invoice-pay-with-paystack', [PaystackPaymentController::class, 'invoicePayWithPaystack'])->middleware('XSS')->name('invoice.pay.with.paystack');
Route::get('/invoice/paystack/{pay_id}/{invoice_id}', [PaystackPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.paystack');

Route::post('/invoice-pay-with-flaterwave', [FlutterwavePaymentController::class, 'invoicePayWithFlutterwave'])->middleware('XSS')->name('invoice.pay.with.flaterwave');
Route::get('/invoice/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvociePaymentStatus'])->name('invoice.flaterwave');

Route::post('/invoice-pay-with-razorpay', [RazorpayPaymentController::class, 'invoicePayWithRazorpay'])->middleware('XSS')->name('invoice.pay.with.razorpay');
Route::get('/invoice/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.razorpay');

Route::post('/invoice-pay-with-paytm', [PaytmPaymentController::class, 'invoicePayWithPaytm'])->middleware('XSS')->name('invoice.pay.with.paytm');
Route::post('/invoice/paytm/{invoice}', [PaytmPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.paytm');

Route::post('/invoice-pay-with-mercado', [MercadoPaymentController::class, 'invoicePayWithMercado'])->middleware('XSS')->name('invoice.pay.with.mercado');
Route::get('/invoice/mercado/{invoice}', [MercadoPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.mercado');

Route::post('/invoice-pay-with-mollie', [MolliePaymentController::class, 'invoicePayWithMollie'])->middleware('XSS')->name('invoice.pay.with.mollie');
Route::get('/invoice/mollie/{invoice}', [MolliePaymentController::class, 'getInvociePaymentStatus'])->name('invoice.mollie');

Route::post('/invoice-pay-with-skrill', [SkrillPaymentController::class, 'invoicePayWithSkrill'])->middleware('XSS')->name('invoice.pay.with.skrill');
Route::get('/invoice/skrill/{invoice}', [SkrillPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.skrill');

Route::post('/invoice-pay-with-coingate', [CoingatePaymentController::class, 'invoicePayWithCoingate'])->middleware('XSS')->name('invoice.pay.with.coingate');
Route::get('/invoice/coingate/{invoice}', [CoingatePaymentController::class, 'getInvociePaymentStatus'])->name('invoice.coingate');

Route::get('/invoice/error/{flag}/{invoice_id}', [PaymentWallController::class, 'invoiceerror'])->name('error.invoice.show');
Route::post('/invoicepayment', [PaymentWallController::class, 'invoicepay'])->name('paymentwall.invoice');
Route::post('/invoice-pay-with-paymentwall/{invoice}', [PaymentWallController::class, 'invoicePayWithPaymentWall'])->name('invoice-pay-with-paymentwall');

Route::get('/stripe-payment-status', [StripePaymentController::class, 'GetStripePaymentStatus'])->name('stripe.payment.status');


// ==========================================Import Export====================================

Route::get('client/import/export', [ClientController::class, 'fileImportExport'])->name('client.file.import');
Route::post('client/import', [ClientController::class, 'fileImport'])->name('client.import');
Route::get('client/export', [ClientController::class, 'fileExport'])->name('client.export');

Route::get('invoice/export', [InvoiceController::class, 'fileExport'])->name('invoice.export');

Route::get('estimation/export', [EstimationController::class, 'fileExport'])->name('estimation.export');

Route::get('product/import/export', [ProductController::class, 'fileImportExport'])->name('product.file.import');
Route::post('product/import', [ProductController::class, 'fileImport'])->name('product.import');
Route::get('product/export', [ProductController::class, 'fileExport'])->name('product.export');

//=======================================zoommeeting=================================

Route::post('/setting/saveZoomSettings', [SettingsController::class, 'saveZoomSettings'])->middleware('auth','XSS')->name('setting.ZoomSettings');

Route::any('zoommeeting/calendar', [ZoomMeetingController::class, 'calender'])->middleware('auth','XSS')->name('zoommeeting.calender');
Route::resource('zoommeeting', ZoomMeetingController::class)->middleware('auth','XSS');
	

//================================slack==================================
Route::post('setting/slack', [SettingsController::class, 'slack'])->name('slack.setting');


//==============================telegram===============================

Route::post('setting/telegram', [SettingsController::class, 'telegram'])->name('telegram.setting');


/*==================================Recaptcha====================================================*/

Route::post('/recaptcha-settings', [SettingsController::class, 'recaptchaSettingStore'])->middleware('auth','XSS')->name('recaptcha.settings.store');

Route::any('user-reset-password/{id}', [UserController::class, 'employeePassword'])->name('user.reset');
Route::post('user-reset-password/{id}', [UserController::class, 'employeePasswordReset'])->name('user.password.update');


/*==================================Storeage====================================================*/

Route::post('storage-settings', [SettingsController::class, 'storageSettingStore'])->middleware('auth','XSS')->name('storage.setting.store');
