<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use JWTAuth;
use Response;
use \Illuminate\Http\Response as Res;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends ApiController
{


    /**
     * @description: Api user authenticate method
     * @param: email, password
     * @return: Json String response
     */
    public function authenticate(Request $request)
    {
        $rules = array (
            'email' => 'required|email',
            'password' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator-> fails()){
            return $this->respondValidationError('VERIFIER VOS CHAMPS.', $validator->errors());
        }
        else{
            $user = User::where('email', $request['email'])->first();
            if($user){
                $api_token = $user->api_token;
                if ($api_token == NULL){
                    return $this->_login($request['email'], $request['password']);
                }
                try{
                    $user = JWTAuth::toUser($api_token);
                    return $this->respond([
                        'status' => 'success',
                        'status_code' => $this->getStatusCode(),
                        'message' => 'DEJA CONNECTE',
                        'user' => $this->userTransformer->transform($user)
                    ]);
                }catch(JWTException $e){
                    $user->api_token = NULL;
                    $user->save();
                    return $this->respondInternalError("AUTHENTIFICATION ECHOUE");
                }
            }
            else{
                return $this->respondWithError("EMAIL OU PASSWORD INCORECT");
            }
        }
    }

    private function login($email, $password)
    {
        $credentials = ['email' => $email, 'password' => $password];

        if ( ! $token = JWTAuth::attempt($credentials)) {
            return $this->respondWithError("utilisateur n existe pas!");
        }
        $user = JWTAuth::toUser($token);
        $user->api_token = $token;
        $user->save();
        return $this->respond([
            'status' => 'success',
            'status_code' => $this->getStatusCode(),
            'message' => 'CONNEXION REUSSIE',
            'data' => $user
        ]);
    }

    /**
     * @description: CREATION DE COMPTE
     * @param: lastname, firstname, username, email, password. bith date
     * @return: Json String response
     */
    public function register(Request $request)
    {
        $rules = array (
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator-> fails()){
            return $this->respondValidationError('VERIFIER VOS CHAMPS', $validator->errors());
        }
        else{
            $user = User::create([
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'first_name' => $request['first_name'],
                'bith_date' => $request['bith_date'],
                'email' => $request['email'],
                'password' => \Hash::make($request['password']),
            ]);
            return $this->login($request['email'], $request['password']);
        }
    }

    /**
     * @description:log out
     * @param: null
     * @return: Json String response
     */
    public function logout($api_token)
    {
        try{
            $user = JWTAuth::toUser($api_token);
            $user->api_token = NULL;
            $user->save();
            JWTAuth::setToken($api_token)->invalidate();
            $this->setStatusCode(Res::HTTP_OK);
            return $this->respond([
                'status' => 'success',
                'status_code' => $this->getStatusCode(),
                'message' => 'LOG OUT SUCCESSFUL!',
            ]);

        }catch(JWTException $e){
            return $this->respondInternalError("LOG OUT ECHOUE!");
        }
    }
}
