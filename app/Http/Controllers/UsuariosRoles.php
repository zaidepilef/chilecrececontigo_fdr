<?php

namespace App\Http\Controllers;

use App\User;
use App\Rol;
use Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use View;
use Session;

class UsuariosRoles extends Controller
{
    //
    public function index()
    {
        View::share('roles', Rol::all());
        // formNewUser
        $users = User::all();
        return view('AdminUsuariosRoles.index', ['users' => $users, 'formEditUser' => false]);
    }

    public function newUser(Request $request)
    {
        try {

            $newUser = new User;
            $resultado = $request::all();
            echo "<pre>";
            print_r($resultado);
            echo "</pre>";
            $validate = Validator::make($resultado, [
                'usernombre' => 'required|string',
                'userapellido' => 'required|string',
                'usermail' => 'required|email|max:255',
                'newusername' => 'required|string|max:255',
            ]);



            if ($validate->fails()) {
                // echo"falil";
                return redirect()->back()->withErrors($validate->errors());
            } else {
                // echo"val";
                // valido
                $id = $resultado['userid'];
                $usernombre = $resultado['usernombre'];
                $userapellido = $resultado['userapellido'];
                $usermail = $resultado['usermail'];
                $newusername = $resultado['newusername'];
                $ldap = $resultado['ldap'];
                $newuserpass = $resultado['newuserpass'];
                $newuserpassconfirm = $resultado['newuserpassconfirm'];
                $userrolid = $resultado['userrolid'];

                $newUser->nombre = $usernombre;
                $newUser->apellido = $userapellido;
                $newUser->email = $usermail;
                $newUser->name = $newusername;
                $newUser->rol_id = $userrolid;

                if (isset($id)) {
                    $usuarioup = User::find($id);

                    $on = ($ldap === 'on') ? true : false;
                    $usuarioup->update([
                        'nombre' => $usernombre,
                        'apellido' => $userapellido,
                        'email' => $usermail,
                        'name' => $newusername,
                        'rol_id' => $userrolid,
                        'ldap' => $on
                    ]);
                    return redirect()->back()->with('mensajes_ok', 'Usuario Guardado');

                } else {

                    // si es ldap
                    if ($ldap === 'on') {

                        $newUser->ldap = true;
                        $newUser->password = md5($newuserpass);
                        $newUser->save();
                        return redirect()->back()->with('mensajes_ok', 'Usuario Almacenado');
                    } else {

                        $newUser->ldap = false;

                        if ($newuserpass === $newuserpassconfirm) {
                            $newUser->password = md5($newuserpass);
                            $newUser->save();
                            return redirect()->back()->with('mensajes_ok', 'Usuario Almacenado');
                        } else {
                            return redirect()->back()->withErrors('Passwords no coinciden');
                        }
                    }
                }
            }
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function editUser($id)
    {
        try {

            View::share('roles', Rol::all());

            $users = User::all();

            /*

            echo "<br> id : " . $id;
            $usuario = User::where('id', $id)->first();

            echo "<pre>";
            print_r($usuario);
            echo "</pre>";
            echo "<pre>";
            var_dump($usuario);
            echo "</pre>";
            */

            return view('AdminUsuariosRoles.usuarioRoles',  ['users' => $users, 'formEditUser' => false]);
            // return redirect()->back()->with('formEditUser',true);
        } catch (ModelNotFoundException $exception) {
            // return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function saveUser(Request $request)
    { }
}
