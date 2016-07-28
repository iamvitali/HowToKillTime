@extends('layouts.master')

@section('title', 'Let\'s Find that film!')

@section('content')
    <h1 class="text-center">Let's find that film!</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="input-group">
                <input id="iNameOrCode" type="text" class="form-control" placeholder="Type a name or an IMDB code" />
                <span class="input-group-btn">
                    <a id="bSearchForFilm" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
                </span>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div id='results'>
            </div>
        </div>
    </div>
@endsection

@section('documentReadyJquery')
    @parent

    $("#iNameOrCode").keyup(function (event) {
        if(event.keyCode == 13) {
            $("#bSearchForFilm").click();
        }
    });

    $('#bSearchForFilm').on('click', function () {
        var params = {};
        params.r    = 'json';
        params.plot = 'short';
        params.s    = $('#iNameOrCode').val();

        var params_in_url_format = $.param(params);

        if(params_in_url_format) {
            params_in_url_format = '?' + params_in_url_format;
        }

        $.ajax({
            type: 'GET',
            url: 'http://www.omdbapi.com/' + params_in_url_format,
            beforeSend: function () {
                $('#results').html('<div class="text-center"><span class="fa fa-circle-o-notch fa-spin fa-2x"></span></div>');
            },
            success: function (result) {
                $('#results').load('/showResults/', result);
            },
            dataType: 'jsonp'
        });

    });
@endsection