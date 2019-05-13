<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Validator;
use App\User;
use App\Desenpeno;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteEvaluacion;

 
class DesenpenoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function index(Request $request)
    {
        $nombre   = $request->nombre;
        $apellido = $request->apellido;
      
        $registros = User::orderBy('name')
                         ->nombre($nombre)
                         ->apellido($apellido)
                         ->paginate(12)
                         ->appends($request->all());
        
        return view('evaluacion.index',compact('registros')); 
    }
  
    public function indexjfe(Request $request)
    {
        $usr = Auth::user();
        $permisosUsuario = $usr->roles->pluck('name')->toArray();
        $permisoRh = in_array('rh', $permisosUsuario);
      
        $nombre   = $request->nombre;
        $apellido = $request->apellido;

        $registros = User::nombre($nombre)
                         ->apellido($apellido)
                         ->where('jefe_id', Auth::user()->id)
                         ->orderBy('name')
                         ->get();

        return view('evaluacion.indexjfe', compact('registros', 'permisoRh'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $usuario = User::find($request->user_id); 
        $fecha = Carbon::now();
        
        return view('evaluacion.create',compact('usuario','fecha'));
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'objetivo1'=>'required', 
            'peso_oindividuales'=>'required',
            'peso_oadmon'=>'required',
            'peso_ocultura'=>'required'
        ]);
        
        Desenpeno::create($request->all());
        
        return redirect()->route('evaluacion.indexjfe')
                         ->with('success','Registro creado satisfactoriamente');
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $usr = Auth::user();
        $registros = Desenpeno::find($id);
        $permisosUsuario = $usr->roles->pluck('name')->toArray();
        $permisoRh = in_array('rh', $permisosUsuario);
        $resutado = true;
        //Evaluar de que boton viene
        if($request->descargar_pdf) {
            $view =  \View::make('evaluacion.editPdf', compact('registros', 'resutado'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('reporte.pdf');
        }
        else if($request->enviar_pdf) {
        	    $estado = 'success';
        	    $mensaje = 'El reporte se envio correctamete.';
        	    
        	    if($request->email_evalucion) {
        	        $correo = $request->email_evalucion;
        	        $pdf = \App::make('dompdf.wrapper');
            	    $pdf->loadView('evaluacion.editPdf', compact('registros', 'resutado'));
                    
                  try {
                    Mail::raw('Evaluación de Desempeño', function($message) use($pdf, $correo)
                      {
                          $message->from('no-reply@balluff.com', 'Balluff');
                          $message->to($correo)->subject('Evaluación de Desempeño');
                          $message->attachData($pdf->output(), "Evaluacion_de_Desempeno.pdf");
                      });
                    
                  }
                  catch ( \Exception $e) {
                      $estado = 'error';
                      $mensaje = 'El reporte no se envio.';
                  }
        	    }
              return redirect()->route('evaluaciones.edit', $registros->id )->with($estado, $mensaje);
    	    }
        
        return  view('evaluacion.show',compact('registros', 'usr', 'permisoRh', 'id'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
      
        $registros = Desenpeno::find($id);
        //Evaluar de que boton viene
        if($request->descargar_pdf) {
            $view =  \View::make('evaluacion.editPdf', compact('registros'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('reporte.pdf');
        }
        else if($request->enviar_pdf) {
        	    $estado = 'success';
        	    $mensaje = 'El reporte se envio correctamete.';
        	    
        	    if($request->email_evalucion) {
        	        $correo = $request->email_evalucion;
        	        $pdf = \App::make('dompdf.wrapper');
            	    $pdf->loadView('evaluacion.editPdf', compact('registros'));
                    
                  try {
                    Mail::raw('Evaluación de Desempeño', function($message) use($pdf, $correo)
                      {
                          $message->from('no-reply@balluff.com', 'Balluff');
                          $message->to($correo)->subject('Evaluación de Desempeño');
                          $message->attachData($pdf->output(), "Evaluacion_de_Desempeno.pdf");
                      });
                    
                  }
                  catch ( \Exception $e) {
                      $estado = 'error';
                      $mensaje = 'El reporte no se envio.';
                  }
        	    }
              return redirect()->route('evaluaciones.edit', $registros->id )->with($estado, $mensaje);
    	    }
        
        return view('evaluacion.edit', compact('registros', 'id'));
    }
  
    public function editob($id, Request $request)
    {
      
        $registros = Desenpeno::find($id);
        //Evaluar de que boton viene
        if($request->descargar_pdf) {
            $view =  \View::make('evaluacion.editPdf', compact('registros'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('reporte.pdf');
        }
        else if($request->enviar_pdf) {
        	    $estado = 'success';
        	    $mensaje = 'El reporte se envio correctamete.';
        	    
        	    if($request->email_evalucion) {
        	        $correo = $request->email_evalucion;
        	        $pdf = \App::make('dompdf.wrapper');
            	    $pdf->loadView('evaluacion.editPdf', compact('registros'));
                    
                  try {
                    Mail::raw('Evaluación de Desempeño', function($message) use($pdf, $correo)
                      {
                          $message->from('no-reply@balluff.com', 'Balluff');
                          $message->to($correo)->subject('Evaluación de Desempeño');
                          $message->attachData($pdf->output(), "Evaluacion_de_Desempeno.pdf");
                      });
                    
                  }
                  catch ( \Exception $e) {
                      $estado = 'error';
                      $mensaje = 'El reporte no se envio.';
                  }
        	    }
              return redirect()->route('evaluaciones.objetivos', $registros->id )->with($estado, $mensaje);
    	    }
        
        return view('evaluacion.objetivos', compact('registros', 'id'));
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)    {
        
        $this->validate($request,[ 'objetivo1'=>'required' ]);
        Desenpeno::find($id)->update($request->all());
        
        return redirect()->route('evaluacion.indexjfe')->with('success','Registro actualizado satisfactoriamente');
 
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
         Desempeno::find($id)->delete();
        return redirect()->route('evaluacion.indexjfe')->with('success','Registro eliminado satisfactoriamente');
    }
  
    public function firma(Request $request)
    {
      $firma = Desenpeno::find($request->user_id);
          if($firma){
            $firma->f_empleado = 1;
            $firma->save(); 
          }
       return redirect()->back()->with('success', 'Evaluacion firmada.');
    }
    
    public function firma1(Request $request)
    {
      $firma = Desenpeno::find($request->user_id);
          if($firma){
            $firma->f_jefe= 1;
            $firma->save(); 
          }
       return redirect()->back()->with('success', 'Evaluacion firmada.');
    }
    
    public function firma2(Request $request)
    {
      $firma = Desenpeno::find($request->user_id);
          if($firma){
            $firma->f_rh = 1;
            $firma->save(); 
          }
       return redirect()->back()->with('success', 'Evaluacion firmada.');
    }
    
    public function mievaluacion() {
        $usr = Auth::user();
        if ( !empty($usr->desenpeno) ) {
            return redirect()->route('evaluaciones.show', $usr->desenpeno->id);
        }
        else {
            return redirect()->back()->with('message', 'No cuentas con evaluaciones de desempeño.');
        }
    }
    
    
}