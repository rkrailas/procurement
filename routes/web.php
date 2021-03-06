<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\PurchaseRequisitionList;
use App\Http\Livewire\PurchaseRequisitionDetails;
use App\Http\Livewire\RequisitionInbox;
use App\Http\Controllers\Form\PRForm;

use App\Http\Livewire\RfqList;
use App\Http\Livewire\RfqDetail;
use App\Http\Livewire\RfqInbox;

use App\Http\Livewire\PurchaseOrderList;
use App\Http\Livewire\PurchaseOrderDetails;

use App\Http\Livewire\admin\ChangePassword;
use App\Http\Controllers\ClientController;


//for Test
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Livewire\Test1;
use App\Http\Livewire\Test2;
use App\Http\Controllers\PHPJasperController;
use App\Http\Livewire\GoodsReceipt;
use App\Http\Livewire\GoodsReceiptDetails;
use App\Http\Livewire\UserList;
use App\Http\Livewire\UserForm;
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

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    //======== Purchase Requisition ========
    Route::get('purchaserequisitionlist', PurchaseRequisitionList::class)->name('purchaserequisitionlist');
    Route::get('purchaserequisitiondetails', PurchaseRequisitionDetails::class)->name('purchaserequisitiondetails');
    Route::get('requisitioninbox', RequisitionInbox::class)->name('requisitioninbox');
    Route::get('rfqlist', RfqList::class)->name('rfqlist');
    Route::get('rfqdetail', RfqDetail::class)->name('rfqdetail');
    // Route::get('rfqinbox', Rfqinbox::class)->name('rfqinbox');
    Route::get('PRForm/{prno}', [PRForm::class,'genForm']);
    // Route::get('purchaseorderlist', PurchaseOrderList::class)->name('purchaseorderlist');
    // Route::get('purchaseorderdetails', PurchaseOrderDetails::class)->name('purchaseorderdetails');

    Route::get('datarequestorforseleect2', [PurchaseRequisitionList::class, 'dataRequestorForSeleect2'])->name('datarequestorforseleect2');
    Route::get('datadecidervalidatorforseleect2', [PurchaseRequisitionDetails::class, 'dataDeciderValidatorForSeleect2'])->name('datadecidervalidatorforseleect2');
    Route::get('datarequested_forforseleect2', [PurchaseRequisitionDetails::class, 'dataRequested_forForSeleect2'])->name('datarequested_forforseleect2');

    Route::prefix('user_management')->group(function () {
        Route::get('/list', UserList::class)->name('list_user');
        Route::get('/form', UserForm::class)->name('form_user');
    });
});


Route::get('admin/changepassword', ChangePassword::class)->name('admin.changepassword');
Route::post('logout', [ClientController::class, 'logout'])->name('logout');


//for Test
Route::get('test1', Test1::class)->name('test1');
Route::get('test2', Test2::class)->name('test2');
Route::get('PHPJasper/{prno}', [PHPJasperController::class,'genReport'])->name('PHPJasper');
Route::get('dataforselect2', [Test1::class, 'getdataforselect2'])->name('dataforselect2');
