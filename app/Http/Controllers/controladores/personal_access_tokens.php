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
    $this->enviarcorreo($name);

    
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
    $this->enviarcorreo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
}
public function enviarcorreo($name){
  $correoenvia=config('app.mjcorreo');
      $nombreenvia =config('app.mjquienloenvia');
      $correoadministrador=config('app.mjadministrador');
      $apikey=config('app.mjapikeypub');
      $apisecret=config('app.mjapikeypriv');
      $persona = DB::table('usuarios')
      ->join('personas', 'usuarios.id', '=', 'personas.usuario')
      ->select('usuarios.correo', 'personas.*')
     ->Where('correo', $name)->get();
     $persona2=tokens::where('name',$name)->get();
      $mj = new \Mailjet\Client( $apikey, $apisecret,true,['version' => 'v3.1']);
    $body = [
      'Messages' => [
        [
          'From' => [
            'Email' =>  $correoenvia,
           'Name' =>  $nombreenvia
          ],
          'To' => [
            [
              'Email' => $correoadministrador,
              'Name' => "Nuevo usuario"
            ]
          ],
          'Subject' => "Greetings from Mailjet.",
          'TextPart' => "My first Mailjet email",
          'HTMLPart' => "h3>Dear passenger 
          <p> 'El usuario  con los siguientes datos no cuenta con los
           permisos necesarios: {$persona}'</p> sus permisos son:
            $persona2<br />",
          'CustomID' => "AppGettingStartedTest"
        ]
      ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    if($response->success())
        return response()->json(["por favor verifique su correo y ingrese sus datos"
        =>$response->getData()],200);
        return response()->json(["mensaje"=>$response->getData()],500);
} 
}
