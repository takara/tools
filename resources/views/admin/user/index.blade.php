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
            <th>ユーザー名</th>
            <th>メールアドレス</th>
            </thead>
            @foreach($user as $row)
                <tr>
                    <td>{{$row->name}}</td>
                    <td>{{$row->email}}</td>
                    <th><button type="button" class="btn btn-block btn-default">編集</button></th>
                </tr>
            @endforeach
        </table>
    </div>
    </div>
@stop
