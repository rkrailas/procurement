<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionList;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionDetails;
use App\Http\Livewire\admin\ChangePassword;
use App\Http\Controllers\ClientController;

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
    Route::get('purchase-requisition/purchaserequisitionlist', PurchaseRequisitionList::class)->name('purchase-requisition.purchaserequisitionlist');
    Route::get('purchase-requisition/purchaserequisitiondetails', PurchaseRequisitionDetails::class)->name('purchase-requisition.purchaserequisitiondetails');
});

Route::get('admin/changepassword', ChangePassword::class)->name('admin.changepassword');
Route::post('logout', [ClientController::class, 'logout'])->name('logout');
