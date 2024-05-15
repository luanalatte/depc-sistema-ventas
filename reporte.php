<?php

$fecha = date("y-m-d");

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte-$fecha.csv");

include_once("config.php");
include_once("entidades/venta.php");

$aVentas = Venta::obtenerGrilla();

$fs = fopen("php://output", "w");
fputs($fs, $bom = chr(0xEF).chr(0xBB).chr(0xBF));

$aTitulos = ["Fecha", "Cliente", "Producto", "Cantidad", "Total"];
fputcsv($fs, $aTitulos, ";");

foreach ($aVentas as $venta) {
    $aFila = [
        $venta->fecha_hora,
        $venta->nombre_cliente,
        $venta->nombre_producto,
        $venta->cantidad,
        $venta->total
    ];

    fputcsv($fs, $aFila, ";");
}

fclose($fs);
