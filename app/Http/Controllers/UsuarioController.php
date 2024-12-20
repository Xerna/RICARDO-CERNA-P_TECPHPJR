<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = Usuarios::all();
        return view ('usuarios.index', compact('usuarios'));

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
        $request->validate([
            'apodo' => 'required|unique:usuarios',
            'contrasenha' => 'required|min:6'
        ])
        $usuario = Usuario::create([
            'apodo' => $request->apodo,
            'contrasenha' => Hash::make($request->contrasenha),
            'rol' => 'user'
        ]);

        return response()->json([
            'success' => true,
            'usuario' => $usuario
        ])
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
        ])
    }
}
