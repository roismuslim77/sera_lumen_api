<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Format;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;

class ReqresController extends Controller
{
    public function __construct() 
    {
        $this->client = new Client([
            'base_uri' => env('REQRES_API_URL')
        ]);
    }

    public function login(Request $request)
    {
        $validatedData = $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        //set parameter request
        $reqData = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ];

        //get data reqres
        try {
            $response = $this->client->request('POST', env('REQRES_LOGIN_API', 'api/login'), [
                'json' => $reqData
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            return Format::response([
                'data' => $data,
                'message' => 'login success'
            ]);
        } catch (ServerException $th) {
            $msg = json_decode($th->getResponse()->getBody()->getContents());
            return Format::response([
                'data' => null,
                'message' => 'Terjadi kesalahan pada sistem.',
                'original' => $msg
            ], true, 200);
        } catch (ClientException $th) {
            $msg = json_decode($th->getResponse()->getBody()->getContents());
            return Format::response([
                'data' => null,
                'code' => $th->getCode(),
                'message' => $msg->error ?? 'Terjadi kesalahan pada sistem.'
            ], true, 200);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        //set parameter request
        $reqData = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ];

        //get data reqres
        try {
            //code...
            $response = $this->client->request('POST', env('REQRES_REGISTER', 'api/register'), [
                'json' => $reqData
            ]);
            $data = json_decode($responses->getBody()->getContents(), true);
    
            return Format::response([
                'data' => $data,
                'message' => 'register success'
            ]);
        } catch (ServerException $th) {
            $msg = json_decode($th->getResponse()->getBody()->getContents());
            return Format::response([
                'data' => null,
                'message' => 'Terjadi kesalahan pada sistem.',
                'original' => $msg
            ], true, 200);
        } catch (ClientException $th) {
            $msg = json_decode($th->getResponse()->getBody()->getContents());
            return Format::response([
                'data' => null,
                'code' => $th->getCode(),
                'message' => $msg->error ?? 'Terjadi kesalahan pada sistem.'
            ], true, 200);
        }
    }
}