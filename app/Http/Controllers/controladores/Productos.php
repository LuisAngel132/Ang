<?php

namespace App\Http\Controllers\controladores;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\modelos\Producto;
use App\modelos\Comentario;

use \Mailjet\Resources;
use App\modelos\tokens;

class Productos extends Controller
{
    public function insertarproducto(Request $request){
        if ($request->user()->tokenCan('provedor')||$request->user()->tokenCan('administrador')){
          $persona3=0;
          $persona2=DB::table('usuarios')
          ->join('personas', 'usuarios.id', '=', 'personas.usuario')
          ->select('personas.usuario','personas.id')
          ->Where('usuarios.correo', $request->user()->correo)->get();
          foreach($persona2 as $value)
          {
           $persona3=$value->id;
          }
          if($persona3==$request->persona){
          $request->validate([
                'producto'=>'required',
                'estadodelproducto'=>'required',
           'persona'=>'required|numeric'
                 ]);    
            $Producto=new Producto;
        $Producto->producto=$request->producto;
        $Producto->estadodelproducto=$request->estadodelproducto;
       $Producto->persona=$request->persona;
        $Producto->save();
        return response()->json([$Producto,"este es el id del producto al cual se le hara referencia en los comentario",],200);
        }
        return response()->json(["su id de persona es erroneo al token"],400);

      }
        $name=$request->user()->correo;
    $this->enviarcorreo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);

            }
            public function actualizarProducto(Request $request,$id){
                if ($request->user()->tokenCan('provedor')||$request->user()->tokenCan('administrador')){
                  $persona4=0;
                  $persona2=DB::table('usuarios')
                  ->join('personas', 'usuarios.id', '=', 'personas.usuario')
                  ->join('productos', 'personas.id', '=', 'productos.persona')
                  ->select('productos.id')
                  ->Where('usuarios.correo', $request->user()->correo)->get();
                 
        $longitud = count($persona2);
       $n=0;
       for ($i = 0; $i <$longitud+1; $i++) {
         foreach($persona2 as $value)
         {
           $persona3=$value->id;
     if( $persona3==$id)
     {
      $request->validate([
        'producto'=>'required',
   
         ]);    

    $Producto=Producto::find($id);
$Producto->producto=$request->producto; 
$Producto->estadodelproducto=$request->estadodelproducto;       

$Producto->save();
return response()->json([ $Producto],200);

     }
    }
      }
        return response()->json([ "el producto no le pertenece"],400);
                }
                $name=$request->user()->correo;
    $this->enviarcorreo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);

            }
            public function verProductos(Request $request){
                if ($request->user()->tokenCan('cliente')||$request->user()->tokenCan('Gratis')||
                $request->user()->tokenCan('provedor')||$request->user()->tokenCan('administrador')){
        $Producto=Producto::all();
        return response()->json([$Producto,],200);
        
            }
            $name=$request->user()->correo;
    $this->enviarcorreo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);

        }
            public function eliminarProducto(Request $request,$id){
                if ($request->user()->tokenCan('provedor')||$request->user()->tokenCan('administrador')){
                  $persona2=DB::table('usuarios')
                  ->join('personas', 'usuarios.id', '=', 'personas.usuario')
                  ->join('productos', 'personas.id', '=', 'productos.persona')
                  ->select('productos.id')
                  ->Where('usuarios.correo', $request->user()->correo)->get(); 
                 
        $Producto=Producto::all();
        $longitud = count($persona2);
        $n=0;
        for ($i = 0; $i <$longitud+1; $i++) {
          foreach($persona2 as $value)
          {
            $persona3=$value->id;
      if( $persona3==$id)
      {
        $Producto=Producto::find($id);
        $Producto2=comentario::where('producto',$id)->get();
        $longitud = count($Producto2);
        for ($i = 0; $i <$longitud+1; $i++) {
        
         
        foreach($Producto2 as $value)
        {
          $Producto3=$value->id;
          $Producto4=comentario::find($Producto3);
          $Producto4->delete();

        }
        $Producto->delete();
        $Producto=Producto::all();

        return response()->json([ $Producto,],200);

      }
    }
  }
}

  return response()->json(["el producto no le pertenece",],400);


                }
                $name=$request->user()->correo;

                $this->enviarcorreo($name);
                
                return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
            
            }
            public function enviarcorreo($name){
              $apikey=config('app.mjapikeypub');
              $apisecret=config('app.mjapikeypriv');
              $correoenvia=config('app.mjcorreo');
              $nombreenvia =config('app.mjquienloenvia');
              $correoadministrador=config('app.mjadministrador');
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
                          'Email' =>$correoadministrador,
                          'Name' => "Nuevo usuario"
                        ]
                      ],
                      'Subject' => "Greetings from Mailjet.",
                      'TextPart' => "My first Mailjet email",
                      'HTMLPart' => "h3>Dear passenger 
                      <p> 'El usuario  con los siguientes datos no cuenta con los
                       permisos necesarios para comentar,eliminar,borrar o actualizar : {$persona}'</p> sus permisos son:
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
