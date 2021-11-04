<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

class UserController extends Controller
{

    public function createAdmin(Request $request){
        $array = $request->input();
        $string = json_encode($array);
        $json = json_decode($string);

        $validate = \Validator::make($array, [
            'email_emp' => 'required|email|unique:pcs_usuario', // unique:tabla(db)
            'contrasena' => 'required'
        ]);

        if(!empty($array))
        {
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'error' => $validate->errors()
                );  
            }
            else{
                // cifrar contraseña
                $pwd = hash('sha256', $json->contrasena);

                $fechaActual = date('y-m-d');

                $user = new User();
                $user->email_emp = $json->email_emp;
                $user->contrasena = $pwd;
                $user->nombres = $json->nombres;
                $user->apellidos = $json->apellidos;
                //$user->telefono = $json->telefono;
                $user->fch_creacion = $fechaActual;
                $user->id_tusuario = 1;
                $user->id_estado = 1;

                $user->save();
                $data = array(
                    'status' => 'exitoso',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente'
                );
            }
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos'
            );
        }
        return response()->json($data,$data['code']);
    }

    public function register(Request $request){
        
        $token = $request->header('Authorization');
        $jwtAut = new \JwtAuth();
        $checkToken = $jwtAut->checkToken($token);

        $array = $request->input();
        $string = json_encode($array);
        $json = json_decode($string);
        //var_dump($array); die();

        // Validar datos

        $validate = \Validator::make($array, [
            'email_emp' => 'required|email|unique:pcs_usuario', // unique:tabla(db)
            'contrasena' => 'required'
        ]);

        if($checkToken && !empty($array))
        {
            $user_tk = $jwtAut->checkToken($token, true);
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'error' => $validate->errors()
                );  
            }
            else{
                // cifrar contraseña
                //$pwd = password_hash($json->contrasena, PASSWORD_BCRYPT, ['cost' => 9]);
                $pwd = hash('sha256', $json->contrasena);

                $fechaActual = date('y-m-d');

                //var_dump($json); die();
                $user = new User();
                $user->email_emp = $json->email_emp;
                $user->contrasena = $pwd;
                $user->nombres = $json->nombres;
                $user->apellidos = $json->apellidos;
                $user->telefono = $json->telefono;
                $user->fch_creacion = $fechaActual;
                $user->creadopor = $user_tk->id;
                $user->id_tusuario = $json->id_tusuario;
                $user->id_estado = $json->id_estado;

                $user->save();
                $data = array(
                    'status' => 'exitoso',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente'
                );
            }
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 200,
                'message' => 'Los datos enviados no son correctos'
            );
        }
        return response()->json($data,$data['code']);
    }

    public function login(Request $request){

        //$jwtAut = new \App\Helpers\JwtAuth();        
        $jwtAut = new \JwtAuth();

        // Recibir datos por POST
        $array = $request->input();
        $string = json_encode($array);
        $json = json_decode($string);
        // Validar esos datos
        $validate = \Validator::make($array, [
            'email_emp' => 'required|email|',
            'contrasena' => 'required'
        ]);

        if(!empty($array))
        {
            if($validate->fails()){
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se podido identificar',
                    'error' => $validate->errors()
                );  
            }
            else{
                // cifrar contraseña
                $pwd = hash('sha256', $json->contrasena);
                
                // Devolver token o datos
                $signup = $jwtAut->signup($json->email_emp, $pwd);

                if(!empty($json->getToken)){
                    $signup = $jwtAut->signup($json->email_emp, $pwd, true);
                }
            }
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 200,
                'message' => 'Los datos enviados no son correctos'
            );
        }
        //$signup = 'token hola pablito';
        return response()->json($signup, 200);
    }

    public function update(Request $request){
        // Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAut = new \JwtAuth();
        $checkToken = $jwtAut->checkToken($token);

        // Recoger los datos por post
        $array = $request->input();
        $string = json_encode($array);
        $json = json_decode($string);

        if($checkToken && !empty($array)){

            $user = $jwtAut->checkToken($token, true);
            // Validar datos
            $validate = \Validator::make($array, [
                'id_usuario' => 'require',
                'email_emp' => 'required|email|unique:pcs_usuario'.$user->id, // unique:tabla(db)
                'contrasena' => 'required'
            ]);

            // quitar datos que no quiero actualizar
            unset($array['id_usuario']);
            unset($array['fch_creacion']);
            unset($array['creadopor']);

            // actualizar en bd
            $user_update = User::where('id_usuario', $user->id)->update($array);

            // devolver array con resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'changes' => $array
            );

        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El Usuario no esta identificado.'
            );
        }

        return response()->json($data, $data['code']);
    }
}
