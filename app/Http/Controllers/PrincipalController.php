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
    //////////COMPROBACION O REGISTRO DE EMISORES(PROVEEDORES)//////
    $rfc_emisor=$comprobante->emisor['rfc'];
    $nombre_emisor=$comprobante->emisor['nombre'];
    $this->registrar_proveedor($rfc_emisor,$nombre_emisor);
    //////////FIN COMPROBACION O REGISTRO DE EMISORES(PROVEEDORES)//////
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
/////////////FIN FOR QUE RECORRE TODOS LOS ARCHIVOS
}

public function registrar_proveedor($rfc, $nombre){
  $cont1=0;
  $db = dbase_open('C:/Users/Incretec Desarrollo/Desktop/DBF/pruebas.dbf', 2);
  if ($db) {
    $numero_registros = dbase_numrecords($db);
  ////  echo $numero_registros;
  ////  echo "<br>";
    if ($numero_registros== 0) {
      $cont=$numero_registros+1;
      dbase_add_record($db, array($cont,$nombre, $rfc));
    ////  echo "Primer registro";
      echo "<br>";
    }
    else {

        for ($i = 1; $i <= $numero_registros; $i++) {

              $fila=dbase_get_record_with_names($db, $i);
            $comparacion=str_replace(' ', '', $fila['RFC']);
            $bandera=strcmp($comparacion, $rfc);
             if($bandera == 0)
               {
                $cont1=$cont1+1;
            ////    echo "Registro repetido";
               }
        }
        echo $cont1;
        if ($cont1 == 0) {
          $num_registro=$numero_registros+1;
          dbase_add_record($db, array($num_registro,$nombre, $rfc));
          echo "Registrado Proveedor";
        }

    }


    dbase_close($db);
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
