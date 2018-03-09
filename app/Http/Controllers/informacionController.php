<?php

namespace startnow\Http\Controllers;
use startnow\proyectos;
use startnow\informacion;
use Illuminate\Http\Request;

class informacionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    

    public function index(Request $request){
        $id = $request->input('id');
        $proyectos = proyectos::select('nombre')->where('idProyecto',$id)->get();
        $miembrosequipo = miembrosequipo::select('nombres','apellidoP','apellidoM')->where('idMiembro',1)->get();
     
        //return view('informacion',['proyectos'=>$proyectos,'miembrosequipo'=>   $miembrosequipo[0]]);
    }

    public function pago() {
        Conekta::setApiKey("key_QxsrxvJs2u3rjtz9qaysTQ");
        try {
            $cargo = Conekta_Charge::create(array(
                "amount" => "200",
                'currency' => 'MXN',
                'description' => 'Donativo',
                'reference_id' => 'orden',
                'card' => $_POST['conektaTokenId'],
                'details' => array(
                    'name' => 'Prueba',
                    'phone'=> 'Prueba',
                    'email'=> 'Prueba',

                )
            ));
        } catch (Conekta_error $e) {
            return View::make('payment', array('message' => $e->getMessage()));
        }
        return View::make('payment',array('message'=>$charge->status));
    }
    
}
