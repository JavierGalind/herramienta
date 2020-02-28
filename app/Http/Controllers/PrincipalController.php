<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use org\majkel\dbase\Table;
use File;
use Session;
use App\Venta;
use DB;
class PrincipalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $valor = Session::get('clave');
    return view('cxc');
    }
public function sesion(Request $request){
  Session::put('clave', $request->clave);
return view('welcome1');
}
public function empresa(Request $request){
  $dbf = Table::fromFile('Z:/Cuentas por cobrar/empresa1.dbf');
return view('welcome',compact('dbf'));
}

public function ver_regisrto(){
    $clave=Session::get('clave');
   $ventas = DB::table('ventas')->where('empresa', $clave)->get();
return view('verregistro', compact('ventas'));
}


public function cxc(Request $request){
$files= $request->filess;
$ruta=str_replace("\"", '/', $request->url);
$rut=$ruta."/";
foreach ($files as $fil) {
  $comprobante = \CfdiUtils\Cfdi::newFromString(file_get_contents($rut.$fil))
      ->getQuickReader();
      $totalfactura= $comprobante['total'];
      $foliofactura=$comprobante['folio'];
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
    }
    $importe=$concepto['importe'];
    $concepto_sat=$concepto['claveprodserv'];
    $descripcion=$concepto['descripcion'];

    $this->registrar_factura($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion);
    $this->registrar_facturasql($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion);
}


}
/////////////FIN FOR QUE RECORRE TODOS LOS ARCHIVOS
return redirect()->action('PrincipalController@ver_regisrto');
}
///////////REGISTRAR FACTURA
public function registrar_factura($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion){
  $clave=Session::get('clave');
  $db = dbase_open('Z:/Cuentas por cobrar/'.$clave.'/archivos/ventas2.dbf', 2);
  if ($db) {
       dbase_add_record($db, array($foliofactura,$foliofactura,$fechaarmada,$importe,$impuestos1,'F','0.00',$fechaarmada,'F',$proveedor,$tipo,$concepto_sat,$descripcion,'0.00'));
  dbase_close($db);
  }
}
public function registrar_facturasql($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion)
{
$clave= Session::get('clave');
$empresa= new Venta;
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