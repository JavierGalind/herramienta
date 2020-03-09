@extends('Layouts.layout')

@section('content')
    <h1 style="color:blue;">CFDI CXC DE LA EMPRESA: {{Session::get('empresa')}}</h1> <a class="btn btn-primary" href="{{route('empresa')}}" role="button">CAMBIAR EMPRESA</a>
  <form class="" action="{{route('cargardbf')}}" method="get">
    <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">INDICADOR</th>
      <th scope="col">REFERENCIA</th>
      <th scope="col">FOLIO</th>
      <th scope="col">TIPO DE COMPROBANTE</th>
      <th scope="col">FECHA</th>
      <th scope="col">IMPORTE</th>
      <th scope="col">IVA</th>
      <th scope="col">DESCUENTO</th>
      <th scope="col">FECHA DE COBRO</th>
      <th scope="col">CLAVE DEL CLIENTE</th>
      <th scope="col">NOMBRE DEL CLIENTE</th>
      <th scope="col">TIPO</th>
      <th scope="col">CONCEPTO</th>
      <th scope="col">DESCRIPCION</th>
      <th scope="col">IMPUESTO</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($ventas as $ven)
      <tr>
        <td> <input type="checkbox" name="indic[]" class="sub_chk" value="{{$ven->id}}"></td>
        <td>{{$ven->referencia}}</td>
        <td>{{$ven->folio}}</td>
        <td>{{$ven->tipo_comprobante}}</td>
        <td>{{$ven->fecha}}</td>
        <td>{{$ven->importe}}</td>
        <td>{{$ven->iva}}</td>
        <td>{{$ven->descuento}}</td>
        <td>{{$ven->fechacobro}}</td>
          <td>{{$ven->clave_clie}}</td>
          <td>{{$ven->nombre_cliente}}</td>
        <td>{{$ven->tipo}}</td>
        <td>{{$ven->concepto}}</td>
        <td>{{$ven->descripcion}}</td>
        <td>{{$ven->impuesto}}</td>
      </tr>
    @endforeach
  </tbody>
</table>
  <button class="btn btn-primary" type="submit">Button</button>
  </form>


@endsection
