// =============================================
// MÓDULO: ServicioDeProductos (Gestión de datos)
// =============================================
const ServicioDeProductos = {
    async obtenerProductosDeAPI() {
      try {
        const respuesta = await fetch(`${urlAPI}productos.php`);
        if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);
        
        const datos = await respuesta.json();
        if (!datos.productos || !Array.isArray(datos.productos)) {
          throw new Error("Formato de datos incorrecto");
        }
        
        return datos.productos;
      } catch (error) {
        console.error("Error al obtener productos:", error);
        throw error;
      }
    },
  
    async guardarProductosEnCache(productos) {
      localStorage.setItem("productos", JSON.stringify(productos));
    },
  
    obtenerProductosDeCache() {
      try {
        const productosEnCache = localStorage.getItem("productos");
        return productosEnCache ? JSON.parse(productosEnCache) : [];
      } catch (error) {
        console.error("Error al leer caché:", error);
        return [];
      }
    }
  };
  
  // =============================================
  // MÓDULO: RenderizadorDeProductos (Interfaz de usuario)
  // =============================================
  const RenderizadorDeProductos = {
    crearTarjetaProducto(producto) {
      return `
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow">
            <img src="${producto.imagen}" class="card-img-top" alt="${producto.nombre}">
            <div class="card-body">
              ${this.crearDetallesProducto(producto)}
              ${this.crearControlesProducto()}
            </div>
          </div>
        </div>
      `;
    },
  
    crearDetallesProducto(producto) {
      return `
        <h5 class="card-title">${producto.nombre}</h5>
        <p class="card-text">${producto.descripcion}</p>
        <p class="fw-bold">${CalculadorDePrecios.obtenerPrecio(producto)} €</p>
      `;
    },
  
    crearControlesProducto() {
      return `
        <div class="d-flex justify-content-between align-items-center mt-3">
          ${this.crearControlCantidad()}
          <button class="btn btn-danger rounded-pill ms-3">Agregar al carrito</button>
        </div>
      `;
    },
  
    crearControlCantidad() {
      return `
        <div class="d-flex align-items-center border rounded-pill overflow-hidden" style="width: 120px;">
          <button class="btn btn-sm px-2 py-1 text-white border-0" onclick="ControladorDeCantidad.disminuir(this)">−</button>
          <input type="text" class="form-control text-center border-0" value="1" style="width: 40px;" readonly>
          <button class="btn btn-sm px-2 py-1 text-white border-0" onclick="ControladorDeCantidad.aumentar(this)">+</button>
        </div>
      `;
    },
  
    mostrarListaProductos(productos, idContenedor) {
      const contenedor = document.getElementById(idContenedor);
      if (!contenedor) return;
  
      contenedor.innerHTML = `
        <div class="row">
          ${productos.map(producto => this.crearTarjetaProducto(producto)).join('')}
        </div>
      `;
    }
  };
  
  // =============================================
  // MÓDULO: CalculadorDePrecios
  // =============================================
  const CalculadorDePrecios = {
    obtenerPrecio(producto) {
      const precios = {
        unitario: producto.precio_unitario,
        mediano: producto.precio_mediano,
        grande: producto.precio_grande
      };
      return precios[producto.tipo_precio] || 0;
    }
  };
  
  // =============================================
  // MÓDULO: ControladorDeCantidad
  // =============================================
  const ControladorDeCantidad = {
    cambiarCantidad(boton, cambio) {
      const input = boton.parentElement.querySelector('input');
      let cantidad = parseInt(input.value) + cambio;
      input.value = Math.max(1, cantidad);
    },
    
    aumentar(boton) { this.cambiarCantidad(boton, 1); },
    disminuir(boton) { this.cambiarCantidad(boton, -1); }
  };
  
  // =============================================
  // MÓDULO: GestorDeSecciones
  // =============================================
  const GestorDeSecciones = {
    secciones: [
      'vista-destacados',
      'cont_Bocaditos_dulces', 'cont_Bocaditos_salados',
      'cont_postres', 'cont_tartas',
      'cont_Personales_dulces', 'cont_Personales_salados',
      'vista-nosotros', 'vista-contacto'
    ],
  
    mostrarSeccion(idSeccion) {
      this.secciones.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
          elemento.style.display = (id === idSeccion) ? "block" : "none";
        }
      });
      
      this.mostrarOcultarDestacados(idSeccion === 'vista-destacados');
      this.desplazarASeccion(idSeccion);
    },
  
    mostrarOcultarDestacados(mostrar) {
      const destacados = document.getElementById("productos-destacados");
      if (destacados) destacados.style.display = mostrar ? "flex" : "none";
    },
  
    desplazarASeccion(idSeccion) {
      const seccion = document.getElementById(idSeccion);
      if (seccion) seccion.scrollIntoView({ behavior: "smooth" });
    }
  };
  
  // =============================================
  // MÓDULO: ControladorPrincipal (Orquestador)
  // =============================================
  const ControladorPrincipal = {
    async iniciar() {
      try {
        let productos = ServicioDeProductos.obtenerProductosDeCache();
        
        if (productos.length === 0) {
          productos = await ServicioDeProductos.obtenerProductosDeAPI();
          await ServicioDeProductos.guardarProductosEnCache(productos);
        }
  
        this.mostrarProductosDestacados(productos);
        this.mostrarProductosEnMenusDesplegables(productos);
        
      } catch (error) {
        console.error("Error en inicialización:", error);
        // Aquí podrías mostrar un mensaje al usuario
      }
    },
  
    mostrarProductosDestacados(productos) {
      const destacados = productos.filter(p => p.destacado);
      RenderizadorDeProductos.mostrarListaProductos(destacados, "productos-destacados");
    },
  
    mostrarProductosEnMenusDesplegables(productos) {
      const categorias = {
        'BOCADITOS': ['dulces', 'salados'],
        'POSTRES': ['tartas', 'postres'],
        'PERSONALES': ['dulces', 'salados']
      };
  
      Object.entries(categorias).forEach(([categoriaPrincipal, subcategorias]) => {
        subcategorias.forEach(subcategoria => {
          const productosFiltrados = productos.filter(p => 
            p.categoria_principal === categoriaPrincipal && 
            p.subcategoria === subcategoria
          );
          const idContenedor = `cont_${categoriaPrincipal}_${subcategoria}`.toLowerCase();
          RenderizadorDeProductos.mostrarListaProductos(productosFiltrados, idContenedor);
        });
      });
    }
  };
  
  // Inicialización al cargar la página
  document.addEventListener("DOMContentLoaded", () => ControladorPrincipal.iniciar());