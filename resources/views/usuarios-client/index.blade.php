@extends('layouts.app')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Usuarios (Client-side)
            <button id="nuevo-usuario-btn" class="btn btn-primary btn-sm pull-right">
                Nuevo Usuario
            </button>
        </h3>
    </div>
    <div class="panel-body">
        <table class="table table-bordered" id="usuarios-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Apodo</th>
                    <th>Contraseña</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->apodo }}</td>
                    <td>{{ $usuario->contrasenha }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info edit-usuario" data-id="{{ $usuario->id }}">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger delete-usuario" data-id="{{ $usuario->id }}">
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/usuarios-client.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.usuariosManager = new UsuariosManager();
    });
</script>
@endpush