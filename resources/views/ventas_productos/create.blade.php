@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="crear-venta_productos">
    <h1><strong>Nueva Venta de Productos</strong></h1>

    @if( sizeof($errors) > 0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventas_productos.store') }}" method="POST">
        @csrf

        <!-- Fila para buscar por código de barras y nombre del producto -->
        <div class="row mb-3">
            <!-- Entrada para buscar por código de barras -->
            <div class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="codigo_barras" class="form-label">Código de Barras</label>
                <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" placeholder="Ingrese el código de barras del producto" autocomplete="off" onkeypress="buscarProducto(event)">
            </div>

            <!-- Entrada para buscar por nombre del producto -->
            <div class="col-12 col-md-6">
                <label for="producto_nombre" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" placeholder="Buscar producto por nombre" onkeyup="buscarPorNombre()">
                <ul id="sugerencias_nombre" class="list-group" style="display: none; position: absolute; z-index: 10;"></ul>
            </div>            
        </div>

        <!-- Tabla para agregar productos seleccionados -->
        <table class="table table-bordered" id="tabla_productos">
            <thead>
                <tr>
                    <th>Código de Barras</th>
                    <th>Nombre del Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filas dinámicas se agregarán aquí -->
            </tbody>
        </table>

        <!-- Total general -->
        <div class="mb-3 total-general-container">
            <label for="total_general" class="form-label total-label">Total General</label>
            <input type="number" step="0.01" class=" total-input" id="total_general" name="total_general" readonly>
        </div>


            <button type="submit" class="btn btn-primary">Guardar Venta</button>
            <a href="{{ route('ventas_productos.index') }}" class="btn btn-secondary">Cancelar</a>

    </form>
</section>

<script>
    const productos = @json($productos); // Obtener todos los productos disponibles desde la base de datos
    let totalGeneral = 0;

    // Buscar producto por código de barras
    function buscarProducto(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const codigoBarras = document.getElementById('codigo_barras').value.trim();
            const producto = productos.find(p => p.codigo_barras === codigoBarras);

            if (producto) {
                agregarProductoTabla(producto);
                document.getElementById('codigo_barras').value = ''; // Limpiar el campo
            } else {
                alert('Producto no encontrado.');
            }
        }
    }

    // Buscar productos por nombre
    function buscarPorNombre() {
        const inputNombre = document.getElementById('producto_nombre').value.toLowerCase();
        const listaProductos = document.getElementById('sugerencias_nombre');
        listaProductos.innerHTML = ''; // Limpiar las sugerencias previas

        if (inputNombre.length > 0) {
            const coincidencias = productos.filter(p => p.nombre.toLowerCase().includes(inputNombre));

            if (coincidencias.length > 0) {
                listaProductos.style.display = 'block';
                coincidencias.forEach(producto => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item');
                    li.classList.add('list-group-item-action');
                    li.textContent = producto.nombre;
                    li.setAttribute('data-id', producto.id);
                    li.setAttribute('data-codigo', producto.codigo_barras);
                    li.setAttribute('data-precio', producto.precio);
                    li.setAttribute('data-stock', producto.stock);
                    li.onclick = function() {
                        seleccionarProducto(producto.id);
                    };
                    listaProductos.appendChild(li);
                });
            } else {
                listaProductos.style.display = 'none';
            }
        } else {
            listaProductos.style.display = 'none';
        }
    }

    // Seleccionar un producto de las sugerencias
    function seleccionarProducto(productoId) {
        const producto = productos.find(p => p.id === productoId);
        if (producto) {
            agregarProductoTabla(producto);
            document.getElementById('producto_nombre').value = ''; // Limpiar el campo de nombre
            document.getElementById('sugerencias_nombre').style.display = 'none'; // Ocultar las sugerencias
        }
    }

    // Agregar producto a la tabla
    function agregarProductoTabla(producto) {
        if (producto.stock === 0) {
            alert(`El producto "${producto.nombre}" no tiene stock disponible.`);
            return;
        }

        const tablaBody = document.querySelector('#tabla_productos tbody');

        // Verificar si el producto ya está en la tabla
        if (document.getElementById(`fila_${producto.id}`)) {
            alert('El producto ya fue agregado.');
            return;
        }

        const fila = document.createElement('tr');
        fila.id = `fila_${producto.id}`;

        // Columnas de la fila
        fila.innerHTML = `
            <td>${producto.codigo_barras}</td>
            <td>${producto.nombre}</td>
            <td>
                <input type="number" class="form-control" id="cantidad_${producto.id}" 
                    name="productos[${producto.id}][cantidad]" 
                    min="1" max="${producto.stock}" value="1" 
                    oninput="actualizarSubtotal(${producto.id}, ${producto.precio}, ${producto.stock})">
            </td>
            <td>${Number(producto.precio).toFixed(2)}</td>
            <td>
                <input type="number" class="form-control subtotal" id="subtotal_${producto.id}" 
                    name="productos[${producto.id}][subtotal]" 
                    value="${Number(producto.precio).toFixed(2)}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="eliminarProducto(${producto.id})">Eliminar</button>
            </td>
        `;

        tablaBody.appendChild(fila);
        actualizarTotalGeneral();
    }


    // Actualizar subtotal al cambiar cantidad
    function actualizarSubtotal(productoId, precioUnitario, stockDisponible) {
        const cantidadInput = document.getElementById(`cantidad_${productoId}`);
        const subtotalInput = document.getElementById(`subtotal_${productoId}`);

        let cantidad = parseInt(cantidadInput.value);

        if (cantidad > stockDisponible) {
            alert(`Solo hay ${stockDisponible} unidades disponibles.`);
            cantidadInput.value = stockDisponible;
            cantidad = stockDisponible;
        }

        if (cantidad < 1) {
            cantidadInput.value = 1;
            cantidad = 1;
        }

        const subtotal = cantidad * precioUnitario;
        subtotalInput.value = subtotal.toFixed(2);

        actualizarTotalGeneral();
    }


    // Eliminar producto de la tabla
    function eliminarProducto(productoId) {
        const fila = document.getElementById(`fila_${productoId}`);
        fila.remove();
        actualizarTotalGeneral();
    }

    // Actualizar total general
    function actualizarTotalGeneral() {
        const subtotales = document.querySelectorAll('.subtotal');
        totalGeneral = 0;

        subtotales.forEach(input => {
            totalGeneral += parseFloat(input.value);
        });

        document.getElementById('total_general').value = totalGeneral.toFixed(2);
    }
</script>
@endsection
