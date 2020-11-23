<?php

namespace App\Http\Controllers\controladores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\modelos\Persona;
use App\modelos\Comentario;
use App\modelos\Producto;
use App\modelos\documentacion;

use App\User;
use App\modelos\tokens;

use Illuminate\Support\Facades\DB;
use \Mailjet\Resources;

class Personas extends Controller
{
    public function insertarpersona(Request $request){
        if ($request->user()->tokenCan('cliente')||$request->user()->tokenCan('provedor')||$request->user()->tokenCan('administrador')
        ){  
          $persona3=0;
          $persona4=0;
          $persona6=0;
          $persona7=persona::where('documentos',$request->foto)->get();
          foreach($persona7 as $value){
            $persona6=$value->id;
          }
           $persona2=DB::table('usuarios')
           ->join('personas', 'usuarios.id', '=', 'personas.usuario')
          ->select('personas.usuario','usuarios.id')
         ->Where('usuarios.correo', $request->user()->correo)->get();
         $persona5=DB::table('usuarios')
        ->select('usuarios.id')
       ->Where('usuarios.correo', $request->user()->correo)->get();
         foreach($persona2 as $value){
         $persona3=$value->usuario;

         } foreach($persona5 as $value){
          $persona4=$value->id;
 
          }
         if($persona3<1){
           if($persona4==$request->usuario){
             if($persona6<1){
        $request->validate([
                'nombre'=>'required',
                'apellidopaterno'=>'required',
                'apellidomaterno'=>'required',
                'sexo'=>'required',
                'edad'=>'numeric|required',
                'usuario'=>'numeric|required',
                'foto'=>'numeric|required',
                 ]);
$persona=new persona;
$persona->nombre=$request->nombre;
$persona->apellidopaterno=$request->apellidopaterno;
$persona->apellidomaterno=$request->apellidomaterno;
$persona->sexo=$request->sexo;
$persona->edad=$request->edad;
$persona->usuario=$request->usuario;
$persona->documentos=$request->foto;
$persona->save();
return response()->json([$persona,"para insertar su producto o comentario guarde su id"],200);
        }       
        return response()->json(["la foto ya es usada por alguien mas",],400);
 
}
        return response()->json(["la entrada de id es erronea",],400);

      }
        return response()->json(["usted ya tiene un perfil creado",],400);

}
        $name=$request->user()->correo;
        $this->enviarcorreo($name);

        
        return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);


    }
    public function actualizarpersona(Request $request,$id){
$persona3=0;
$persona2=DB::table('usuarios')
->join('personas', 'usuarios.id', '=', 'personas.usuario')
->select('personas.usuario','personas.id')
->Where('usuarios.correo', $request->user()->correo)->get();
foreach($persona2 as $value)
{
$persona3=$value->id;  
}
if ($request->user()->tokenCan('cliente')||$request->user()->tokenCan('administrador')||$request->user()->tokenCan('provedor')){
 if($persona3==$id){
  $request->validate([
        'nombre'=>'required',
        'apellidopaterno'=>'required',
        'apellidomaterno'=>'required',
        'edad'=>'numeric|required',
         ]);    
         $persona=persona::find($id);
    $persona->nombre=$request->nombre;
        $persona->apellidopaterno=$request->apellidopaterno;
        $persona->apellidomaterno=$request->apellidomaterno;
        $persona->edad=$request->edad;
        $persona->save();
        return response()->json([$persona,],200);

      }
      return response()->json(["su id no concide con su token",],400);

    }
    $name=$request->user()->correo;
    $this->enviarcorreo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);


    }
    public function verpersonas(Request $request){
      if ($request->user()->tokenCan('cliente')||$request->user()->tokenCan('administrador')||$request->user()->tokenCan('provedor')||$request->user()->tokenCan('Gratis')){
            $persona = DB::table('usuarios')
            ->join('personas', 'usuarios.id', '=', 'personas.usuario')
            ->select('usuarios.correo', 'personas.*')
        ->get();
return response()->json([$persona,],200);
        }
        $name=$request->user()->correo;
        $this->enviarcorreo($name);

        
        return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);



    }
    public function eliminarpersona(Request $request,$id){
        if ($request->user()->tokenCan('administrador')){
        $persona2=DB::table('personas')
          ->join('productos', 'personas.id', '=', 'productos.persona')
          ->select('productos.id')
         ->Where('productos.persona', $id)->get();
         
          $persona=Comentario::where('persona',$id);
          $persona->delete();
        
          $persona=producto::where('persona',$id);
          $persona->delete();
$persona=persona::find($id);
$persona->delete();
$persona=persona::all();
return response()->json([$persona,],200);
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
                'Email' =>$correoenvia,
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
               permisos necesarios: $persona'</p> sus permisos son:
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
