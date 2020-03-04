<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CXP;
use DB;
use org\majkel\dbase\Table;
use File;
use Session;

class CXPController extends Controller
{
  public function index()
  {
  return view('cxp');
  }



  public function cxp(Request $request){
  $files= $request->filess;
  $ruta=str_replace("\"", '/', $request->url);
  $rut=$ruta."/";
  foreach ($files as $fil) {
    $archivo=$fil;
    $band=$this->comprobar_factura($archivo);
    if($band==true){
    $comprobante = \CfdiUtils\Cfdi::newFromString(file_get_contents($rut.$fil))
        ->getQuickReader();
        if ($comprobante['tipodecomprobante'] != 'N') {
          $foliofactura=$comprobante['folio'];
          $tratarfecha=str_replace('-', '', $comprobante['fecha']);
          $fechanueva=substr($tratarfecha, 0,8);
          $dia=substr($fechanueva, 6,2);
          $mes=substr($fechanueva, 4,2);
          $anio=substr($fechanueva, 0,4);
          $fechaarmada=$anio.$mes.$dia;
          $tipo='1';
        $rfc_receptor=$comprobante->receptor['rfc'];
        $nombre_receptor=$comprobante->receptor['nombre'];
        $this->registrar_proveedor($rfc_emisor,$nombre_emisor);
        $proveedor= $this->consultar_proveedor($rfc_emisor);
        //////////FIN COMPROBACION O REGISTRO DE EMISORES(PROVEEDORES)//////
        // usando asignación de variable
        $conceptos = $comprobante->conceptos;
         foreach($conceptos() as $concepto) {
          // usando propiedad

        foreach(($concepto->impuestos->traslados)() as $traslado) {
            $impuestos1=$traslado['importe'];
            if(is_null($impuestos1)){
              $impuestos1='0.00';
            }
              foreach(($concepto->impuestos->retenciones)() as $retencion) {
                  $imp1=$retencion['importe'];
                  $reten[]=$imp1;
          }

if(isset($reten)){
  $retencion1=$reten[0];
  $retencion2=$reten[1];
  unset($reten[0],$reten[1]);
}
else{
  $retencion1='0.00';
  $retencion2='0.00';
}
        $importe=$concepto['importe'];
        $descripcion=$concepto['descripcion'];
        ///$this->registrar_factura($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion);
      $this->registrar_facturasql($foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $descripcion, $archivo, $retencion1, $retencion2);
    }
        }
  }
  /////////////FIN FOR QUE RECORRE TODOS LOS ARCHIVOS
    }
  return redirect()->action('PrincipalController@ver_regisrto');
  }
}

////////COMPROBAR FACTURA///////////////
public function comprobar_factura($archivo){
  $factura= DB::table('c_x_ps')->where('nombre_factura', $archivo)->first();
  $bandera=false;
  if(is_null($factura)){
    $bandera=true;
  }
  return $bandera;
}


public function registrar_facturasql($foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $descripcion, $archivo, $retencion1, $retencion2)
{
$clave= Session::get('clave');
$empresa= new CXP;
$empresa->nombre_factura=$archivo;
$empresa->referencia=$foliofactura;
$empresa->folio=$foliofactura;
$empresa->fecha=$fechaarmada;
$empresa->tipo=$tipo;
$empresa->clave_clie=$proveedor;
$empresa->importe=$importe;
$empresa->iva=$impuestos1;
$empresa->fechacobro=$fechaarmada;
$empresa->concepto=$concepto_sat;
$empresa->descripcion=$descripcion;
$empresa->impuesto='0.00';
$empresa->empresa=$clave;
$empresa->save();
}
/////////FIN REGISTRAR FACTURA
public function registrar_proveedor($rfc, $nombre){
  $cont1=0;
    $clave=Session::get('clave');
  $db = dbase_open('Z:/Cuentas por cobrar/'.$clave.'/archivos/pruebas.dbf', 2);
  if ($db) {
    $numero_registros = dbase_numrecords($db);
  ////  echo $numero_registros;
  ////  echo "<br>";
    if ($numero_registros== 0) {
      $cont=$numero_registros+1;
      $clv=$aux = str_pad($cont, 4, "0", STR_PAD_LEFT);
      dbase_add_record($db, array($clv,$nombre, $rfc));
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
        if ($cont1 == 0) {
          $num_registro=$numero_registros+1;
          dbase_add_record($db, array($num_registro,$nombre, $rfc));
          echo "Registrado Proveedor";
        }

    }


    dbase_close($db);
  }
}


public function consultar_proveedor($rfc){
    $clave=Session::get('clave');
  $db = dbase_open('C:/PUBLIC/contadores1/Cuentas por cobrar/'.$clave.'/archivos/pruebas.dbf', 0);

if ($db) {
  $número_registros = dbase_numrecords($db);
  for ($i = 1; $i <= $número_registros; $i++) {
      $fila = dbase_get_record_with_names($db, $i);
      $comparacion=str_replace(' ', '', $fila['RFC']);
      $bandera=strcmp($comparacion, $rfc);
       if($bandera == 0)
         {
        $clave=$fila['CLAVE'];
         }
  }
  dbase_close($db);
}
return $clave;
}

}
