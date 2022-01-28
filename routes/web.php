<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionList;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionDetails;
use App\Http\Livewire\PurchaseRequisition\RequisitionInbox;
use App\Http\Livewire\admin\ChangePassword;
use App\Http\Controllers\ClientController;
use App\Http\Livewire\Test1;

//for Test
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

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

});

Route::get('admin/changepassword', ChangePassword::class)->name('admin.changepassword');
Route::post('logout', [ClientController::class, 'logout'])->name('logout');


//for Test
Route::get('test1', Test1::class)->name('test1');
Route::post('/dropzone-store', [Test1::class,'dropzoneStore'])->name('dropzone.store');
