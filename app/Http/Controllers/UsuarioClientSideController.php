<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
class UsuarioClientSideController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all(['id', 'apodo', 'contrasenha']);
        return view('usuarios-client.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'apodo' => 'required|unique:usuarios',
                'contrasenha' => 'required|min:6'
            ]);
    
            $usuario = Usuario::create([
                'apodo' => $request->apodo,
                'contrasenha' => Hash::make($request->contrasenha)
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

    public function destroy(Usuario $usuario)
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
