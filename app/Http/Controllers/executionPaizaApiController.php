<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class executionPaizaApiController extends Controller
{

    private $apiKey;
    private $client;
    /**
     * executionPaizaApiController constructor.
     */
    public function __construct()
    {
        $this->apiKey = ['api_key' => 'guest'];
        $this->client = new Client();
    }

    public function executionSourceCode(Request $request)
    {
        $postResult       = $this->create($request);
        $getReqData       = $this->get_status($postResult);
        if(isset($getReqData["error"])){
            return $getReqData["error"];
        }
        $getDetailsResult = $this->get_details($getReqData);

        return response()->json($getDetailsResult);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function create(Request $request)
    {
        $reqData = $request->all();
        $reqData += $this->apiKey;

        $postReqData = http_build_query($reqData);
        $res = $this->client->post('http://api.paiza.io:80/runners/create?' . $postReqData);

        return json_decode($res->getbody(), true);
    }

    /**
     * @param $postResult
     * @return array
     */
    private function get_status($postResult)
    {
        $reqData = ['id' => $postResult['id']];
        $reqData += $this->apiKey;
        $getReqData = http_build_query($reqData);
        do {
            $res = $this->client->get('http://api.paiza.io:80/runners/get_status?' . $getReqData);
            $getStatusResult = json_decode($res->getbody(), true);
            Sleep(1);
            if(isset($getStatusResult['error'])){
                return ["error"=>$getStatusResult['error']];
            }
        } while ($getStatusResult['status'] == 'running');
        return $reqData;
    }

    /**
     * @param $getReqData
     * @return mixed
     */
    private function get_details($getReqData)
    {
        $getReqData = http_build_query($getReqData);
        $res = $this->client->get('http://api.paiza.io:80/runners/get_details?' . $getReqData);

        $getDetailsResult = json_decode($res->getbody(), true);
        return $getDetailsResult;
    }
}
