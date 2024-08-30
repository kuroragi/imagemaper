<?php

namespace App\CPU;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Helpers{

    public static function getToken(){

        $secret = config('secret.secret');
        $api = config('secret.main_api').'/get-token';

        $client = new Client();

        $body = [
            'secret' => $secret,
        ];

        $response = $client->request('POST', $api, [
            'form_params' => $body,
        ]);

        $status = $response->getStatusCode();

        if ($status == 200) {
            $token = json_decode($response->getBody()->getContents())->data->access_token;
            

            return $token;
        }

        return response()->json('user not authorized');
    }

    public static function getCategory(){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/category';

        $header = [
            'Authorization' => 'Bearer'.$key,
        ];

        try {
            $client = new Client();

            $response = $client->request('GET', $api, [
                'headers' => $header,
            ]);

            $status = $response->getStatusCode();

            session()->put('token', $key);

            if ($status == 200) {
                $resp = json_decode($response->getBody()->getContents())->api_status;
                if ($resp == 0) {
                    $data = [
                        'code' => 404,
                        'message' => 'Data tidak ditemukan',
                    ];

                    return $data;
                } else {
                    $category = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 200,
                        'data' => $category,
                    ];

                    return $data;
                }
            }
        } catch (ClientException $e) {
            // $this->getSkpd($request);
        }
        // return response()->json(['code' => 200, 'message' => 'success']);
    }

    public static function getGroup(){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/asset_group';

        $header = [
            'Authorization' => 'Bearer'.$key
        ];

        try {
            $client = new Client();

            $response = $client->request('POST', $api, [
                'headers' => $header,
            ]);

            $status = $response->getStatusCode();

            session()->put('token', $key);

            if($status == 200){
                $resp = json_decode($response->getBody()->getContents())->api_status;
                if($resp == 0){
                    $data = [
                        'code' => 404,
                        'message' => 'Data tidak ditemukan',
                    ];

                    return $data;
                }else{
                    $group = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 200,
                        'data' => $group,
                    ];

                    return $data;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

}