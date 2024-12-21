/**
 * Clase para gestionar la tabla de usuarios y sus operaciones CRUD
 */
class UsuariosManager {
    /**
     * Constructor de la clase
     * Inicializa la tabla y los eventos
     */
    constructor() {
        // Configuración inicial
        this.routes = window.Routes.usuarios;
        this.tabla = null;
        
        // Selectores del DOM
        this.selectores = {
            tabla: '#usuarios-table',
            botonNuevo: '#nuevo-usuario-btn'
        };

        // Inicialización
        this.inicializarTabla();
        this.inicializarEventos();
    }

    /**
     * Inicializa la tabla de usuarios con DataTables
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
                {data: 'apodo', name: 'apodo', width: '35%'},
                {data: 'contrasenha', name: 'contrasenha', width: '25%'},
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
                url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            scrollY: '50vh',
            scroller: true,
            scrollCollapse: true
        };

        this.tabla = $(this.selectores.tabla).DataTable(configuracionTabla);
    }

    /**
     * Renderiza los botones de acción para cada fila
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
     * Crea un botón HTML con los parámetros especificados
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
     * Inicializa los eventos de la tabla
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
     * Muestra el formulario para crear un nuevo usuario
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
     * Muestra el formulario para editar un usuario
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
     * Muestra el formulario de usuario (crear/editar)
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
                       placeholder="${usuario ? 'Nueva Contraseña (opcional)' : 'Contraseña'}">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: usuario ? 'Actualizar' : 'Guardar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const apodo = document.getElementById('swal-apodo').value;
                const contrasenha = document.getElementById('swal-contrasenha').value;

                if (!apodo) {
                    Swal.showValidationMessage('El apodo es requerido');
                    return false;
                }

                if (!usuario && !contrasenha) {
                    Swal.showValidationMessage('La contraseña es requerida');
                    return false;
                }

                return { apodo, contrasenha };
            }
        });

        return formulario;
    }

    /**
     * Elimina un usuario después de confirmar
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
     * Actualiza la tabla sin perder la página actual
     */
    actualizarTabla() {
        this.tabla.ajax.reload(null, false);
    }

    /**
     * Muestra un mensaje de éxito
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
     * Maneja los errores de las peticiones
     */
    manejarError(error) {
        const mensaje = error.response?.data?.message || 'Ha ocurrido un error';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    }

    /**
     * Maneja los errores específicos de DataTables
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