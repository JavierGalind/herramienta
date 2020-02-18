@extends('Layouts.layout')

@section('content')
  <form action="{{route('cxc')}}" method="get">
    <div class="form-group">
      <label for="exampleInputEmail1">Ingresa la direccion de la carpeta:</label>
      <input type="text" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp" name="carpeta">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>

@endsection
