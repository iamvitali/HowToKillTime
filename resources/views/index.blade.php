@extends('layouts.master')

@section('title', 'Let\'s Find that film!')

@section('css')
    #backgroundIconYear {
        margin-top: 8.5px; /* fixes fa icon not being vertically centered */
        z-index: 3; /* fixes icon disappearing on input being focused */
    }

    #dYearBox {
        display: block; /* fixes incorrect sizing of the year input box */
    }

    #iYear {
        border-radius: 4px; /* fixes square corners on the right */
    }

    #searchResults .panel {
        margin: 0;
    }

    #searchResults .panel-default {
        background-color: #fff;
        padding: 5px;
        border-radius: 0;
    }

    #searchResults .panel-default.active {
        background-color: #eff0f1;
    }

    #searchResults .panel-default > a:hover {
        text-decoration: none;
    }

    #searchResults .panel-default:hover {
        background-color: #eff0f1;
    }

@endsection

@section('content')
    <h1 class="text-center">Let's find that film!</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="form-group has-feedback">
                <input id="iNameOrCode" type="search" class="form-control" placeholder="Type a name or an IMDB code" />
                <span class="glyphicon glyphicon-search form-control-feedback"></span>

                <br>
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-lg-4 col-lg-offset-2">
                        <div id="dropdownMenuType" data-selected-option="" class="dropdown">
                            <button class="btn btn-default btn-block dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Type: <span id='sDropdownMenuTypeSelectedOption'>Everything</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuType">
                                <li><a data-type="">Everything</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a data-type="movie">Films</a></li>
                                <li><a data-type="series">Series</a></li>
                                <li><a data-type="episode">Episodes</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-lg-4">
                        <div id="dYearBox" class="input-group">
                            <input id="iYear" type="text" class="form-control" placeholder="Year">
                            <span id='backgroundIconYear' class="fa fa-calendar form-control-feedback"></span>
                        </div>
                    </div>
                </div>

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

