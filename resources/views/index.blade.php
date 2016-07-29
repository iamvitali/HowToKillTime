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

@section('additionalScripts')
    {{ Html::script('/js/jquery.twbsPagination.min.js') }}
@endsection

@section('documentReadyJquery')
    @parent

    $('#iNameOrCode').on('input', function () {
        findFilmsAndShows(1); // open first page if there are results
    });

    function findFilmsAndShows(page) {
        if($('#iNameOrCode').val()) {
            var params = {};
            params.r    = 'json';
            params.plot = 'short';
            params.s    = $('#iNameOrCode').val();
            params.page = page;

            var params_in_url_format = '?' + $.param(params);

            window.film_search_ajax = $.ajax({
                type: 'GET',
                url: 'http://www.omdbapi.com/' + params_in_url_format,
                dataType: 'json',
                beforeSend: function () {
                    /* show loading icon */
                    $('#results').html('<div class="text-center"><span class="fa fa-circle-o-notch fa-spin fa-2x"></span></div>');

                    /* Check if the previous request is still running and if so cancel it */
                    if(window.film_search_ajax) window.film_search_ajax.abort();
                },
                success: function (result) {
                    $('#results').load('/showResults/', result, function () {
                        var items_per_page = 10; // this is set by OMDB API and is currently 10

                        /* check if there was anything found and there is more than 1 page */
                        if(result.Response === 'True' && result.totalResults > items_per_page) {
                            $('.pagination').twbsPagination({
                                first: false, // hide "First" navigation button
                                last: false, // hide "Last" navigation button
                                startPage: page, // current page
                                totalPages: Math.ceil(result.totalResults / items_per_page), // rounding up so 1.05 pages would become 2 pages
                                visiblePages: 7,
                                onPageClick: function (event, pageToSwitchTo) {
                                    /* only call AJAX if we are trying to switch to a different page */
                                    if(page !== pageToSwitchTo) {
                                        findFilmsAndShows(pageToSwitchTo);
                                    }
                                }
                            });
                        }
                    });
                },
                error: function (textStatus, errorThrown) {
                    if(errorThrown !== 'abort') {
                        var error_message = 'We are very sorry but our online pigeon didn\'t make it back with the data.<br><br>If this happens again please contact admin@vitali.london with the following error code: ' + textStatus.status;
                        $('#results').html('<div class="alert alert-danger text-center" role="alert">' + error_message + '</div>');
                    }
                }
            });
        } else {
            /* Cancel ajax request if it's still processing */
            window.film_search_ajax.abort();

            /* Empty the results section */
            $('#results').html('');
        }
    }
@endsection