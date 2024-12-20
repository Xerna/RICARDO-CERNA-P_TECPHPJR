@extends('layouts.app')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Usuarios
            <button class="btn btn-primary btn-sm pull-right" onclick="crearUsuario()">
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
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
</table>
    </div>
</div>

<!-- Modal para Crear/Editar -->
<div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="usuarioForm">
                    <input type="hidden" id="usuario_id">
                    <div class="form-group">
                        <label>Apodo</label>
                        <input type="text" class="form-control" id="apodo" name="apodo" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" class="form-control" id="contrasenha" name="contrasenha">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
            </div>
        </div>
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