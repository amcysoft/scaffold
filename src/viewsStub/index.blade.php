@extends('layouts.app')

@section('content')
  <div class="container">
    <ol class="breadcrumb">
      <li><a href="{{ url('/home') }}">Home</a></li>
      <li><a href="{{ url('{{smallPlural}}') }}">{{capPlural}}</a></li>
    </ol>
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="visible-lg-inline visible-md-inline">{{capPlural}}</h3>
        <a href="{{ url('{{smallPlural}}/create') }}" class="btn btn-primary pull-right">
          <i class="fa fa-plus"></i> Create {{capSingle}}
        </a>
      </div>
      <div class="panel-body">
        @include('flash::message')
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
          <tr>
            {{table_header}}
            <th>Edit</th>
            <th>Delete</th>
          </tr>
          @foreach (${{smallPlural}} as ${{smallSingle}})
            <tr>
              {{table_data}}
              <td>
                <a class="btn btn-info" href="{{ action('{{capPlural}}Controller@edit', ${{smallSingle}}->id) }}"><i class="fa fa-pencil"></i> Edit</a>
              </td>
              <td>
              {!! Form::open(['action' => ['{{capPlural}}Controller@destroy', ${{smallSingle}}->id], 'method' => 'DELETE']) !!}
                <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
              {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
          </table>
          {!! ${{smallPlural}}->links() !!}
        </div>
      </div>
    </div>
    <!-- ================================================== -->
  </div>
@stop