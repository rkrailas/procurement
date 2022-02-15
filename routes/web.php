<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionList;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionDetails;
use App\Http\Livewire\PurchaseRequisition\RequisitionInbox;
use App\Http\Livewire\admin\ChangePassword;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DropzoneController;
use App\Http\Controllers\Form\PRForm;

//for Test
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Livewire\Test1;
use App\Http\Livewire\Test2;
use App\Http\Controllers\PHPJasperController;
use App\Http\Controllers\PageController;

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

Route::group(['middleware' => 'auth'], function () {
    //======== Purchase Requisition ========
    // Route::get('pr/pr_list', PurchaseRequisitionList::class)->name('pr.pr_list');
    Route::get('purchase-requisition/purchaserequisitionlist', PurchaseRequisitionList::class)->name('purchase-requisition.purchaserequisitionlist');
    Route::get('purchase-requisition/purchaserequisitiondetails', PurchaseRequisitionDetails::class)->name('purchase-requisition.purchaserequisitiondetails');
    Route::get('purchase-requisition/requisitioninbox', RequisitionInbox::class)->name('purchase-requisition.requisitioninbox');
    Route::get('purchase-requisition/PRForm/{prno}', [PRForm::class,'genForm']);
});

Route::get('admin/changepassword', ChangePassword::class)->name('admin.changepassword');
Route::post('logout', [ClientController::class, 'logout'])->name('logout');


//for Test
Route::get('test1', Test1::class)->name('test1');
Route::get('test2', Test2::class)->name('test2');
//Route::post('/dropzone-store', [Test1::class,'dropzoneStore'])->name('dropzone.store');
Route::get('PHPJasper/{prno}', [PHPJasperController::class,'genReport'])->name('PHPJasper');

Route::get('/dropzone', [DropzoneController::class,'dropzone']);
Route::post('/dropzone-store', [DropzoneController::class,'dropzoneStore'])->name('dropzone.store');

//Test Dropzone
Route::post('attactFilePR',[PurchaseRequisitionDetails::class,'attactFilePR'])->name('attactFilePR'); 
Route::get('uploadFile',[PageController::class,'index']);
Route::post('uploadFile',[PageController::class,'uploadFile'])->name('uploadFile');
