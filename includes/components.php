<?php
// Función para generar tarjetas de estadísticas (UI moderna con sombras y auto-layout)
function renderStatCard($titulo, $valor, $descripcion, $tipo_color = "text-primary") {
    echo "
    <div class='stats shadow'>
        <div class='stat'>
            <div class='stat-title'>$titulo</div>
            <div class='stat-value $tipo_color'>$valor</div>
            <div class='stat-desc'>$descripcion</div>
        </div>
    </div>
    ";
}

// Función para generar una tabla moderna y responsiva
function renderTable($headers, $rows) {
    echo "<div class='overflow-x-auto bg-base-100 rounded-box shadow'>";
    echo "<table class='table table-zebra w-full'>";
    
    // Encabezados
    echo "<thead><tr>";
    foreach ($headers as $header) {
        echo "<th>$header</th>";
    }
    echo "</tr></thead>";
    
    // Filas de datos
    echo "<tbody>";
    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>$cell</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    
    echo "</table>";
    echo "</div>";
}
?>