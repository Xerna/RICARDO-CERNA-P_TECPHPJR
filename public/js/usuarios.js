class UsuariosManager {
    constructor() {
        this.routes = window.Routes.usuarios;
        this.table = null;
        this.initializeDataTable();
        this.initializeEventListeners();
    }

    initializeDataTable() {
        this.table = $('#usuarios-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: {
                url: this.routes.index,
                type: 'GET',
                error: this.handleAjaxError.bind(this)
            },
            columns: [
                {data: 'id', name: 'id', width: '10%'},
                {data: 'apodo', name: 'apodo', width: '35%'},
                {data: 'contrasenha', name: 'contrasenha', width: '25%'},
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    width: '30%',
                    render: this.renderActions.bind(this)
                }
            ],
            order: [[0, 'desc']],
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
                },
                aria: {
                    sortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sortDescending: ": Activar para ordenar la columna de manera descendente"
                }
            },
            deferRender: true,
            scrollY: '50vh',
            scroller: true,
            scrollCollapse: true
        });
    }

    renderActions(data) {
        const buttons = [];
        
        if (data.can_edit) {
            buttons.push(`
                <button class="btn btn-sm btn-info edit-usuario" 
                        data-id="${data.id}">
                    Editar
                </button>
            `);
        }
        
        if (data.can_delete) {
            buttons.push(`
                <button class="btn btn-sm btn-danger delete-usuario" 
                        data-id="${data.id}">
                    Eliminar
                </button>
            `);
        }
        
        return `<div class="btn-group">${buttons.join('')}</div>`;
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
                const response = await axios.post(this.routes.store, formValues);
                
                if (response.data.success) {
                    this.table.ajax.reload(null, false);
                    this.showSuccess('Usuario creado exitosamente');
                }
            } catch (error) {
                this.handleError(error);
            }
        }
    }

     async editarUsuario(id) {
        try {
            const url = this.routes.edit.replace(':id', id);
            const response = await axios.get(url);
            const usuario = response.data;

            const { value: formValues } = await Swal.fire({
                title: 'Editar Usuario',
                html:
                    `<input id="swal-apodo" class="swal2-input" value="${usuario.apodo}" placeholder="Apodo">` +
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
                const updateUrl = this.routes.update.replace(':id', id);
                const updateResponse = await axios.put(updateUrl, formValues);
                
                if (updateResponse.data.success) {
                    this.table.ajax.reload(null, false);
                    this.showSuccess('Usuario actualizado exitosamente');
                }
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    async eliminarUsuario(id) {
        try {
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
                const url = this.routes.destroy.replace(':id', id);
                const response = await axios.delete(url);

                if (response.data.success) {
                    this.table.ajax.reload(null, false);
                    this.showSuccess('Usuario eliminado exitosamente');
                }
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    handleError(error) {
        const message = error.response?.data?.message || 'Ha ocurrido un error';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    handleAjaxError(xhr, error, thrown) {
        console.error('Error en DataTable:', error);
        this.handleError({response: {data: {message: 'Error al cargar los datos'}}});
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