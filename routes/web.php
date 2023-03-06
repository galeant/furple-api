<?php

use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    dd(__('message.register'));
    return __('message.register');
    // $array =  range(1, 20000);
    // dd($array);
    // DB::table('block')->insert([
    //     'user_id' =>
    // ])
    // $data = User::where('id', '!=', 1)->with(['hasFriends' => function ($q) {
    //     $q->where('id', 1);
    // }])
    //     ->with([
    //         'belongFriends' => function ($q) {
    //             $q->where('id', 1);
    //         }
    //     ])->get()->toArray();
    // dd($data);

    // $friend = User::where(function ($q) {
    //     $q->whereRelation('hasFriends', 'id', 1)
    //         ->orWhereRelation('belongFriends', 'id', 1);
    // })->get();
    // dd($friend);
    // dd($data);
    return view('welcome');
});
