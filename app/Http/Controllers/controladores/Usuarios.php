<?php

namespace App\Http\Controllers\controladores;
use  Illuminate\Support\Facades\DB ;
use App\modelos\Producto;
use App\modelos\Comentario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use \Mailjet\Resources;
use App\modelos\tokens;

class Usuarios extends Controller
{
    public function insertarUser(Request $request){
        $request->validate([
            'correo'=>'required|email',
            'contraseña'=>'required',
                        ]);    
        $User=new User;
        $User->correo=$request->correo;
        $User->contraseña=Hash::make($request->contraseña);
        $User->rol_asignado_por_permisos=0;
        $User->aceptacion=0;
        $codigo=rand(10000,1000000);
        $User->codigo= $codigo;
        $User->save();
       $correo= $request->correo;
       $this->enviarcorreo($correo,$codigo);
       $user = DB::table('usuarios')
       ->select('usuarios.id')->where('correo',$request->correo)
   ->get();
     
       return response()->json(["por favor verifique su correo y ingrese sus datos este id que se le proporciona es para insertar su perfil recuerde insertar la documentacion antes del
        perfil despues de verificar proceda a iniciar sesion para ingresar sus datos",$user,],200);    
            
        
        //$response->success() && var_dump($response->getData());       
    } 
           
            public function eliminarUser(Request $request,$id){
                if ($request->user()->tokenCan('administrador')){
                  
                  $User=User::find($id);
                
                $User->delete();
        if($User->delete())
        {$User=User::all();
        return response()->json([$User,],200);
        }else{
            return response()->json([$User,"SIGUE CONECTADO CON DATOS DE LA PERSONA POR FAVOR 
            ELIMINE A LA PERSONA PRIMERO"],200);

        }
    }
    $name=$request->user()->correo;
    $this->correo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
    }
    public function LogIn(Request $request)
    {
        $request->validate([
            'correo'=>'required|email',
            'contraseña'=>'required',
        ]);
        $user=User:: where('correo',$request->correo)->first();
        
        if($user->aceptacion==1){
        if(!$user||!Hash::check($request->contraseña,$user->contraseña))
        {
    throw ValidationException::withMessages([
        'correo|contraseña'=>['sus datos son incorrectos'],]);
    
        }
    else{
        if($user->rol_asignado_por_permisos==0)
        {
            $token = $user->createToken($request->correo, ['Gratis'])->plainTextToken;
            $user->save();
        }else if($user->rol_asignado_por_permisos==1)
        {
            $token = $user->createToken($request->correo, ['cliente'])->plainTextToken;
            $user->save();
        }
       
        else if($user->rol_asignado_por_permisos==2)
        {
            $token = $user->createToken($request->correo, ['provedor'])->plainTextToken;
            $user->save();            
        }
       
       else if($user->rol_asignado_por_permisos==4)
      {
        $token = $user->createToken($request->correo, ['administrador'])->plainTextToken;
        $user->save();    
      }
    
    
    return response()->json([ $token," \n su id para la pesona o su perfil es el siguiente pero primero 
    proceda a crear su documentacion\n","id:"=>$user->id],201);
    
    }
}  
else if($user->aceptacion==0){
     return response()->json([ "La cuenta no esta activada"],400);
} 
    }   

             public function LogOut (Request $request){

            return response()->json(["eliminados"=>$request->user()->tokens()->delete()],201);

                     
                            }
                        
                            public function crear(Request $request,$codigo){
        // return $request->all();

        $results = DB::select('select id,codigo from usuarios where codigo ='.$codigo);
        foreach ($results as $valor) {
            $id=$valor->id;
        $codigo=$valor->codigo;
        }
        if($codigo>1)
        {

 $user=User::find($id);
 $user->aceptacion=1;
 $user->save();
         return response()->json([ "su Cuenta fue activada"],201);
        }
if($results==null)
 {       return response()->json([ "Te has equivocado vuelve a intentarlo"],500);
        }
        
      

       
    }
    public function enviarcorreo($correo,$codigo){ 
      $apikey =config('app.mjapikeypub');
      $apisecret=config('app.mjapikeypriv');
       $correoenvia=config('app.mjcorreo');
       $nombreenvia =config('app.mjquienloenvia');
      
      $mj = new \Mailjet\Client( $apikey,$apisecret,true,['version' => 'v3.1']);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => $correoenvia,
               'Name' => $nombreenvia
              ],
              'To' => [
                [
                  'Email' => $correo,
                  'Name' => "Nuevo usuario"
                ]
              ],
              'Subject' => "Greetings from Mailjet.",
              'TextPart' => "My first Mailjet email",
              'HTMLPart' => "h3>Dear passenger 
              </h2> http://127.0.0.1:8000/api/{$codigo}</h3><br />",
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
public function correo($name){
  $persona = DB::table('usuarios')
  ->join('personas', 'usuarios.id', '=', 'personas.usuario')
  ->select('usuarios.correo', 'personas.*')
 ->Where('correo', $name)->get();
 $persona2=tokens::where('name',$name)->get();
 $apikey =config('app.mjapikeypub');
 $apisecret=config('app.mjapikeypriv');
 $correoenvia=config('app.mjcorreo');
 $nombreenvia =config('app.mjquienloenvia');
 $correoadministrador=config('app.mjadministrador');

  $mj = new \Mailjet\Client($apikey,$apisecret,true,['version' => 'v3.1']);
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' => $correoenvia,
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
