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

  public function ver_regisrto(){
      $clave=Session::get('clave');
     $ventas = DB::table('c_x_ps')->where('empresa', $clave)->get();
        $dbf = Table::fromFile('Z:/Cuentas por Pagar/'.$clave.'/archivos/concepto.dbf');
  return view('verregistrocxp', compact('ventas','dbf'));
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
        if ($comprobante['tipodecomprobante'] != 'N' && $comprobante['tipodecomprobante'] != 'P') {
          $foliofactura=$comprobante['folio'];
          $tipo_comprobante=$comprobante['tipodecomprobante'];
          $descuento=$comprobante['descuento'];
          if(is_null($descuento)){
            $descuento='0.00';
          }
          $subtotal=intval($comprobante['subtotal'])-intval($comprobante['descuento']);
          if(strlen($foliofactura)<1)
          {
            $folio=$comprobante->complemento->timbreFiscalDigital['UUID'];
            $tratarfolio=str_replace('-', '', $folio);
              $foliofactura=substr($tratarfolio, 26,6);
          }
          $tratarfecha=str_replace('-', '', $comprobante['fecha']);
          $fechanueva=substr($tratarfecha, 0,8);
          $dia=substr($fechanueva, 6,2);
          $mes=substr($fechanueva, 4,2);
          $anio=substr($fechanueva, 0,4);
          $fechaarmada=$anio.$mes.$dia;
          $tipo='1';
        $rfc_emisor=$comprobante->emisor['rfc'];
        $nombre_emisor=$comprobante->emisor['nombre'];
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
                  echo $imp1;
                  echo "<br>";
                  $reten[]=$imp1;
          }

if(isset($reten)){
  $retencion1=$reten[0];
  $retencion2=$reten[1];

}
else{
  $retencion1='0.00';
  $retencion2='0.00';
}
        $importe=$concepto['importe'];
        $descripcion=$concepto['descripcion'];
        ///$this->registrar_factura($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion);
      $this->registrar_facturasql($foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $descripcion, $archivo, $retencion1, $retencion2, $tipo_comprobante,$nombre_emisor, $descuento, $subtotal);
    }
        }
  }
  /////////////FIN FOR QUE RECORRE TODOS LOS ARCHIVOS
    }
  }
  return redirect()->action('CXPController@ver_regisrto');
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


public function registrar_facturasql($foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $descripcion, $archivo, $retencion1, $retencion2, $tipo_comprobante,$nombre_emisor, $descuento, $subtotal)
{
$clave= Session::get('clave');
$empresa= new CXP;
$empresa->nombre_factura=$archivo;
$empresa->tipo_comprobante=$tipo_comprobante;
$empresa->nombre_cliente=$nombre_emisor;
$empresa->descuento=intval($descuento);
$empresa->subtotal=intval($subtotal);
$empresa->referencia=$foliofactura;
$empresa->folio=$foliofactura;
$empresa->pedido=$foliofactura;
$empresa->fecha=$fechaarmada;
$empresa->tipo=$tipo;
$empresa->clave_clie=$proveedor;
$empresa->importe=$importe;
$empresa->iva=$impuestos1;
$empresa->fecha_cobro=$fechaarmada;
$empresa->fecha_doc=$fechaarmada;
$empresa->concepto=$descripcion;
$empresa->cl_con="";
$empresa->clv_con="";
$empresa->impuesto=$retencion1;
$empresa->impuesto2=$retencion2;
$empresa->empresa=$clave;
$empresa->save();
}
/////////FIN REGISTRAR FACTURA
public function registrar_proveedor($rfc, $nombre){
  $cont1=0;
    $clave=Session::get('clave');
  $db = dbase_open('Z:/Cuentas por Pagar/'.$clave.'/archivos/clientes.dbf', 2);
  if ($db) {
    $numero_registros = dbase_numrecords($db);
  ////  echo $numero_registros;
  ////  echo "<br>";
    if ($numero_registros== 0) {
      $cont=$numero_registros+1;
      $clv=$aux = str_pad($cont, 4, "0", STR_PAD_LEFT);
      dbase_add_record($db, array($clv,'1',"","",$nombre,"",$rfc,"","","","","","","","","","","","","","",'0',"",'0.00','0.00','0.00',"",'0.00'
      ,"","","","",'F',"","","","","","","","","","","","","","","","","","","","","","","","","","",""
    ,"","",'F','F'));
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
          $clv1=str_pad($num_registro, 4, "0", STR_PAD_LEFT);
          dbase_add_record($db, array($clv1,'1',"","",$nombre,"",$rfc,"","","","","","","","","","","","","","",'0',"",'0.00','0.00','0.00',"",'0.00'
          ,"","","","",'F',"","","","","","","","","","","","","","","","","","","","","","","","","","",""
        ,"","",'F','F'));
          echo "Registrado Proveedor";
        }

    }


    dbase_close($db);
  }
}


public function consultar_proveedor($rfc){
    $clave=Session::get('clave');
  $db = dbase_open('Z:/Cuentas por Pagar/'.$clave.'/archivos/clientes.dbf', 0);

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






///////////REGISTRAR FACTURA
public function cargardbfcxp(Request $request){
  $clave=Session::get('clave');
  $valores=$request->indic;
  $concept=$request->sel;
  for ($i=0; $i < count($valores) ; $i++) {
    $val = DB::table('c_x_ps')->where('id', $valores[$i])->first();
    $iden=$val->id;
    $referencia= $val->referencia;
    $fecha=$val->fecha;
    $importe=$val->importe;
    $iva=$val->iva;
    $clave_clie=$val->clave_clie;
    $tipo=$val->tipo;
    $concepto=$val->concepto;
    $impuesto=$val->impuesto;
    $impuesto2=$val->impuesto2;
    $clv_con=$concept[$iden];
    $cl_con=$concept[$iden];
    $id=$val->id;
  $this->guardarenDBFcxp($referencia, $importe, $iva, $fecha, $clave_clie, $tipo, $concepto, $impuesto,$impuesto2, $clv_con, $cl_con);
  $this->eliminarsql($id);
  }
  return redirect()->action('CXPController@ver_regisrto');
}

public function eliminarsql($id){
      $venta = CXP::whereid($id)->firstOrFail();
      $venta->delete();
}

public function guardarenDBFcxp($referencia, $importe, $iva, $fecha, $clave_clie, $tipo, $concepto, $impuesto,$impuesto2, $clv_con, $cl_con){
  $clave=Session::get('clave');
 $db = dbase_open('Z:/Cuentas por Pagar/'.$clave.'/archivos/ventas.dbf', 2);
 if ($db) {
      dbase_add_record($db, array($referencia,$referencia,$referencia,$fecha,$importe,$iva,'F','0.00','F',$fecha,'F',$clave_clie,$tipo,"","",$concepto,"",$impuesto,$impuesto2,"","",'F','F',"","",'F',"",'F'
      ,"","","","",$clv_con,$cl_con,"",'F',"","",'F',""));
 dbase_close($db);
 }
}


}
