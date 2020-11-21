<?php

namespace App\Http\Controllers\controladores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\modelos\Comentario;
use Illuminate\Support\Facades\DB;
use \Mailjet\Resources;
use App\modelos\tokens;

class Comentarios extends Controller
{
    public function insertarComentario(Request $request){
     
      if  ($request->user()->tokenCan('cliente')||$request->user()->
      tokenCan('administrador')){
        $persona2=0;
        $persona3=0;
        $persona6=0;
        $persona8=0;
        $persona2=DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->select('personas.usuario','personas.id')
        ->Where('usuarios.correo', $request->user()->correo)->get();
        $persona3=DB::table('usuarios')
                  ->join('personas', 'usuarios.id', '=', 'personas.usuario')
                  ->join('productos', 'personas.id', '=', 'productos.persona')
                  ->select('productos.id')
                  ->Where('usuarios.correo', $request->user()->correo)->get(); 
        $longitud = count($persona2);
        foreach($persona2 as $value)
        {
          $persona8=$value->id;
        }
        for ($i = 0; $i <$longitud; $i++) {
          foreach($persona3 as $value)
          {
            $persona6=$value->id;
      if( $persona6==$request->producto)
      {
        if($persona6==$request->producto)
        {

          $request->validate([
            'titulo'=>'required',
            'comentario'=>'required',
            'persona'=>'numeric|required',
            'producto'=>'numeric|required',
             ]);
             
        $Comentario=new Comentario;
        $Comentario->titulo=$request->titulo;
        $Comentario->comentario=$request->comentario;
        $Comentario->persona=$request->persona;
        $Comentario->producto=$request->producto;
        $Comentario->save();
        $name=$request->user()->correo;
        
        $persona = DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->select('usuarios.correo', 'personas.*')
        ->Where('personas.id', $request->persona)->get();
        $persona2 = DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->join('productos', 'personas.id', '=', 'productos.persona')
        ->select('usuarios.correo', 'personas.*','productos.*')
        ->Where('productos.id', $request->producto)->get();
        
        foreach($persona as $value){
        $correo1=$value->correo;
        $sujeto=$value->nombre;
        }
        foreach($persona2 as $value){
        $correo2=$value->correo;
        $producto=$value->producto;
        }
        
        
        }
        return response()->json([$Comentario,],200);

      }
      if( $persona8==$request->persona)
  {


          $request->validate([
            'titulo'=>'required',
            'comentario'=>'required',
            'persona'=>'numeric|required',
            'producto'=>'numeric|required',
             ]);
             
        $Comentario=new Comentario;
        $Comentario->titulo=$request->titulo;
        $Comentario->comentario=$request->comentario;
        $Comentario->persona=$request->persona;
        $Comentario->producto=$request->producto;
        $Comentario->save();
        $name=$request->user()->correo;
        
        $persona = DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->select('usuarios.correo', 'personas.*')
        ->Where('personas.id', $request->persona)->get();
        $persona2 = DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->join('productos', 'personas.id', '=', 'productos.persona')
        ->select('usuarios.correo', 'personas.*','productos.*')
        ->Where('productos.id', $request->producto)->get();
        
        foreach($persona as $value){
        $correo1=$value->correo;
        $sujeto=$value->nombre;
        }
        foreach($persona2 as $value){
        $correo2=$value->correo;
        $producto=$value->producto;
        }
        return response()->json([$Comentario,],200);

      }

    }

  } 
 
  

 


                
       
  return response()->json(["no se puede completar la accion",],400);

      
        }
        $name=$request->user()->correo;
        $this->enviarcorreo($name);
    
        
        return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
    
            }
            public function actualizarComentario(Request $request,$id){
                if  ($request->user()->tokenCan('cliente')||$request->user()->
                tokenCan('administrador')){
                    $request->validate([
                        'titulo'=>'required',
                        'comentario'=>'required',
                       
                         ]);
                    $Comentario=Comentario::find($id);
        $Comentario->titulo=$request->titulo;
        $Comentario->comentario=$request->comentario;
        $Comentario->save();
        $persona = DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->select('usuarios.correo', 'personas.*')
       ->Where('personas.id', $Comentario->persona)->get();
       $persona2 = DB::table('usuarios')
       ->join('personas', 'usuarios.id', '=', 'personas.usuario')
       ->join('productos', 'personas.id', '=', 'productos.persona')
       ->select('usuarios.correo', 'personas.*','productos.*')
      ->Where('productos.id', $Comentario->producto)->get();
     foreach($persona as $value){
$correo1=$value->correo;
$sujeto=$value->nombre;
     }
     foreach($persona2 as $value){
      $correo2=$value->correo;
$producto=$value->producto;
    }
    $this->actualizaciondecomentarioconcorreo($correo1,$correo2,$producto,$sujeto);
        return response()->json([$Comentario,],200);
                }
                $name=$request->user()->correo;
                $this->enviarcorreo($name);
            
                
                return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
            
            }
            public function verComentarios(Request $request){
                if  ($request->user()->tokenCan('provedor')||
                $request->user()->tokenCan('cliente')||
                $request->user()->tokenCan('administrador')||
                $request->user()->tokenCan('Gratis')){  
        $Comentario=Comentario::all();
        return response()->json([$Comentario,],200);
                }
                $name=$request->user()->correo;
                $this->enviarcorreo($name);
            
                
                return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
            
            }
            public function verComentariosporpersona(Request $request,$id){
                if  ($request->user()->tokenCan('cliente')||$request->user()->
                tokenCan('provedor')||$request->user()->
                tokenCan('administrador')||
                $request->user()->tokenCan('Gratis')){  

                $Comentario=Comentario::where('persona',$id)->get();
                return response()->json([$Comentario,],200);
                }
                $name=$request->user()->correo;
                $this->enviarcorreo($name);
            
                
                return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
            
                    }
                    public function verComentariosporproducto(Request $request,$id){
                        if  ($request->user()->tokenCan('cliente')||$request->user()->tokenCan
                        ('provedor')||$request->user()->tokenCan('administrador')||
                        $request->user()->tokenCan('Gratis')){  

                        $Comentario=Comentario::where('producto',$id)->get();
                        return response()->json([$Comentario,],200);
                        }       
                        $name=$request->user()->correo;
                        $this->enviarcorreo($name);
                    
                        
                        return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);
                    
                    }              
            public function eliminarComentario(Request $request,$id){
                if  ($request->user()->tokenCan('cliente')||$request->user()->tokenCan('administrador')){  
          {
        $Comentario=Comentario::find($id);
      
        $persona = DB::table('usuarios')
        ->join('personas', 'usuarios.id', '=', 'personas.usuario')
        ->select('usuarios.correo', 'personas.*')
       ->Where('personas.id', $Comentario->persona)->get();
       $persona2 = DB::table('usuarios')
       ->join('personas', 'usuarios.id', '=', 'personas.usuario')
       ->join('productos', 'personas.id', '=', 'productos.persona')
       ->select('usuarios.correo', 'personas.*','productos.*')
      ->Where('productos.id', $Comentario->producto)->get();
     foreach($persona as $value){
$correo1=$value->correo;
$sujeto=$value->nombre;
     }
     foreach($persona2 as $value){
      $correo2=$value->correo;
$producto=$value->producto;
    }
        $Comentario->delete();
        $Comentario=Comentario::all();
       $this-> eliminaciondecomentarioconcorreo($correo1,$correo2,$producto,$sujeto);
        return response()->json([$Comentario],200);
          }
         
    }
    $name=$request->user()->correo;
    $this->enviarcorreo($name);

    
    return response()->json(["no tiene los permisos requeridos para la siguiente accion",],400);

}
public function enviarcorreo($name){
  $correoadministrador=config('app.mjadministrador');

  $apikey=config('app.mjapikeypub');
  $apisecret=config('app.mjapikeypriv');
  $correoenvia=config('app.mjcorreo');
 $nombreenvia =config('app.mjquienloenvia');
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
           'Name' => $nombreenvia
          ],
          'To' => [
            [
              'Email' =>  $correoadministrador,
              'Name' => "Nuevo usuario"
            ]
          ],
          'Subject' => "Greetings from Mailjet.",
          'TextPart' => "My first Mailjet email",
          'HTMLPart' => "h3>Dear passenger 
          <p> 'El usuario  con los siguientes datos no puede insertar comentario eliminarlo o actualizarlo
          : $persona'</p> sus permisos son:
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
public function correoconcomentarios($correo1,$correo2,$producto,$sujeto){
  $apikey=config('app.mjapikeypub');
  $apisecret=config('app.mjapikeypriv');
  $correoenvia=config('app.mjcorreo');
  $nombreenvia =config('app.mjquienloenvia');
 
    $mj = new \Mailjet\Client( $apikey, $apisecret,true,['version' => 'v3.1']);
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' =>  $correoenvia,
         'Name' => $nombreenvia
        ],
        'To' => [
          [
            'Email' => $correo1,
            'Name' => "Nuevo usuario"
          ]
        ],
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "h3>Dear passenger 
        <p> 'Su comentario fue asignado al producto :$producto sactifactoriamente'</p> 
          <br />",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $mjs = new \Mailjet\Client( $apikey, $apisecret,true,['version' => 'v3.1']);
  $bodys = [
    'Messages' => [
      [
        'From' => [
          'Email' => $correoenvia,
         'Name' =>  $nombreenvia
        ],
        'To' => [
          [
            'Email' => $correo2,
            'Name' => "Nuevo usuario"
          ]
        ],
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "h3>Dear passenger 
        <p> 'la persona  :$sujeto comento a su producto:$producto'</p> 
          <br />",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
 
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response2 = $mjs->post(Resources::$Email, ['body' => $bodys]);

  if($response->success())
      return response()->json(["por favor verifique su correo y ingrese sus datos"
      =>$response->getData()],200);
      return response()->json(["mensaje"=>$response->getData()],500);
} 
public function actualizaciondecomentarioconcorreo($correo1,$correo2,$producto,$sujeto){
  $apikey=config('app.mjapikeypub');
  $apisecret=config('app.mjapikeypriv');
  $correoenvia=config('app.mjcorreo');
  $nombreenvia =config('app.mjquienloenvia');
 
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
            'Email' => $correo1,
            'Name' => "Nuevo usuario"
          ]
        ],
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "h3>Dear passenger 
        <p> 'Su comentario fue actualizado al producto :$producto sactifactoriamente'</p> 
          <br />",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $mjs = new \Mailjet\Client( $apikey, $apisecret,true,['version' => 'v3.1']);
  $bodys = [
    'Messages' => [
      [
        'From' => [
          'Email' => $correoenvia,
         'Name' =>  $nombreenvia
        ],
        'To' => [
          [
            'Email' => $correo2,
            'Name' => "Nuevo usuario"
          ]
        ],
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "h3>Dear passenger 
        <p> 'la persona  :$sujeto a actualizado su comentario al  producto:$producto'</p> 
          <br />",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
 
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response2 = $mjs->post(Resources::$Email, ['body' => $bodys]);

  if($response->success())
      return response()->json(["por favor verifique su correo y ingrese sus datos"
      =>$response->getData()],200);
      return response()->json(["mensaje"=>$response->getData()],500);
} 
public function eliminaciondecomentarioconcorreo($correo1,$correo2,$producto,$sujeto){
  $apikey=config('app.mjapikeypub');
  $apisecret=config('app.mjapikeypriv');
    
  $correoenvia=config('app.mjcorreo');
  $nombreenvia =config('app.mjquienloenvia');
    $mj = new \Mailjet\Client( $apikey, $apisecret,true,['version' => 'v3.1']);
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' => $correoenvia,
         'Name' => $nombreenvia
        ],
        'To' => [
          [
            'Email' => $correo1,
            'Name' => "Nuevo usuario"
          ]
        ],
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "h3>Dear passenger 
        <p> 'Su comentario fue eliminado al producto :$producto sactifactoriamente'</p> 
          <br />",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $mjs = new \Mailjet\Client( $apikey, $apisecret,true,['version' => 'v3.1']);
  $bodys = [
    'Messages' => [
      [
        'From' => [
           'Email' => $correoenvia,
         'Name' => $nombreenvia
        ],
        'To' => [
          [
            'Email' => $correo2,
            'Name' => "Nuevo usuario"
          ]
        ],
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "h3>Dear passenger 
        <p> 'la persona  :$sujeto a eliminado su comentario al  producto:$producto'</p> 
          <br />",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
 
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response2 = $mjs->post(Resources::$Email, ['body' => $bodys]);

  if($response->success())
      return response()->json(["por favor verifique su correo y ingrese sus datos"
      =>$response->getData()],200);
      return response()->json(["mensaje"=>$response->getData()],500);
} 
}
