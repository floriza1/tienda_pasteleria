//Carrito
let productos = obtenerProductosDesdeCache(); // localStorage
if (productos.length === 0) productos = await obtenerProductosDesdeAPI(); // si no hay en local storage lo lo pide a la API

function obtenerProductosDesdeCache(){
    //Recupera el array de productos desde localStorage o devuelve un array vacio
    return JSON.parse(localStorage.getItem("carrito")) || [];
}

function guardarProductosEnCache(productos){
    //Guarda el array de productos como string json
    localStorage.setItem("carrito", JSON.stringify(productos));
}
  
function agregarAlCarrito(productoId, boton){
    //Obtiene el array actual del carrito
    let productos = obtenerProductosDesdeCache();
    let producto = productos.find(produ => produ.id_producto == productoId);

    if(producto){
        //Si Ya existe , incrementa la cantidad
    }else{
        //si no existe, crea nuevo productos usando datos desde el botón
        // esto debe ser para agregar un producto que no esta en el carrito y debe ser
        // general para cualquier productos y dices que lo agrega al array productos.push(producto) eso en principio estará vacio o con algún producto que no lo sabemos
    }

}

 const 
document.addEventListener('click', ()=>{ 
    event.preventDefault();
    if(!id_cliente  ){
        const response = await fetch(apiUrl + 'register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password }),

        })
          
        }

        enviar a  registrarse o login html ('registerYlogin.html')
    } else{
        y si si el usuario ya existe llamar a  agregarAlCarrito
    } 

 })
//constantes de formulario de pago y ids de carrtio
const contCarrito = document.getElementById("cont-carrito");
const listaCarrito = document.getElementById('lista-carrito');
const btnIrAPago = document.getElementById('btnIrAPago');
const contFormPago = document.getElementById('cont-form-pago')
const formPago = document.getElementById('formPago');

document.getElementById('btnIrAPago').addEventListener('click', () => {
   contFormPago.scrollIntoView({ behavior: 'smooth' });
    formPago.addEventListener("submit", async function(e) {
        e.preventDefault();
      
        const productos = obtenerProductosDesdeCache(); // tu función del carrito
        const nombrePago = document.getElementById("nombrePago").value;
        const apellidoPago = document.getElementById("apellidoPago").value;
        const emailPago = document.getElementById("emailPago").value;
        const direccionPago = document.getElementById("direccionPago").value;
        const telefonoPago = document.getElementById('telefonoPago')
        const fechaEntrega = document.getElementById("fechaEntrega").value;
        //Captura el metodo elegido del pago
        const metodoPago = document.querySelector('input[name="metodoPago"]:checked').value;

        if (productos.length === 0) {
            alert("Tu carrito está vacío.");
            return;
          }
        
          const body = {
            productos: productos,
            fecha_entrega: fechaEntrega,
            metodo_pago: metodoPago,
            telefono: telefono,
            direccion_entrega: direccion
          };
        
          const response = await fetch("php/pedidos.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
          });
        
          const resultado = await response.json();
          if (resultado.message) {
            alert("Pedido realizado con éxito");
            localStorage.removeItem("carrito"); // Vaciar carrito
            window.location.href = "gracias.html"; // o redirecciona donde quieras
          } else {
            alert("Error: " + (resultado.error || "No se pudo completar el pedido"));
          }
    
  });
  
});




