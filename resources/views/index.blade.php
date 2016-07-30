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
            <div class="text-center">
                <div class="pagination">
                    <a class="btn btn-default next-page" style="display: none;">Load more</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additionalScripts')
    {{ Html::script('/js/jquery.twbsPagination.min.js') }}
@endsection

@section('documentReadyJquery')
    @parent

    var loading_icon_html = '<div class="loading text-center"><span class="fa fa-circle-o-notch fa-spin fa-2x"></span></div>';

    $('#iNameOrCode').on('input', function () {
        clearTimeout(window.timeout_to_start_search);
        findFilmsAndShows(1); // open first page if there are results
    });

    /* Auto-click on "Load more" button when user scrolls to the bottom of the page */
    $(window).scroll(function(){
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('.next-page').trigger('click');
            console.log('triggered!');
        }
    });

    function findFilmsAndShows(page) {
        if($('#iNameOrCode').val()) {
            var params = {};
            params.r    = 'json';
            params.v    = 1; // OMDB API version
            params.s    = $('#iNameOrCode').val();
            params.page = page;

            var params_in_url_format = '?' + $.param(params);

            /* Don't send ajax immediately to prevent spamming requests while user is typing */
            window.timeout_to_start_search = setTimeout(function() {
                window.film_search_ajax = $.ajax({
                    type: 'GET',
                    url: 'http://www.omdbapi.com/' + params_in_url_format,
                    dataType: 'json',
                    beforeSend: function () {
                        /* remove load more content button */
                        $('.next-page').off('click').hide();

                        /* show loading icon */
                        if(page === 1) {
                            $('#results').html(loading_icon_html);
                        } else {
                            $('#results').append(loading_icon_html);
                        }

                        /* Check if the previous request is still running and if so cancel it */
                        if(window.film_search_ajax) window.film_search_ajax.abort();
                    },
                    success: function (result) {
                        var items_per_page = 10; // this is set by OMDB API and is currently 10

                        if(page === 1) {
                            $('#results').load('/showResults/', result, function () {
                                /* check if there was anything found and there is more than 1 page */
                                if(result.Response === 'True') {
                                    var total_pages = Math.ceil(result.totalResults / items_per_page); // rounding up so 1.05 pages would become 2 pages

                                    /* add load more content button */
                                    if((page+1) <= total_pages) {
                                        $('.next-page').show().off('click').on('click', function () {
                                            findFilmsAndShows(page+1);
                                        });
                                    }
                                }
                            });
                        } else {
                            $.post('/showResults/', result, function(data) {
                                console.log(page);

                                /* check if there was anything found and there is more than 1 page */
                                if(result.Response === 'True') {
                                    /* remove the loading circle - we do not need to do that on page 1 as it overwrites it */
                                    $('.loading').remove();

                                    /* append the loaded content to the existing table */
                                    $('table.search-results').append($(data).find('table.search-results > tbody > tr').filter('tr'));

                                    var total_pages = Math.ceil(result.totalResults / items_per_page); // rounding up so 1.05 pages would become 2 pages

                                    /* add load more content button */
                                    if((page+1) <= total_pages) {
                                        $('.next-page').show().off('click').on('click', function () {
                                            findFilmsAndShows(page+1);
                                        });
                                    }
                                }
                            });
                        }
                    },
                    error: function (textStatus, errorThrown) {
                        if(errorThrown !== 'abort') {
                            var error_message = 'We are very sorry but our online pigeon didn\'t make it back with the data.<br><br>If this happens again please contact admin@vitali.london with the following error code: ' + textStatus.status;
                            var error_html = '<div class="alert alert-danger text-center" role="alert">' + error_message + '</div>';

                            if(page === 1) {
                                $('#results').html(error_html);
                            } else {
                                $('#results').append(error_html);
                            }
                        }
                    }
                });
            }, 300);
        } else {
            /* Cancel ajax request if it's still processing */
            window.film_search_ajax.abort();

            /* Empty the results section */
            $('#results').html('');

            /* remove load more content button */
            $('.next-page').off('click').hide();
        }
    }
@endsection