<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use org\majkel\dbase\Table;
use File;
use Session;
use App\Venta;
use App\Factura;
use DB;
class PrincipalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     //////////PAGINA INICIAL DE CXC///////////
    public function index()
    {
    $valor = Session::get('clave');
    return view('cxc');
    }
    //////////////////////////////////////
    /////OBTENER LA CLAVE DE LA EMPRESA
public function sesion(Request $request){
  Session::put('clave', $request->clave);
  $db = dbase_open('Z:/Cuentas por cobrar/empresa1.dbf', 0);
 $clv= Session::get('clave');
if ($db) {
  $número_registros = dbase_numrecords($db);
  for ($i = 1; $i <= $número_registros; $i++) {
      $fila = dbase_get_record_with_names($db, $i);
      $comparacion=str_replace(' ', '', $fila['CLAVE_CLIE']);
      $bandera=strcmp($comparacion, $clv);
       if($bandera == 0)
         {
        $emp=$fila['NOMBRE'];
        Session::put('empresa', $emp);
         }
  }
  dbase_close($db);
}
return view('welcome1');
}
//////////////////LISTAR LAS EMPRESAS//////////////////////////
public function empresa(Request $request){
  $dbf = Table::fromFile('Z:/Cuentas por cobrar/empresa.dbf');
return view('welcome',compact('dbf'));
}
//////////////////////////////////////
////////////////VER LOS REGISTROS DE VENTAS REGISTRADAS/////////////////
public function ver_regisrto(){
    $clave=Session::get('clave');
   $ventas = DB::table('ventas')->where('empresa', $clave)->get();
return view('verregistro', compact('ventas'));
}
/////////////////////////////
//////////////////LECTURA DE XMLS MASIVO////////////////////////////
public function cxc(Request $request){
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
        $totalfactura= $comprobante['total'];
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
      $this->registrar_proveedor($rfc_receptor,$nombre_receptor);
      $proveedor= $this->consultar_proveedor($rfc_receptor);
      //////////FIN COMPROBACION O REGISTRO DE EMISORES(PROVEEDORES)//////
      // usando asignación de variable
      $conceptos = $comprobante->conceptos;
       foreach($conceptos() as $concepto) {
        // usando propiedad

      foreach(($concepto->impuestos->traslados)() as $traslado) {
          $impuestos1=$traslado['importe'];
          if(is_null($impuestos1)){
            $impuestos1=0.00;
          }
      }
      $importe=$concepto['importe'];
      $concepto_sat=$concepto['claveprodserv'];
      $descripcion=$concepto['descripcion'];

      ///$this->registrar_factura($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion);
      $this->registrar_facturasql($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion, $archivo, $tipo_comprobante,$nombre_receptor, $descuento, $subtotal);
  }
      }

}
/////////////FIN FOR QUE RECORRE TODOS LOS ARCHIVOS
  }
return redirect()->action('PrincipalController@ver_regisrto');
}
////////COMPROBAR FACTURA///////////////
public function comprobar_factura($archivo){
  $factura= DB::table('ventas')->where('nombre_factura', $archivo)->first();
  $bandera=false;
  if(is_null($factura)){
    $bandera=true;
  }
  return $bandera;
}
///////////REGISTRAR FACTURA
public function cargardbf(Request $request){
  $clave=Session::get('clave');
  $valores=$request->indic;
  for ($i=0; $i < count($valores) ; $i++) {
    $val = DB::table('ventas')->where('id', $valores[$i])->first();
    $referencia= $val->referencia;
    $fecha=$val->fecha;
    $importe=$val->importe;
    $iva=$val->iva;
    $clave_clie=$val->clave_clie;
    $tipo=$val->tipo;
    $concepto=$val->concepto;
    $descripcion=$val->descripcion;
    $id=$val->id;
  $this->guardarenDBF($referencia, $importe, $iva, $fecha, $clave_clie, $tipo, $concepto, $descripcion);
  $this->eliminarsql($id);
  }
  return redirect()->action('PrincipalController@ver_regisrto');
}

public function eliminarsql($id){
      $venta = Venta::whereid($id)->firstOrFail();
      $venta->delete();
}

public function guardarenDBF($referencia, $importe, $iva, $fecha, $clave_clie, $tipo, $concepto, $descripcion){
  $clave=Session::get('clave');
 $db = dbase_open('Z:/Cuentas por cobrar/'.$clave.'/archivos/ventas.dbf', 2);
 if ($db) {
      dbase_add_record($db, array($referencia,$referencia,$fecha,$importe,$iva,'F','0.00','F',$fecha,'F',$clave_clie,$tipo,"","",$descripcion,"",'0.00',"",'F','F',"",""
      ,'F',"",'F',"","","","","",""));
 dbase_close($db);
 }
}



public function registrar_facturasql($totalfactura, $foliofactura, $fechaarmada, $tipo, $proveedor, $impuestos1, $importe, $concepto_sat, $descripcion, $archivo, $tipo_comprobante,$nombre_emisor, $descuento, $subtotal)
{
$clave= Session::get('clave');
$empresa= new Venta;
$empresa->nombre_factura=$archivo;
$empresa->tipo_comprobante=$tipo_comprobante;
$empresa->nombre_cliente=$nombre_emisor;
$empresa->descuento=intval($descuento);
$empresa->subtotal=$subtotal;
$empresa->referencia=$foliofactura;
$empresa->folio=$foliofactura;
$empresa->fecha=$fechaarmada;
$empresa->tipo=$tipo;
$empresa->clave_clie=$proveedor;
$empresa->importe=$importe;
$empresa->subtotal=$subtotal;
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
  $db = dbase_open('Z:/Cuentas por cobrar/'.$clave.'/archivos/clientes.dbf', 2);
  if ($db) {
    $numero_registros = dbase_numrecords($db);
  ////  echo $numero_registros;
  ////  echo "<br>";
    if ($numero_registros== 0) {
      $cont=$numero_registros+1;
      $clv=$aux = str_pad($cont, 4, "0", STR_PAD_LEFT);
      dbase_add_record($db, array($clv,$nombre,'1',"","",$rfc,"","","","","","","","","","","","",""
      ,'0',"",'0.00','0.00','0.00','0.00',"",'0.00',"","","","",'F',"","","","","","","","","","",""
      ,"","","","","","","","",'F','F'));
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
          dbase_add_record($db, array($clv1,$nombre,'1',"","",$rfc,"","","","","","","","","","","","",""
          ,'0',"",'0.00','0.00','0.00','0.00',"",'0.00',"","","","",'F',"","","","","","","","","","",""
          ,"","","","","","","","",'F','F'));
          echo "Registrado Proveedor";
        }

    }


    dbase_close($db);
  }
}


public function consultar_proveedor($rfc){
    $clave=Session::get('clave');
  $db = dbase_open('Z:/Cuentas por cobrar/'.$clave.'/archivos/clientes.dbf', 0);

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

public function deleteAll(Request $request)
 {
     $ids = $request->ids;
     //DB::table("ventas")->whereIn('id',explode(",",$ids))->delete();
  return $ids;
 }

public function guardarDBF($ids){
  echo "entramos aqui";
  dd($ids);
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
