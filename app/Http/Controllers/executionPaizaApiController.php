<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class executionPaizaApiController extends Controller
{
    public function executionSourceCode(Request $request)
    {
        //リクエストのデータを変数に格納
        $reqData = $request->all();
        
        $apiKey = ['api_key' => 'guest'];

        //api_keyを追加
        $reqData += $apiKey;

        $postReqData = http_build_query($reqData);

        $client = new Client();
        $res = $client->post('http://api.paiza.io:80/runners/create?'.$postReqData);

        $postResult = json_decode($res->getbody(),true);

        $reqData = [
            'id' => $postResult['id'],
        ];
        $reqData += $apiKey;

        $getReqData = http_build_query($reqData);

        do{
        $res = $client->get('http://api.paiza.io:80/runners/get_status?'.$getReqData);

        $getStatusResult = json_decode($res->getbody(),true);
        Sleep(1);
        }while($getStatusResult['status'] == 'running');

        $res = $client->get('http://api.paiza.io:80/runners/get_details?'.$getReqData);

        $getDetailsResult = json_decode($res->getbody(),true);

        return response()->json($getDetailsResult);

        
     }
}
