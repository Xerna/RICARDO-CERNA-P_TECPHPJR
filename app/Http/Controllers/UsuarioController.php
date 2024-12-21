<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use DataTables;

class UsuarioController extends Controller
{
    /**
     * Reglas de validación para usuario
     */
    private $rules = [
        'create' => [
            'apodo' => 'required|unique:usuarios',
            'contrasenha' => 'required|min:6'
        ],
        'update' => [
            'apodo' => 'required|unique:usuarios,apodo,%s', // %s será reemplazado por el ID
            'contrasenha' => 'nullable|min:6'
        ]
    ];

    /**
     * Mostrar listado de usuarios
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('usuarios.index');
        }

        return $this->getDatatableData();
    }

    /**
     * Obtener datos para DataTable
     *
     * @return mixed
     */
    private function getDatatableData()
    {
        $query = Usuario::select(['id', 'apodo']);
        
        return DataTables::of($query)
            ->addColumn('actions', function($usuario) {
                return [
                    'id' => $usuario->id,
                    'can_edit' => true, 
                    'can_delete' => true 
                ];
            })
            ->filterColumn('apodo', function($query, $keyword) {
                $query->where('apodo', 'like', "%{$keyword}%");
            })
            ->make(true);
    }

    /**
     * Almacenar un nuevo usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate($this->rules['create']);

            $usuario = Usuario::create([
                'apodo' => $request->apodo,
                'contrasenha' => Hash::make($request->contrasenha),
                'rol' => 'user'
            ]);

            return $this->successResponse('Usuario creado exitosamente', $usuario);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al crear usuario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar información de un usuario para edición
     *
     * @param Usuario $usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Usuario $usuario)
    {
        try {
            return response()->json($usuario);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un usuario específico
     *
     * @param Request $request
     * @param Usuario $usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Usuario $usuario)
    {
        try {
            // Reemplazar %s con el ID del usuario en la regla unique
            $rules = $this->rules['update'];
            $rules['apodo'] = sprintf($rules['apodo'], $usuario->id);
            
            $request->validate($rules);
            
            $data = ['apodo' => $request->apodo];
            
            if ($request->filled('contrasenha')) {
                $data['contrasenha'] = Hash::make($request->contrasenha);
            }

            $usuario->update($data);

            return $this->successResponse('Usuario actualizado exitosamente', $usuario);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un usuario específico
     *
     * @param Usuario $usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Usuario $usuario)
    {
        try {
            $usuario->delete();
            return $this->successResponse('Usuario eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al eliminar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Respuesta exitosa estándar
     *
     * @param string $message
     * @param mixed|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse($message, $data = null)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data) {
            $response['usuario'] = $data;
        }

        return response()->json($response);
    }

    /**
     * Respuesta de error estándar
     *
     * @param string|array $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    private function errorResponse($message, $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }
}