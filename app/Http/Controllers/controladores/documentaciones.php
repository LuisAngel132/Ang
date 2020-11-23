<?php

namespace App\Http\Controllers\controladores;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\modelos\documentacion;
use \Mailjet\Resources;
use App\modelos\tokens;
use Illuminate\Support\Facades\Storage;
class documentaciones extends Controller
{
    public function guardardocumentos(Request $request){
        if ($request->user()->tokenCan('cliente') ||$request->user()->tokenCan('administrador')||$request->user()->tokenCan('provedor')){
            if($request->hasFile('file')){
                $imagenenbasededatos=new documentacion;
                $path=storage::disk('public')->put('imagenes', $request->file);
                

                     

    $file = $request->file('file');
                //obtenemos el nombre del archivo
                $nombre =  time()."_".$file->getClientOriginalName();
                
                $imagen='storage/app/documentacion/'.$path;
                //indicamos que queremos guardar un nuevo archivo en el disco local
    
                $imagenenbasededatos = new documentacion;
                $imagenenbasededatos->foto = $imagen;
                $imagenenbasededatos->save();
                return response()->json(['su id de la documentacion
                para la persona es el campo id',$imagenenbasededatos],200);
            }
         
    
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
