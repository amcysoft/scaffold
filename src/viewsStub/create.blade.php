@extends('layouts.app')

@section('content')
  <div class="container">
  	<h1>Create {{capSingle}}</h1>
  	<hr>
  	<ol class="breadcrumb">
  		<li><a href="{{ url('/home') }}">Home</a></li>
  		<li><a href="{{ url('{{smallPlural}}') }}">{{capPlural}}</a></li>
  		<li>Create</li>
  	</ol>
  	@include('flash::message')
    {!! Form::open(['url' => '{{smallPlural}}']) !!}
      @include('{{smallPlural}}.form', ['submitText' => '<i class="fa fa-plus"></i> Create'])
    {!! Form::close() !!}

    <!-- ================================================== -->
  </div>
@stop