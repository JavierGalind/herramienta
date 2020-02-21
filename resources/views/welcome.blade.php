@extends('Layouts.layout')

@section('content')
  <form id="form1" action="{{ url('cxc') }}" method="GET" enctype="multipart/form-data">
      <div class="form-group">
          {!!csrf_field()!!}
          <input name="filess[]" type="file" multiple accept=".xml" required="" >
          <small id="fileHelp" class="form-text text-muted">Solo Archivos .xml</small>

      </div>
      <button type="submit" class="btn btn-success col-md-6"><i class="fa fa-upload"></i> Cargar</button>
  </form>
  </div>

@endsection
