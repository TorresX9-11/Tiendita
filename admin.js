document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario
    const formulario = document.querySelector('form');
    if (formulario) {
        formulario.addEventListener('submit', function(event) {
            const nombre = document.getElementById('nombre').value.trim();
            const precio = document.getElementById('precio').value.trim();
            
            let valido = true;
            let mensajes = [];
            
            // Validar nombre
            if (nombre === '') {
                valido = false;
                mensajes.push('El nombre de la fruta es obligatorio.');
            }
            
            // Validar precio
            if (precio === '') {
                valido = false;
                mensajes.push('El precio es obligatorio.');
            } else if (isNaN(precio) || parseFloat(precio) < 0) {
                valido = false;
                mensajes.push('El precio debe ser un número positivo.');
            }
            
            // Si hay errores, prevenir envío del formulario y mostrar mensajes
            if (!valido) {
                event.preventDefault();
                mostrarMensajesError(mensajes);
            }
        });
    }
    
    // Función para mostrar mensajes de error
    function mostrarMensajesError(mensajes) {
        // Buscar o crear contenedor de mensajes
        let contenedorMensajes = document.querySelector('.mensajes-error');
        
        if (!contenedorMensajes) {
            contenedorMensajes = document.createElement('div');
            contenedorMensajes.className = 'mensajes-error mensaje error';
            formulario.insertBefore(contenedorMensajes, formulario.firstChild);
        }
        
        // Limpiar mensajes anteriores
        contenedorMensajes.innerHTML = '';
        
        // Agregar listado de mensajes
        const lista = document.createElement('ul');
        
        mensajes.forEach(mensaje => {
            const item = document.createElement('li');
            item.textContent = mensaje;
            lista.appendChild(item);
        });
        
        contenedorMensajes.appendChild(lista);
    }
    
    // Filtro y búsqueda en la tabla
    const inputBusqueda = document.createElement('input');
    inputBusqueda.type = 'text';
    inputBusqueda.id = 'busqueda';
    inputBusqueda.placeholder = 'Buscar fruta...';
    inputBusqueda.className = 'input-busqueda';
    
    const tablaContainer = document.querySelector('.admin-lista');
    if (tablaContainer) {
        // Insertar el input de búsqueda antes de la tabla
        const tabla = tablaContainer.querySelector('table');
        if (tabla) {
            tablaContainer.insertBefore(inputBusqueda, tabla);
            
            // Evento para filtrar la tabla
            inputBusqueda.addEventListener('input', function() {
                const termino = this.value.toLowerCase();
                const filas = tabla.querySelectorAll('tbody tr');
                
                filas.forEach(fila => {
                    const texto = fila.textContent.toLowerCase();
                    if (texto.includes(termino)) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        }
    }
    
    // Ordenamiento de la tabla
    const cabeceras = document.querySelectorAll('table th');
    cabeceras.forEach(cabecera => {
        if (cabecera.textContent !== 'Acciones') {
            cabecera.style.cursor = 'pointer';
            cabecera.title = 'Haz clic para ordenar';
            
            // Agregar icono de ordenamiento
            const icono = document.createElement('span');
            icono.className = 'icono-orden';
            icono.innerHTML = ' &#8597;'; // Flecha arriba/abajo
            cabecera.appendChild(icono);
            
            // Agregar evento click para ordenar
            cabecera.addEventListener('click', function() {
                const tabla = this.closest('table');
                const indice = Array.from(this.parentNode.children).indexOf(this);
                const filas = Array.from(tabla.querySelectorAll('tbody tr'));
                const direccion = this.getAttribute('data-orden') === 'asc' ? -1 : 1;
                
                // Ordenar filas
                filas.sort((a, b) => {
                    let valorA = a.children[indice].textContent.trim();
                    let valorB = b.children[indice].textContent.trim();
                    
                    // Si es numérico, convertir para ordenamiento correcto
                    if (!isNaN(valorA) && !isNaN(valorB)) {
                        return direccion * (parseFloat(valorA) - parseFloat(valorB));
                    }
                    
                    // Ordenamiento alfabético
                    return direccion * valorA.localeCompare(valorB);
                });
                
                // Actualizar atributo de dirección
                this.setAttribute('data-orden', direccion === 1 ? 'asc' : 'desc');
                
                // Actualizar iconos de todas las cabeceras
                cabeceras.forEach(c => {
                    if (c !== this) {
                        c.removeAttribute('data-orden');
                        c.querySelector('.icono-orden').innerHTML = ' &#8597;';
                    } else {
                        c.querySelector('.icono-orden').innerHTML = direccion === 1 ? ' &#8593;' : ' &#8595;';
                    }
                });
                
                // Reordenar DOM
                const tbody = tabla.querySelector('tbody');
                filas.forEach(fila => tbody.appendChild(fila));
            });
        }
    });
    
    // Agregar estilos adicionales
    const estilos = document.createElement('style');
    estilos.textContent = `
        .input-busqueda {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .mensajes-error ul {
            margin: 5px 0 5px 20px;
        }
        
        th[data-orden] {
            background-color: #e8f5e9;
        }
        
        .icono-orden {
            display: inline-block;
            margin-left: 5px;
        }
    `;
    document.head.appendChild(estilos);
});
