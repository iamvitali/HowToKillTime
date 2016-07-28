@extends('layouts.master')

@section('title', 'Let\'s Find that film!')

@section('content')
    <h1 class="text-center">Let's find that film!</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="form-group has-feedback">
                <input id="iNameOrCode" type="search" class="form-control" placeholder="Type a name or an IMDB code" />
                <span class="glyphicon glyphicon-search form-control-feedback"></span>
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

    $('#iNameOrCode').on('change input', function () {
        if($('#iNameOrCode').val()) {
            var params = {};
            params.r    = 'json';
            params.plot = 'short';
            params.s    = $('#iNameOrCode').val();

            var params_in_url_format = $.param(params);

            if(params_in_url_format) {
                params_in_url_format = '?' + params_in_url_format;
            }

            window.film_search_ajax = $.ajax({
                type: 'GET',
                url: 'http://www.omdbapi.com/' + params_in_url_format,
                beforeSend: function () {
                    $('#results').html('<div class="text-center"><span class="fa fa-circle-o-notch fa-spin fa-2x"></span></div>');

                    /* Check if the previous request is still running and if so cancel it */
                    if(window.film_search_ajax) window.film_search_ajax.abort();
                },
                success: function (result) {
                    $('#results').load('/showResults/', result);
                },
                error: function (textStatus, errorThrown) {
                    if(errorThrown !== 'abort') {
                        var error_message = 'We are very sorry but our online pigeon didn\'t make it back with the data.<br><br>If this happens again please contact admin@vitali.london with the following error code: ' + textStatus.status;
                        $('#results').html('<div class="alert alert-danger text-center" role="alert">' + error_message + '</div>');
                    }
                },
                dataType: 'json'
            });
        } else {
            /* Cancel ajax request if it's still processing */
            window.film_search_ajax.abort();

            /* Empty the results section */
            $('#results').html('');
        }

    });
@endsection