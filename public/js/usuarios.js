/**
 * Clase para gestionar la tabla de usuarios y sus operaciones CRUD
 */
class UsuariosManager {
    /**
     * Constructor de la clase
     */
    constructor() {
        this.routes = window.Routes.usuarios;
        this.tabla = null;
        
        this.selectores = {
            tabla: '#usuarios-table',
            botonNuevo: '#nuevo-usuario-btn'
        };

        this.inicializarTabla();
        this.inicializarEventos();
    }

    /**
     * Inicializa la tabla de usuarios
     */
    inicializarTabla() {
        const configuracionTabla = {
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: {
                url: this.routes.index,
                type: 'GET',
                error: (xhr, error, thrown) => this.manejarErrorAjax(xhr, error, thrown)
            },
            columns: [
                {data: 'id', name: 'id', width: '10%'},
                {data: 'apodo', name: 'apodo', width: '60%'},
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    width: '30%',
                    render: (data) => this.renderizarBotones(data)
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
                infoPostFix: "",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                },
                aria: {
                    sortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sortDescending: ": Activar para ordenar la columna de manera descendente"
                }
            },
            scrollY: '50vh',
            scroller: true,
            scrollCollapse: true
        };

        this.tabla = $(this.selectores.tabla).DataTable(configuracionTabla);
    }

    /**
     * Renderiza los botones de acción
     */
    renderizarBotones(data) {
        const botones = [];
        
        if (data.can_edit) {
            botones.push(this.crearBoton('edit', data.id, 'info', 'Editar'));
        }
        
        if (data.can_delete) {
            botones.push(this.crearBoton('delete', data.id, 'danger', 'Eliminar'));
        }
        
        return `<div class="btn-group">${botones.join('')}</div>`;
    }

    /**
     * Crea un botón HTML
     */
    crearBoton(accion, id, tipo, texto) {
        return `
            <button class="btn btn-sm btn-${tipo} ${accion}-usuario" 
                    data-id="${id}">
                ${texto}
            </button>
        `;
    }

    /**
     * Inicializa los eventos
     */
    inicializarEventos() {
        $(this.selectores.botonNuevo).on('click', () => this.crearUsuario());
        
        $(this.selectores.tabla).on('click', '.edit-usuario', (e) => {
            const id = $(e.currentTarget).data('id');
            this.editarUsuario(id);
        });
        
        $(this.selectores.tabla).on('click', '.delete-usuario', (e) => {
            const id = $(e.currentTarget).data('id');
            this.eliminarUsuario(id);
        });
    }

    /**
     * Crear nuevo usuario
     */
    async crearUsuario() {
        const formulario = await this.mostrarFormulario('Nuevo Usuario');
        
        if (!formulario) return;

        try {
            const respuesta = await axios.post(this.routes.store, formulario);
            
            if (respuesta.data.success) {
                this.actualizarTabla();
                this.mostrarMensajeExito('Usuario creado exitosamente');
            }
        } catch (error) {
            this.manejarError(error);
        }
    }

    /**
     * Editar usuario existente
     */
    async editarUsuario(id) {
        try {
            const respuesta = await axios.get(this.routes.edit.replace(':id', id));
            const usuario = respuesta.data;

            const formulario = await this.mostrarFormulario('Editar Usuario', usuario);
            
            if (!formulario) return;

            const respuestaUpdate = await axios.put(
                this.routes.update.replace(':id', id), 
                formulario
            );
            
            if (respuestaUpdate.data.success) {
                this.actualizarTabla();
                this.mostrarMensajeExito('Usuario actualizado exitosamente');
            }
        } catch (error) {
            this.manejarError(error);
        }
    }

    /**
     * Muestra el formulario de usuario
     */
 /**
     * Muestra el formulario de usuario
     */
 async mostrarFormulario(titulo, usuario = null) {
    const { value: formulario } = await Swal.fire({
        title: titulo,
        html: `
            <input id="swal-apodo" 
                   class="swal2-input" 
                   placeholder="Apodo" 
                   value="${usuario?.apodo || ''}">
            <input id="swal-contrasenha" 
                   type="password" 
                   class="swal2-input" 
                   placeholder="${usuario ? 'Nueva Contraseña' : 'Contraseña'}">
            <div class="text-danger" style="display: none;margin-top: 10px;" id="password-requirements">
                ${usuario ? 'Si ingresa una contraseña, debe tener al menos 6 caracteres' : 'La contraseña debe tener al menos 6 caracteres'}
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: usuario ? 'Actualizar' : 'Guardar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            // Agregar validación en tiempo real para la contraseña
            const passwordInput = document.getElementById('swal-contrasenha');
            const requirements = document.getElementById('password-requirements');
            
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 6) {
                    requirements.style.display = 'block';
                } else {
                    requirements.style.display = 'none';
                }
            });
        },
        preConfirm: () => {
            const apodo = document.getElementById('swal-apodo').value;
            const contrasenha = document.getElementById('swal-contrasenha').value;

            if (!apodo) {
                Swal.showValidationMessage('El apodo es requerido');
                return false;
            }

            if (!usuario && !contrasenha) {
                Swal.showValidationMessage('La contraseña es requerida para nuevos usuarios');
                return false;
            }

            // En edición, si se envía la contraseña, no puede estar vacía
            if (contrasenha === '') {
                Swal.showValidationMessage('La contraseña no puede estar vacía');
                return false;
            }

            // Validar longitud mínima si hay contraseña
            if (contrasenha && contrasenha.length < 6) {
                Swal.showValidationMessage('La contraseña debe tener al menos 6 caracteres');
                return false;
            }

            return { apodo, contrasenha };
        }
    });

    return formulario;
}


    /**
     * Eliminar usuario
     */
    async eliminarUsuario(id) {
        try {
            const confirmacion = await Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (!confirmacion.isConfirmed) return;

            const respuesta = await axios.delete(
                this.routes.destroy.replace(':id', id)
            );

            if (respuesta.data.success) {
                this.actualizarTabla();
                this.mostrarMensajeExito('Usuario eliminado exitosamente');
            }
        } catch (error) {
            this.manejarError(error);
        }
    }

    /**
     * Actualiza la tabla
     */
    actualizarTabla() {
        this.tabla.ajax.reload(null, false);
    }

    /**
     * Muestra mensaje de éxito
     */
    mostrarMensajeExito(mensaje) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: mensaje,
            timer: 2000,
            showConfirmButton: false
        });
    }

    /**
     * Maneja errores generales
     */
    manejarError(error) {
        let mensaje = 'Ha ocurrido un error';
        
        if (error.response?.data?.message) {
            // Si el mensaje es un objeto (errores de validación de Laravel)
            if (typeof error.response.data.message === 'object') {
                mensaje = Object.values(error.response.data.message)
                    .flat()
                    .join('\n');
            } else {
                mensaje = error.response.data.message;
            }
        }

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    }

    /**
     * Maneja errores de DataTables
     */
    manejarErrorAjax(xhr, error, thrown) {
        console.error('Error en DataTable:', error);
        this.manejarError({
            response: {
                data: {
                    message: 'Error al cargar los datos'
                }
            }
        });
    }
}