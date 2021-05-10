@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
AdminLTE<br/>
<a href="https://zenn.dev/ikeo/articles/17ead580a029d621e738" target="_blank" rel="noopener noreferrer">元記事</a><br/>
<a href="https://github.com/jeroennoten/Laravel-AdminLTE/wiki" target="_blank" rel="noopener noreferrer">テンプレートwiki</a><br/>
<a href="adminer-4.8.0-mysql.php" target="adminer" rel="noopener noreferrer">adminer</a><br/>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
