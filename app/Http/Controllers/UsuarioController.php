<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DataTables;

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
            $query = Usuario::select(['id', 'apodo', 'rol']);
            
            return DataTables::of($query)
                ->addColumn('actions', function($row){
                    return [
                        'id' => $row->id,
                        'can_edit' => true, // O usa auth()->user()->can('edit', $row) si tienes policies
                        'can_delete' => true // O usa auth()->user()->can('delete', $row) si tienes policies
                    ];
                })
                ->filterColumn('apodo', function($query, $keyword) {
                    $query->where('apodo', 'like', "%{$keyword}%");
                })
                ->orderColumn('id', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->orderColumn('apodo', function ($query, $order) {
                    $query->orderBy('apodo', $order);
                })
                ->orderColumn('rol', function ($query, $order) {
                    $query->orderBy('rol', $order);
                })
                ->toJson();
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
    public function edit(Usuario $usuario)
    {
        try {
            return response()->json($usuario);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuario: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usuario $usuario)
    {
        try {
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
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'usuario' => $usuario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 422);
        }
    }   

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Usuario $usuario)
    {
        try {
            $usuario->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }
}
