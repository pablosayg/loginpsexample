<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Iluminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = 'this_is_a_secret_key-180892';
    }

    public function signup($email, $password, $getToken = null){

        // buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email_emp' => $email,
            'contrasena' => $password
        ])->first();

        // comprobar si son correctos (objeto)
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        // generar el token con los datos del usuario identificado
        if($signup){
            $token = array(
                'id' => $user->id_usuario,
                'mail' => $user->email_emp,
                'name' => $user->nombres,
                'time' => time(),
                'exp' => time() + (24 * 60 * 60) //7 dias, 24 horas, 60 minutos, 60 segundos -> una semana
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');

            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            //devolver los datos decodificados o el token, en funcion de un parametro
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }

        }else{
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto.'
            );
        }
        

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;

        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }

        if(!empty($decoded) && is_object($decoded) && isset($decoded->id)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }
}