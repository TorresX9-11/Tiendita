document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    actualizarContadorCarrito();

    // Agregar evento click a todos los botones "Agregar al carrito"
    const botonesAgregar = document.querySelectorAll('.btn-agregar');
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            const producto = obtenerInfoProducto(this);
            
            // Verificar si el producto ya está en el carrito
            const existeEnCarrito = carrito.findIndex(item => item.id === id);
            
            if (existeEnCarrito !== -1) {
                // Incrementar cantidad si ya existe
                carrito[existeEnCarrito].cantidad += 1;
                mostrarMensaje(`Se ha incrementado la cantidad de ${producto.nombre} en el carrito.`, 'exito');
            } else {
                // Agregar producto nuevo al carrito
                carrito.push({
                    id: id,
                    nombre: producto.nombre,
                    precio: producto.precio,
                    cantidad: 1
                });
                mostrarMensaje(`${producto.nombre} ha sido agregado al carrito.`, 'exito');
            }
            
            // Guardar carrito en localStorage
            localStorage.setItem('carrito', JSON.stringify(carrito));
            
            // Actualizar contador del carrito
            actualizarContadorCarrito();
            
            // Animar botón
            animarBoton(this);
        });
    });
    
    // Funciones auxiliares
    function obtenerInfoProducto(boton) {
        const producto = boton.closest('.producto');
        const nombre = producto.querySelector('h3').textContent;
        const precioTexto = producto.querySelector('.precio').textContent;
        const precio = parseFloat(precioTexto.replace('€', '').trim());
        
        return {
            nombre: nombre,
            precio: precio
        };
    }
    
    function actualizarContadorCarrito() {
        // Crear o actualizar el contador del carrito
        let contador = document.getElementById('contador-carrito');
        
        if (!contador) {
            // Si no existe, crear el elemento
            contador = document.createElement('div');
            contador.id = 'contador-carrito';
            contador.classList.add('contador-carrito');
            
            // Crear el botón del carrito
            const botonCarrito = document.createElement('button');
            botonCarrito.id = 'boton-carrito';
            botonCarrito.classList.add('boton-carrito');
            botonCarrito.innerHTML = '🛒';
            botonCarrito.addEventListener('click', mostrarCarrito);
            
            // Añadir elementos al DOM
            contador.appendChild(botonCarrito);
            document.body.appendChild(contador);
        }
        
        // Establecer la cantidad total de productos
        const total = carrito.reduce((sum, item) => sum + item.cantidad, 0);
        const spanCantidad = document.getElementById('cantidad-carrito') || document.createElement('span');
        spanCantidad.id = 'cantidad-carrito';
        spanCantidad.classList.add('cantidad-carrito');
        spanCantidad.textContent = total;
        
        if (!document.getElementById('cantidad-carrito')) {
            contador.appendChild(spanCantidad);
        }
        
        // Ocultar el contador si está vacío
        if (total === 0) {
            spanCantidad.style.display = 'none';
        } else {
            spanCantidad.style.display = 'flex';
        }
    }
    
    function mostrarCarrito() {
        // Crear modal del carrito
        let modal = document.createElement('div');
        modal.classList.add('modal-carrito');
        
        let contenido = `
            <div class="modal-contenido">
                <span class="cerrar-modal">&times;</span>
                <h2>Tu Carrito</h2>
                <div class="carrito-items">
        `;
        
        if (carrito.length === 0) {
            contenido += `<p class="carrito-vacio">Tu carrito está vacío.</p>`;
        } else {
            // Tabla con los productos
            contenido += `
                <table class="tabla-carrito">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            let total = 0;
            
            carrito.forEach((item, index) => {
                const subtotal = item.precio * item.cantidad;
                total += subtotal;
                
                contenido += `
                    <tr>
                        <td>${item.nombre}</td>
                        <td>€${item.precio.toFixed(2)}</td>
                        <td>
                            <button class="btn-cantidad-menos" data-index="${index}">-</button>
                            <span class="cantidad">${item.cantidad}</span>
                            <button class="btn-cantidad-mas" data-index="${index}">+</button>
                        </td>
                        <td>€${subtotal.toFixed(2)}</td>
                        <td>
                            <button class="btn-eliminar" data-index="${index}">🗑️</button>
                        </td>
                    </tr>
                `;
            });
            
            contenido += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total</td>
                            <td><strong>€${total.toFixed(2)}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="botones-carrito">
                    <button class="btn-vaciar">Vaciar Carrito</button>
                    <button class="btn-checkout">Realizar Pedido</button>
                </div>
            `;
        }
        
        contenido += `
                </div>
            </div>
        `;
        
        modal.innerHTML = contenido;
        document.body.appendChild(modal);
        
        // Añadir eventos después de crear el modal
        const cerrarModal = modal.querySelector('.cerrar-modal');
        cerrarModal.addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        // Eventos para botones de cantidad
        const botonesMenos = modal.querySelectorAll('.btn-cantidad-menos');
        const botonesMas = modal.querySelectorAll('.btn-cantidad-mas');
        const botonesEliminar = modal.querySelectorAll('.btn-eliminar');
        
        botonesMenos.forEach(boton => {
            boton.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                if (carrito[index].cantidad > 1) {
                    carrito[index].cantidad -= 1;
                    actualizarCarrito();
                    document.body.removeChild(modal);
                    mostrarCarrito();
                }
            });
        });
        
        botonesMas.forEach(boton => {
            boton.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                carrito[index].cantidad += 1;
                actualizarCarrito();
                document.body.removeChild(modal);
                mostrarCarrito();
            });
        });
        
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                carrito.splice(index, 1);
                actualizarCarrito();
                document.body.removeChild(modal);
                mostrarCarrito();
            });
        });
        
        // Evento para vaciar carrito
        const botonVaciar = modal.querySelector('.btn-vaciar');
        if (botonVaciar) {
            botonVaciar.addEventListener('click', function() {
                carrito = [];
                actualizarCarrito();
                document.body.removeChild(modal);
                mostrarCarrito();
            });
        }
        
        // Evento para checkout
        const botonCheckout = modal.querySelector('.btn-checkout');
        if (botonCheckout) {
            botonCheckout.addEventListener('click', function() {
                alert('¡Gracias por tu compra! Tu pedido se está procesando.');
                carrito = [];
                actualizarCarrito();
                document.body.removeChild(modal);
            });
        }
    }
    
    function actualizarCarrito() {
        localStorage.setItem('carrito', JSON.stringify(carrito));
        actualizarContadorCarrito();
    }
    
    function mostrarMensaje(texto, tipo) {
        // Crear elemento del mensaje
        const mensaje = document.createElement('div');
        mensaje.classList.add('mensaje-flotante', tipo);
        mensaje.textContent = texto;
        
        // Agregar al DOM
        document.body.appendChild(mensaje);
        
        // Animar entrada
        setTimeout(() => {
            mensaje.classList.add('visible');
        }, 10);
        
        // Eliminar después de un tiempo
        setTimeout(() => {
            mensaje.classList.remove('visible');
            setTimeout(() => {
                document.body.removeChild(mensaje);
            }, 300);
        }, 3000);
    }
    
    function animarBoton(boton) {
        boton.classList.add('pulsando');
        setTimeout(() => {
            boton.classList.remove('pulsando');
        }, 300);
    }
    
    // Agregar estilos dinámicos si no están en el CSS
    if (!document.getElementById('estilos-carrito')) {
        const estilos = document.createElement('style');
        estilos.id = 'estilos-carrito';
        estilos.textContent = `
            .contador-carrito {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
            }
            
            .boton-carrito {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: #4CAF50;
                color: white;
                border: none;
                font-size: 24px;
                cursor: pointer;
                box-shadow: 0 3px 6px rgba(0,0,0,0.2);
                transition: all 0.3s ease;
            }
            
            .boton-carrito:hover {
                background-color: #388E3C;
                transform: scale(1.05);
            }
            
            .cantidad-carrito {
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #ff4d4d;
                color: white;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 12px;
                font-weight: bold;
            }
            
            .modal-carrito {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1100;
            }
            
            .modal-contenido {
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 800px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
                position: relative;
            }
            
            .cerrar-modal {
                position: absolute;
                top: 10px;
                right: 15px;
                font-size: 24px;
                cursor: pointer;
                color: #777;
            }
            
            .tabla-carrito {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }
            
            .tabla-carrito th, .tabla-carrito td {
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            
            .btn-cantidad-menos, .btn-cantidad-mas {
                width: 25px;
                height: 25px;
                border-radius: 50%;
                background-color: #4CAF50;
                color: white;
                border: none;
                cursor: pointer;
                font-size: 16px;
                margin: 0 5px;
            }
            
            .btn-eliminar {
                background-color: transparent;
                border: none;
                cursor: pointer;
                font-size: 18px;
                color: #f44336;
            }
            
            .botones-carrito {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }
            
            .btn-vaciar, .btn-checkout {
                padding: 10px 15px;
                border-radius: 4px;
                border: none;
                cursor: pointer;
                font-weight: 500;
            }
            
            .btn-vaciar {
                background-color: #f44336;
                color: white;
            }
            
            .btn-checkout {
                background-color: #4CAF50;
                color: white;
            }
            
            .mensaje-flotante {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 10px 20px;
                border-radius: 4px;
                color: white;
                z-index: 1200;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
            }
            
            .mensaje-flotante.visible {
                opacity: 1;
                transform: translateY(0);
            }
            
            .mensaje-flotante.exito {
                background-color: #4CAF50;
            }
            
            .mensaje-flotante.error {
                background-color: #f44336;
            }
            
            .pulsando {
                animation: pulso 0.3s;
            }
            
            @keyframes pulso {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(estilos);
    }
});