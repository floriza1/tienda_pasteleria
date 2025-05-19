// Constante global
const urlAPI = "/api/"; // Ruta base de la API

//******FUNCIONES PARA MANEJAR PRODUCTOS **********/

// Obtener productos desde la API
async function obtenerProductosDesdeAPI() {
    try {
        const respuesta = await fetch(urlAPI + "productos.php");
        
        if (!respuesta.ok) {
            throw new Error(`Error en la petición: ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        
        // validar que los objetos tienen la estructura adecuada
        if (!datos.productos || !Array.isArray(datos.productos)) {
            throw new Error("Los datos recibidos no son válidos");
        }

        // Guardar en localStorage
        localStorage.setItem("productos", JSON.stringify(datos.productos));
        
        return datos.productos;
    } catch (error) {
        console.error("Error al obtener productos:", error);
        return [];
    }
}

// Obtener productos desde localStorage
function obtenerProductosDesdeCache() {
    try {
        const productosGuardados = localStorage.getItem("productos");
        return productosGuardados ? JSON.parse(productosGuardados) : [];
    } catch (error) {
        console.error("Error al leer productos guardados:", error);
        return [];
    }
}

// Calcular precio del producto
function calcularPrecio(producto) {
    switch(producto.tipo_precio) {
        case "unitario": return producto.precio_unitario;
        case "mediano": return producto.precio_mediano;
        case "grande": return producto.precio_grande;
        default: return 0;
    }
}

//******FUNCIONES PARA MOSTRAR PRODUCTOS **********/

// Mostrar lista de productos en un contenedor
function mostrarListaProductos(productos, idContenedor) {
    const contenedor = document.getElementById(idContenedor);
    if (!contenedor) return;

    contenedor.innerHTML = `
        <div class="row">
            ${productos.map(producto => crearTarjetaProducto(producto)).join('')}
        </div>
    `;
}

// Crear tarjeta individual de producto
function crearTarjetaProducto(producto) {
    return `
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow">
                <img src="${producto.imagen}" class="card-img-top" alt="${producto.nombre}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${producto.nombre}</h5>
                    <p class="card-text">${producto.descripcion}</p>
                    <p class="fw-bold">${calcularPrecio(producto)} €</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center border rounded-pill overflow-hidden" style="width: 120px;">
                            <button class="btn btn-sm px-2 py-1 text-white border-0" onclick="cambiarCantidad(this, -1)">−</button>
                            <input type="text" class="form-control text-center border-0" value="1" style="width: 40px;" readonly>
                            <button class="btn btn-sm px-2 py-1 text-white border-0" onclick="cambiarCantidad(this, 1)">+</button>
                        </div>
                        <button id="agrearAlCarrito" class="btn btn-danger rounded-pill ms-3" onclick="agregarAlCarrito(${producto.id_producto}, this)">Agregar al carrito</button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Cambiar cantidad de productos
function cambiarCantidad(boton, cambio) {
    const input = boton.parentElement.querySelector('input');
    let cantidad = parseInt(input.value) + cambio;
    if (cantidad < 1) cantidad = 1;
    input.value = cantidad;
}

function mostrarSeccion(idSeccionAMostrar) {
    const secciones = [
        'vista-destacados',
        'vista-nosotros',
        'vista-contacto', 
        'cont_Bocaditos_dulces',
        'cont_Bocaditos_salados',
        'cont_postres',
        'cont_tartas',
        'cont_Personales_dulces',
        'cont_Personales_salados'
    ];

    // Mostrar u ocultar secciones
    secciones.forEach(idSeccion => {
        const elemento = document.getElementById(idSeccion);
        if (elemento) {
            elemento.style.display = (idSeccion === idSeccionAMostrar) ? 'block' : 'none';
        }
    });

    // Control especial para el título "Productos"
    const tituloProductos = document.querySelector('.seccion-productos');
    if (tituloProductos) {
        tituloProductos.style.display = idSeccionAMostrar.startsWith('cont_') ? 'block' : 'none';
    }

    // Desplazamiento suave ajustado para el header
    const seccionDestino = document.getElementById(idSeccionAMostrar);
    if (seccionDestino) {
        const headerHeight = document.querySelector('header').offsetHeight;
        const offsetPosition = seccionDestino.offsetTop - headerHeight;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

/******FUNCIONES PRINCIPAL **********/

// Cargar y mostrar todos los productos
async function cargarYMostrarProductos() {
    try {
        // 1. Obtener productos (de cache o API)
        let productos = obtenerProductosDesdeCache();
        
        if (productos.length === 0) {
            productos = await obtenerProductosDesdeAPI();
        }

        // 2. Mostrar productos destacados
        const productosDestacados = productos.filter(p => p.destacado);
        mostrarListaProductos(productosDestacados, "productos-destacados");

        // 3. Mostrar productos en los dropdowns
        mostrarProductosEnDropdowns(productos);

    } catch (error) {
        console.error("Error al cargar productos:", error);
    }
}

// Mostrar productos en los menús desplegables
function mostrarProductosEnDropdowns(productos) {
    // Bocaditos Dulces
    const bocaditosDulces = productos.filter(p => 
        p.categoria_principal === "BOCADITOS" && p.subcategoria === "dulces"
    );
    mostrarListaProductos(bocaditosDulces, "cont_Bocaditos_dulces");

    // Bocaditos Salados
    const bocaditosSalados = productos.filter(p => 
        p.categoria_principal === "BOCADITOS" && p.subcategoria === "salados"
    );
    mostrarListaProductos(bocaditosSalados, "cont_Bocaditos_salados");

    // Postres
    const postres = productos.filter(p => 
        p.categoria_principal === "POSTRES" && p.subcategoria === "postres"
    );
    mostrarListaProductos(postres, "cont_postres");

    // Tartas
    const tartas = productos.filter(p => 
        p.categoria_principal === "POSTRES" && p.subcategoria === "tartas"
    );
    mostrarListaProductos(tartas, "cont_tartas");

    // Personales Dulces
    const personalesDulces = productos.filter(p => 
        p.categoria_principal === "PERSONALES" && p.subcategoria === "dulces"
    );
    mostrarListaProductos(personalesDulces, "cont_Personales_dulces");

    // Personales Salados
    const personalesSalados = productos.filter(p => 
        p.categoria_principal === "PERSONALES" && p.subcategoria === "salados"
    );
    mostrarListaProductos(personalesSalados, "cont_Personales_salados");
}

// =============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// =============================================
document.addEventListener("DOMContentLoaded", cargarYMostrarProductos);