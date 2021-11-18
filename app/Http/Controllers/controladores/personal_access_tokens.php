<?php

namespace App\Http\Controllers\controladores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\modelos\tokens;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\DB;
use \Mailjet\Resources;

class personal_access_tokens extends Controller
{
public function asignarpermisos(Request $request,$id=null){
    if ($request->user()->tokenCan('administrador')){
        $request->validate([
            'permiso'=>'required',
          
        ]);
$permiso=tokens::find($id);
$permiso->abilities=$request->permiso;
$permiso->save();
$rol=User::find($permiso->tokenable_id);
if($permiso->abilities=="Gratis")
{
$rol->rol_asignado_por_permisos=0;    
}else if($permiso->abilities=="cliente")
{
    $rol->rol_asignado_por_permisos=1;    

}
else if($permiso->abilities=="provedor")
{
    $rol->rol_asignado_por_permisos=2;    

}

$rol->save();
return response()->json([$permiso,],200);
    }
    $name=$request->user()->correo;

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
}
public function eliminarpermisos(Request $request,$id=null){
    if ($request->user()->tokenCan('administrador')){

    $permiso=tokens::find($id);
    $permiso->abilities="Gratis";
    $permiso->save();
    return response()->json([$permiso,],200);
    }
    $name=$request->user()->correo;

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
}

}
