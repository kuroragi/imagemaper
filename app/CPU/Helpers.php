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
            'Authorization' => 'Bearer'.$key,
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

    public static function getGroupSelect(int $groupId){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/asset_group_select';

        $header = [
            'Authorization' => 'Bearer'.$key
        ];

        $body = [
            'id' => $groupId,
        ];

        try {
            $client = new Client();

            $response = $client->request('POST', $api, [
                'headers' => $header,
                'form_params' => $body
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
                    $asset_image = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 200,
                        'data' => $asset_image,
                    ];

                    return $data;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public static function getImageMap(int $groupId){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/image_map';

        $header = [
            'Authorization' => 'Bearer'.$key
        ];

        $body = [
            'id_asset_group' => $groupId,
        ];

        try {
            $client = new Client();

            $response = $client->request('POST', $api, [
                'headers' => $header,
                'form_params' => $body
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
                    $asset_image = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 200,
                        'data' => $asset_image,
                    ];

                    return $data;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public static function postMap($map){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/add_image_map';

        $header = [
            'Authorization' => $key,
        ];

        $body = [
            'name' => $map['name'],
            'coordinate' => $map['coordinate'],
            'description' => $map['description'],
            'status' => $map['status'],
            'shape' => $map['shape'],
            'id_asset_group' => $map['id_group'],
            'id_asset' => $map['id_asset'],
        ];
        
        try {
            $client = new Client();
            $response = $client->request('POST', $api, [
                'headers' => $header,
                'form_params' => $body,
            ]);

            
            
            $status = $response->getStatusCode();
            
            
            session()->put('token', $key);
            
            if ($status == 200) {
                $resp = json_decode($response->getBody()->getContents())->api_status;

                if ($resp == 0) {
                    $data = [
                        'code' => 404,
                        'message' => 'Data Tidak Dapat Dikirim',
                    ];

                    return $data;
                }else{
                    // $imageMap = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 201,
                        'message' => 'Area Berhasil Disimpan',
                    ];

                    return $data;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);

        }
    }

    public static function updateMap($map){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/edit_image_mapper';

        $header = [
            'Authorization' => $key,
        ];

        $body = [
            'id' => $map['id'],
            'name' => $map['name'],
            'coordinate' => $map['coordinate'],
            'description' => $map['description'],
            'device_type' => $map['device_type'],
            'status' => $map['status'],
            'meta' => $map['meta'],
            'shape' => $map['shape'],
            'id_asset_group' => $map['id_asset_group'],
            'id_asset' => $map['id_asset'],
        ];
        
        try {
            $client = new Client();
            $response = $client->request('POST', $api, [
                'headers' => $header,
                'form_params' => $body,
            ]);

            
            
            $status = $response->getStatusCode();
            
            
            session()->put('token', $key);
            
            if ($status == 200) {
                $resp = json_decode($response->getBody()->getContents())->api_status;

                if ($resp == 0) {
                    $data = [
                        'code' => 404,
                        'message' => 'Data Tidak Dapat Dikirim',
                    ];

                    return $data;
                }else{
                    // $imageMap = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 201,
                        'message' => 'Area Berhasil di Update',
                    ];

                    return $data;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $th;

        }
    }

    public static function deleteMap($id){
        $token = Helpers::getToken();

        $key = $token;

        $api = config('secret.main_api').'/delete_image_map';

        $header = [
            'Authorization' => $key,
        ];

        $body = [
            'id' => $id,
        ];
        
        try {
            $client = new Client();
            $response = $client->request('POST', $api, [
                'headers' => $header,
                'form_params' => $body,
            ]);

            
            
            $status = $response->getStatusCode();
            
            
            session()->put('token', $key);
            
            if ($status == 200) {
                $resp = json_decode($response->getBody()->getContents())->api_status;

                if ($resp == 0) {
                    $data = [
                        'code' => 404,
                        'message' => 'Data Tidak Ditemukan',
                    ];

                    return $data;
                }else{
                    // $imageMap = json_decode($response->getBody())->data;

                    $data = [
                        'code' => 201,
                        'message' => 'Area Berhasil Dihapus',
                    ];

                    return $data;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);

        }
    }

}