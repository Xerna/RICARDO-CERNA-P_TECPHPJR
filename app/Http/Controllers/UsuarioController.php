<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $usuarios = Usuario::all();
            return response()->json(['data' => $usuarios]);
        }
    
        return view('usuarios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'apodo' => 'required|unique:usuarios',
                'contrasenha' => 'required|min:6'
            ]);
    
            $usuario = Usuario::create([
                'apodo' => $request->apodo,
                'contrasenha' => Hash::make($request->contrasenha),
                'rol' => 'user'
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'usuario' => $usuario
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'apodo' => 'required|unique:usuarios,apodo,' . $usuario->id,
            'contrasenha' => 'nullable|min:6'
        ]);

        $data = ['apodo' => $request->apodo];

        if($request->filled('contrasenha')){
            $data['contrasenha'] = Hash::make($request->contrasenha);
        }

        $usuario->update($data);

        return response()->json([
            'sucesss' => true,
            'usuario' => $usuario
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Usuario $usuario)
    {
        if(!Hash::check($request->password_confirmation, auth()->user()->contrasenha)){
            return response()->json([
                'sucess' => false,
                'message' => 'Contraseña incorrecta'
            ], '403');
        }
        $usuario->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
