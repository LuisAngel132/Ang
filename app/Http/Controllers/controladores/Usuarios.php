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
        $User->nombre=$request->nombre;
        $User->correo=$request->correo;
        $User->contraseña=Hash::make($request->contraseña);
        $User->rol_asignado_por_permisos=0;
        $User->save();
       $correo= $request->correo;
       $user = DB::table('usuarios')
       ->select('usuarios.id')->where('correo',$request->correo)
   ->get();
     
       return response()->json(["Se a Registrado correctamente",$user,],200);    
            
        
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

 public function LogOut (Request $request){

            return response()->json(["eliminados"=>$request->user()->tokens()->delete()],201);

    }
    public function actualizarpersona(Request $request,$id){
     
               $persona=User::find($id);
          $persona->nombre=$request->nombre;
              $persona->correo=$request->correo;
              $persona->save();
              return response()->json([$persona,],200);
      
            }
           
                       
   


}
