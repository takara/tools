@extends('adminlte::page')

@section('content')
    <div class="card">
    <div class="card-header">
        <h3 class="card-title">Bordered Table</h3>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-block btn-default">新規作成</button>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <table class="table table-bordered">
            <thead>
            <th>変換パターン</th>
            <th>変換文字</th>
            <th>編集</th>
            </thead>
            @foreach($list as $row)
                <tr>
                    <td>{!! $row->pattern !!}</td>
                    <td>{{$row->keyword}}</td>
                    <td><a href="replace_keyword/edit/{{$row->id}}"><button type="button" class="btn btn-block btn-default">編集</button></a></td>
                </tr>
            @endforeach
        </table>
    </div>
    </div>
@stop
