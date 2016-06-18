@extends('layouts.app')

@section('content')
  <div class="container">
  	<h1>Edit {{capSingle}}</h1>
  	<hr>
  	<ol class="breadcrumb">
  		<li><a href="{{ url('/home') }}">Home</a></li>
  		<li><a href="{{ url('{{smallPlural}}') }}">{{capPlural}}</a></li>
  		<li>Edit</li>
  	</ol>
  	@include('flash::message')
    {!! Form::model(${{smallSingle}}, ['method' => 'PATCH', 'action' => ['{{capPlural}}Controller@update', ${{smallSingle}}->id]]) !!}
      @include('{{smallPlural}}.form', ['submitText' => '<i class="fa fa-check"></i> Update'])
    {!! Form::close() !!}

    <!-- ================================================== -->
  </div>
@stop