@extends('layouts.app')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Usuarios
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
        </table>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/usuarios.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.usuariosManager = new UsuariosManager();
    });
</script>
@endpush