class UsuariosManager {
    constructor() {
        this.table = null;
        this.initializeDataTable();
        this.initializeEventListeners();
    }

    initializeDataTable() {
        this.table = $('#usuarios-table').DataTable({
            pageLength: 10,
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
    }

    initializeEventListeners() {
        $('#nuevo-usuario-btn').on('click', () => this.crearUsuario());
        
        $('#usuarios-table').on('click', '.edit-usuario', (e) => {
            const id = $(e.currentTarget).data('id');
            this.editarUsuario(id);
        });
        
        $('#usuarios-table').on('click', '.delete-usuario', (e) => {
            const id = $(e.currentTarget).data('id');
            this.eliminarUsuario(id);
        });
    }

    async crearUsuario() {
        const { value: formValues } = await Swal.fire({
            title: 'Nuevo Usuario',
            html:
                '<input id="swal-apodo" class="swal2-input" placeholder="Apodo">' +
                '<input id="swal-contrasenha" type="password" class="swal2-input" placeholder="Contraseña">',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    apodo: document.getElementById('swal-apodo').value,
                    contrasenha: document.getElementById('swal-contrasenha').value
                }
            }
        });

        if (formValues) {
            try {
                const response = await axios.post('/usuarios-client', formValues);
                
                if (response.data.success) {
                    // Agregamos la nueva fila a la tabla
                    const usuario = response.data.usuario;
                    const newRow = this.table.row.add([
                        usuario.id,
                        usuario.apodo,
                        usuario.contrasenha,
                        this.createActionButtons(usuario.id)
                    ]).draw();

                    this.showSuccess('Usuario creado exitosamente');
                }
            } catch (error) {
                this.handleError(error);
            }
        }
    }

    async editarUsuario(id) {
        const row = this.table.row($(`button[data-id="${id}"]`).closest('tr'));
        const rowData = row.data();

        const { value: formValues } = await Swal.fire({
            title: 'Editar Usuario',
            html:
                `<input id="swal-apodo" class="swal2-input" value="${rowData[1]}" placeholder="Apodo">` +
                '<input id="swal-contrasenha" type="password" class="swal2-input" placeholder="Nueva Contraseña (opcional)">',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    apodo: document.getElementById('swal-apodo').value,
                    contrasenha: document.getElementById('swal-contrasenha').value
                }
            }
        });

        if (formValues) {
            try {
                const response = await axios.put(`/usuarios-client/${id}`, formValues);
                
                if (response.data.success) {
                    const usuario = response.data.usuario;
                    // Actualizamos los datos en la tabla
                    row.data([
                        usuario.id,
                        usuario.apodo,
                        usuario.contrasenha,
                        this.createActionButtons(usuario.id)
                    ]).draw();

                    this.showSuccess('Usuario actualizado exitosamente');
                }
            } catch (error) {
                this.handleError(error);
            }
        }
    }

    async eliminarUsuario(id) {
        const result = await Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/usuarios-client/${id}`);

                if (response.data.success) {
                    // Eliminamos la fila de la tabla
                    this.table.row($(`button[data-id="${id}"]`).closest('tr')).remove().draw();
                    this.showSuccess('Usuario eliminado exitosamente');
                }
            } catch (error) {
                this.handleError(error);
            }
        }
    }

    createActionButtons(id) {
        return `
            <div class="btn-group">
                <button class="btn btn-sm btn-info edit-usuario" data-id="${id}">
                    Editar
                </button>
                <button class="btn btn-sm btn-danger delete-usuario" data-id="${id}">
                    Eliminar
                </button>
            </div>
        `;
    }

    handleError(error) {
        const message = error.response?.data?.message || 'Ha ocurrido un error';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }
}