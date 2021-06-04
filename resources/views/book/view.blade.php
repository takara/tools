@extends('layouts.bootstrap')

@section('title')サンプル@endsection

@section('js')
<script>
    var page = 0;
    $(document).ready(function(){
        $("body").keydown(function(event) {
            console.log("keysowb");
            const a = event.key;
            const b = event.which;
            const c = event.keyCode;
            const d = event.code;
            switch (event.keyCode) {
                case 32:
                case 37:
                    page++;
                    break;
                case 39:
                    page--;
                    break;
            }

            $("img").attr("src","/book/"+page);
            $("img").height($(window).height());
            $("#sp1").text(page);
            $("#sp2").text(c);
        });
        console.log($(window).width());
        console.log($(window).height());
        $("img").height($(window).height());
        $("input.btn").click(function(event) {
            console.log("button");
        });
    });
</script>
@endsection

@section('content')
<div id="container" class="container">
        <div class="row">
            <div class="col">
                <p>: <span id="sp1"></span></p>
                <p>: <span id="sp2"></span></p>
            </div>
            <div class="col-8">
                <img src="/book/0">
            </div>
            <div class="col">
                One of three columns
            </div>
        </div>
</div>
@endsection
