<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionList;
use App\Http\Livewire\PurchaseRequisition\PurchaseRequisitionDetails;

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

//======== Purchase Requisition ========
Route::get('purchase-requisition/purchaserequisitionlist', PurchaseRequisitionList::class)->name('purchase-requisition.purchaserequisitionlist');
Route::get('purchase-requisition/purchaserequisitiondetails', PurchaseRequisitionDetails::class)->name('purchase-requisition.purchaserequisitiondetails');

Route::get('/', function () {
    return view('welcome');
});
