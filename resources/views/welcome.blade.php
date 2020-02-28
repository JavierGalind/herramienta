@extends('Layouts.layout1')

@section('content')
  <form class="" action="{{route('sesion')}}" method="get">
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="inputGroupSelect01">SELECCIONA UNA EMPRESA</label>
      </div>
      <select class="custom-select" id="inputGroupSelect01" name=clave>
        @foreach ($dbf as $db)
   <option value="{{$db->CLAVE_CLIE}}">{{$db->NOMBRE}}</option>
 @endforeach
      </select>
    </div>
    <button class="btn btn-primary" type="submit">Seleccionar</button>
  </form>
@endsection
