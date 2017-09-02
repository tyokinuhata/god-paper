<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('create',['uses' => 'executionPaizaApiController@executionSourceCode']);

Route::get('language',function(Request $request){
    Storage::disk('local')->put('public/language', $request["code"]);

    $text = exec("file storage/language");
    $lang = config('column.language');

    foreach ($lang as $key=>$value){
        if( strpos($text,$key)){
            return $value;
        }
    }
    return "";
});

