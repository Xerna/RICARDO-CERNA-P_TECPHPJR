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

<!-- Modal para Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmar Eliminación</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este usuario?</p>
                <div class="form-group">
                    <label>Ingrese su contraseña para confirmar:</label>
                    <input type="password" class="form-control" id="password_confirmation">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">Eliminar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let table;
let deleteUserId;

table = $('#usuarios-table').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
        url: '{{ route("usuarios.index") }}',
        type: 'GET'
    },
    columns: [
        {data: 'id', name: 'id'},
        {data: 'apodo', name: 'apodo'},
        {data: 'rol', name: 'rol'},
        {
            data: null,
            render: function(data) {
                return `
                    <button class="btn btn-sm btn-info" onclick="editarUsuario(${data.id})">
                        Editar
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${data.id})">
                        Eliminar
                    </button>
                `;
            }
        }
    ]
});

function crearUsuario() {
    $('#usuario_id').val('');
    $('#usuarioForm')[0].reset();
    $('#usuarioModal').modal('show');
}

function editarUsuario(id) {
    $.get(`/usuarios/${id}`, function(data) {
        $('#usuario_id').val(data.id);
        $('#apodo').val(data.apodo);
        $('#usuarioModal').modal('show');
    });
}

function guardarUsuario() {
    const id = $('#usuario_id').val();
    const data = {
        apodo: $('#apodo').val(),
        contrasenha: $('#contrasenha').val(),
        _token: $('meta[name="csrf-token"]').attr('content')  // Añadimos el token CSRF
    };

    $.ajax({
        url: id ? `/usuarios/${id}` : '/usuarios',
        type: id ? 'PUT' : 'POST',  // Cambiamos method por type
        data: data,
        success: function(response) {
            if(response.success) {
                $('#usuarioModal').modal('hide');
                table.ajax.reload();
            }
        },
        error: function(xhr) {
            console.log(xhr);
            alert('Error al guardar usuario: ' + xhr.responseText);
        }
    });
}
function eliminarUsuario(id) {
    deleteUserId = id;
    $('#deleteModal').modal('show');
}

function confirmarEliminacion() {
    $.ajax({
        url: `/usuarios/${deleteUserId}`,
        method: 'DELETE',
        data: {
            password_confirmation: $('#password_confirmation').val()
        },
        success: function() {
            $('#deleteModal').modal('hide');
            table.ajax.reload();
        },
        error: function() {
            alert('Error al eliminar usuario');
        }
    });
}
</script>
@endpush