@section('documentReadyJquery')
    @parent

    var loading_icon_html = '<div class="loading text-center"><span class="fa fa-circle-o-notch fa-spin fa-2x"></span></div>';

    $('#dropdownMenuType ul > li > a').on('click', function () {
        $('#sDropdownMenuTypeSelectedOption').text($(this).text());
        $('#sDropdownMenuTypeSelectedOption').attr('data-selected-option', $(this).attr('data-type'));

        $('#iNameOrCode').trigger('input'); // run search
    });

    $('#iNameOrCode, #iYear').on('input', function () {
        clearTimeout(window.timeout_to_start_search); // if the user is still typing this will reset the timer when seach should start
        findFilmsAndShows(1); // open first page (if there are results)
    });

    /* Auto-click on "Load more" button when user scrolls to the bottom of the page */
    $(window).scroll(function(){
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('.next-page').trigger('click');
        }
    });

    function findFilmsAndShows(page) {
        if($('#iNameOrCode').val()) {

            /* Check if Year field is empty or has 4 digits */
            if(!/^$|^\d{4}$/.test($('#iYear').val())) {
                clearResults();
                var warning_message = 'Please enter the year using 4 digits, for example: 2015<br>Or you can also leave it empty.';
                displayMessage(warning_message, 'warning');
                return;
            }

            var params = {};
            params.r    = 'json';
            params.v    = 1; // OMDb API version

            var film_name_or_imdb_code = $.trim($('#iNameOrCode').val());
            if(/^tt\d{7}$/.test(film_name_or_imdb_code)) {
                params.i    = film_name_or_imdb_code; // searching by IMDb ID
            } else {
                params.s    = film_name_or_imdb_code; // searching by name
                params.page = page;
            }

            /* Security check that the provided type is allowed */
            if($.inArray($('#sDropdownMenuTypeSelectedOption').attr('data-selected-option'), ['movie', 'series', 'episode']) !== -1) {
                params.type = $('#sDropdownMenuTypeSelectedOption').attr('data-selected-option');
            }

            /* Only specifying the year if it was provided by the user */
            if(/^\d{4}$/.test($('#iYear').val())) {
                params.y = $('#iYear').val();
            }

            var params_in_url_format = '?' + $.param(params);

            /* Don't send ajax immediately to prevent spamming requests while user is typing */
            window.timeout_to_start_search = setTimeout(function() {
                window.film_search_ajax = $.ajax({
                    type: 'GET',
                    url: 'https://www.omdbapi.com/' + params_in_url_format,
                    dataType: 'jsonp',
                    beforeSend: function () {
                        /* remove load more content button */
                        $('.next-page').off('click').hide();

                        /* Check if the previous request is still running and if so cancel it */
                        if(window.film_search_ajax) window.film_search_ajax.abort();

                        /* show loading icon */
                        if(page === 1) {
                            $('#results').html(loading_icon_html);
                        } else {
                            $('#results').append(loading_icon_html);
                        }
                    },
                    success: function (result) {
                        if(page === 1) {
                            /*
                            * Check if imdbID is found in the response
                            * If so this means the search was performed by IMDb ID and we only have one element
                            * Because an array "Search" was not created we need to create it and add the single film to it
                            * so that we don't have to change our HTML views
                            */
                            if(result.imdbID) {
                                result['Search'] = [];
                                result['Search'].push({
                                    'Poster':   result.Poster,
                                    'Title':    result.Title,
                                    'Type':     result.Type,
                                    'Year':     result.Year,
                                    'imdbID':   result.imdbID,
                                });

                                result.totalResults = 1;
                            }

                            $('#results').load('/showResults/', result, function () {
                                /* check if there was anything found and there is more than 1 page */
                                if(result.Response === 'True') {
                                    activateLoadMoreButton(result.totalResults, page);
                                    attachFunctionalityToFilmList();
                                }
                            });
                        } else {
                            $.post('/showResults/', result, function(data) {
                                /* check if there was anything found and there is more than 1 page */
                                if(result.Response === 'True') {
                                    /* remove the loading icon - we don't need to do that on page 1 as it overwrites it */
                                    $('.loading').remove();

                                    /* append the loaded content to the existing table */
                                    $('#searchResults').append($(data).find('> .panel').filter('.panel'));

                                    window.stuff = $(data);

                                    activateLoadMoreButton(result.totalResults, page);
                                    attachFunctionalityToFilmList();
                                }
                            });
                        }
                    },
                    error: function (textStatus, errorThrown) {
                        if(errorThrown !== 'abort') { // check that the error is not because we have cancelled the previous ajax request
                            var error_message = 'We are very sorry but our online pigeon didn\'t make it back with the data.<br><br>If this happens again please contact admin@vitali.london with the following error code: ' + textStatus.status;
                            displayMessage(error_message, 'error', page);
                        }
                    }
                });
            }, 300);
        } else {
            clearResults();
        }
    }

    function activateLoadMoreButton(total_results, page) {
        var items_per_page = 10; // this is set by OMDb API and is currently 10

        var total_pages = Math.ceil(total_results / items_per_page); // rounding up so 1.05 pages would become 2 pages

        /* add load more content button */
        if((page+1) <= total_pages) {
            $('.next-page').show().off('click').on('click', function () {
                findFilmsAndShows(page+1);
            });
        }
    }

    function attachFunctionalityToFilmList() {
        $('#searchResults > .panel').off('show.bs.collapse').on('show.bs.collapse', function () {
            $(this).addClass('active');

            loadFilmContent($(this).attr('data-imdb-id'));
        });

        $('#searchResults > .panel').off('hide.bs.collapse').on('hide.bs.collapse', function () {
            $(this).removeClass('active');


        });
    }

    function loadFilmContent(imdb_id) {
        if($('#' + imdb_id).attr('data-content-loaded') !== 'true') {
            $('#' + imdb_id).attr('data-content-loaded', true); // Will only send one ajax request to fetch the content

            var params      = {};
            params.r        = 'json';
            params.v        = 1; // OMDb API version
            params.i        = imdb_id;
            params.plot     = 'full';
            params.tomatoes = true;

            var params_in_url_format = '?' + $.param(params);

            $.ajax({
                type: 'GET',
                url: 'https://www.omdbapi.com/' + params_in_url_format,
                dataType: 'jsonp',
                beforeSend: function () {
                    /* show loading icon */
                    $('#' + imdb_id + ' > .panel-body').prepend(loading_icon_html);
                },
                success: function (result) {
                    console.log(result);
                    var film = $('#' + imdb_id + ' > .panel-body');

                    film.find('.film-info-actors').text(result.Actors);
                    film.find('.film-info-box-office').text(result.BoxOffice);
                    film.find('.film-info-country').text(result.Country);
                    film.find('.film-info-dvd').text(result.DVD);
                    film.find('.film-info-director').text(result.Director);
                    film.find('.film-info-genre').text(result.Genre);
                    film.find('.film-info-language').text(result.Language);
                    film.find('.film-info-metascore').text(result.Metascore);
                    film.find('.film-info-plot').text(result.Plot);
                    film.find('.film-info-production').text(result.Production);
                    film.find('.film-info-rated').text(result.Rated);
                    film.find('.film-info-released').text(result.Released);
                    film.find('.film-info-runtime').text(result.Runtime);
                    film.find('.film-info-website').text(result.Website);
                    film.find('.film-info-writer').text(result.Writer);
                    film.find('.film-info-imdb-rating').text(result.imdbRating);
                    film.find('.film-info-imdb-votes').text(result.imdbVotes);
                    film.find('.film-info-seasons').text(result.totalSeasons);

                    if(result.Awards !== 'N/A') {
                        film.find('.film-info-awards').text(result.Awards);
                        film.find('.film-trophies').show();
                    }

                    if(result.tomatoURL !== 'N/A') {
                        film.find('.film-info-tomato-url').attr('href', result.tomatoURL);

                        film.find('.film-info-tomato-consensus').text(result.tomatoConsensus);
                        film.find('.film-info-tomato-fresh').text(result.tomatoFresh);
                        film.find('.film-info-tomato-image').text(result.tomatoImage);
                        film.find('.film-info-tomato-meter').text(result.tomatoMeter);
                        film.find('.film-info-tomato-rating').text(result.tomatoRating);
                        film.find('.film-info-tomato-reviews').text(result.tomatoReviews);
                        film.find('.film-info-tomato-rotten').text(result.tomatoRotten);

                        film.find('.film-info-tomato-user-meter').text(result.tomatoUserMeter);
                        film.find('.film-info-tomato-user-rating').text(result.tomatoUserRating);
                        film.find('.film-info-tomato-user-reviews').text(result.tomatoUserReviews);

                        film.find('.vegetable-section').show();
                    }


                    if(result.imdbRating !== 'N/A') {
                        film.find('.film-info-imdb-rating-progress-bar').attr('aria-valuenow', result.imdbRating).css('width', (result.imdbRating * 10) + '%');
                    }

                    film.find('.loading').remove();
                    film.find('.film-info').css('visibility', 'visible');
                },
                error: function (textStatus, errorThrown) {
                    var film = $('#' + imdb_id + ' > .panel-body');
                    var error_message = 'We are very sorry but our online pigeon didn\'t make it back with the data.<br><br>If this happens again please contact admin@vitali.london with the following error code: ' + textStatus.status;
                    film.html('<div class="alert alert-danger text-center" role="alert">' + error_message + '</div>')
                }
            });
        }
    }

    function displayMessage(message, type, page) {
        var div_class = '';
        switch(type) {
            case "warning":
                div_class = 'alert-warning';
                break;
            case "error":
                div_class = 'alert-danger';
                break;
        }

        var html = '<div class="alert ' + div_class + ' text-center" role="alert">' + message + '</div>';

        if(typeof page != 'undefined' && page === 1) {
            $('#results').html(html);
        } else {
            $('#results').append(html);
        }
    }

    function clearResults() {
        /* Cancel ajax request if it's still processing */
        if(typeof window.film_search_ajax != 'undefined') {
            window.film_search_ajax.abort();
        }

        /* Empty the results section */
        $('#results').html('');

        /* remove load more content button */
        $('.next-page').off('click').hide();
    }
@endsection