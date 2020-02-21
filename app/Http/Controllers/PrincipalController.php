<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
class PrincipalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
public function cxc(Request $request){
$files= $request->filess;
foreach ($files as $fil) {
  $comprobante = \CfdiUtils\Cfdi::newFromString(file_get_contents('C:/Users/Incretec Desarrollo/Desktop/CXC/'.$fil))
      ->getQuickReader();


    echo "<br>";
        echo $comprobante['total']; // (string) "123.45"
      echo "<br>";
      echo $comprobante['fecha']; // (string) "123.45"
    echo "<br>";
    echo $comprobante->impuestos['totalImpuestosTrasladados']; // (string) "123.45"
    echo "<br>";
    echo $comprobante->emisor['nombre'];
    echo "<br>";
    echo $comprobante->emisor['rfc'];
    echo "<br>";
    echo $comprobante->receptor['nombre'];
    echo "<br>";
    echo $comprobante->receptor['rfc'];
    echo "<br>";
    echo $comprobante->complemento->timbreFiscalDigital['UUID']; // 2017-03-21T08:18:08
    echo "<br>";

    // usando asignación de variable
$conceptos = $comprobante->conceptos;
foreach($conceptos() as $concepto) {
    // usando propiedad

    foreach(($concepto->impuestos->traslados)() as $traslado) {
        echo $traslado['impuesto'];
        echo "<br>";
        echo $traslado['importe'];
        echo "<br>";
    }
    echo $concepto['descripcion'];
    echo "<br>";
}

}
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
