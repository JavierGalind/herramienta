@extends('Layouts.layout')

@section('content')
  <h1>CFDI</h1>
  <table class="table">
    <thead class="thead-dark">
      <tr>
        <th scope="col">REFERENCIA</th>
        <th scope="col">FOLIO</th>
        <th scope="col">FECHA</th>
        <th scope="col">IMPORTE</th>
        <th scope="col">IVA</th>
        <th scope="col">FECHA DE COBRO</th>
        <th scope="col">CLAVE DEL CLIENTE</th>
        <th scope="col">TIPO</th>
        <th scope="col">CONCEPTO</th>
        <th scope="col">DESCRIPCION</th>
        <th scope="col">IMPUESTO</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($ventas as $ven)
        <tr>
          <th scope="row">{{$ven->referencia}}</th>
          <td>{{$ven->folio}}</td>
          <td>{{$ven->fecha}}</td>
          <td>{{$ven->importe}}</td>
          <td>{{$ven->iva}}</td>
          <td>{{$ven->fechacobro}}</td>
            <td>{{$ven->clave_clie}}</td>
          <td>{{$ven->tipo}}</td>
          <td>{{$ven->concepto}}</td>
          <td>{{$ven->descripcion}}</td>
          <td>{{$ven->impuesto}}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
