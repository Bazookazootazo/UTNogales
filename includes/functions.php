function crearTarjeta($titulo, $descripcion, $precio) {
    echo "
    <div class='card w-96 bg-base-100 shadow-xl'>
      <div class='card-body'>
        <h2 class='card-title'>$titulo</h2>
        <p>$descripcion</p>
        <div class='card-actions justify-end'>
          <button class='btn btn-primary'>Comprar $precio</button>
        </div>
      </div>
    </div>";
}