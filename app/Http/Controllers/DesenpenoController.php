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

 
class DesenpenoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mievaluacion()
    {
        $registros=Auth::user()->desenpeno; 
        return view('desenpeno.mievaluacion',compact('registros'));
    }
    public function index()
    {
        //
        $registros = Desenpeno::orderBy('id','DESC')->paginate(12);
        return view('desenpeno.index',compact('registros')); 

    }
  
  public function indexjfe()
    {
        $registros = User::where('jefe_id', Auth::user()->id)->get();
        return view('desenpeno.indexjfe',compact('registros','usuarios')); 

    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      $usuario = User::find($request->user_id); 
        return view('desenpeno.create',compact('usuario'));
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request,[ 'objetivo1'=>'required']);
        Desenpeno::create($request->all());
        return redirect()->route('desenpeno.index')->with('success','Registro creado satisfactoriamente');
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $registros = Desenpeno::find($id);
        return  view('desenpeno.show',compact('registros'));
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $registros = Desenpeno::find($id);
        return view('desenpeno.edit',compact('registros'));
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)    {
        //
        $this->validate($request,[ 'objetivo1'=>'required' ]);
 
        Desenpeno::find($id)->update($request->all());
        return redirect()->route('desenpeno.indexjfe')->with('success','Registro actualizado satisfactoriamente');
 
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
        return redirect()->route('desenpeno.indexjfe')->with('success','Registro eliminado satisfactoriamente');
    }
  
  
}