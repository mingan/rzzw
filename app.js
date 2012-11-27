
var API_KEY = 'AIzaSyAaaooRwzP5tfuBZ6k95ro8RWlt1MLwpn8';
var mqlServiceUrl = 'https://www.googleapis.com/freebase/v1/mqlread';
var dbpSparql = 'http://dbpedia.org/sparql';

$(document).ready(function () {
    var $OGSelector = $('#OGSelector');
    var $Olympics = $('#Olympics');

    var $EventsSelector = $('#EventsSelector');
    var $Events = $('#Events');

    var $DisciplinesSelector = $('#DisciplinesSelector');
    var $Disciplines = $('#Disciplines');
    /*
    var $Winners = $('#Winners');
    var $Sportsmen = $('#Sportsmen');*/

    function loadOlympics () {
        var query = [{
            "type" : "/olympics/olympic_games",
            "name" : null,
            "/time/event/start_date<=" : "2012-11-22",
            "/time/event/start_date" : null,
            "sort" : ["-/time/event/start_date", "-name"],
            "host_city" : null,
            "mid" : null
        }];
        var params = {
            'key': API_KEY,
            'query': JSON.stringify(query)
        };

        $.getJSON(mqlServiceUrl + '?callback=?', params, function(response) {
            $.each(response.result, function(i, result) {
                $Olympics.append('<li><a href="#' + result['mid'] + '" data-og_name="' + result['name'] + '">' + result['name'] + ', ' + result['host_city'] + '</a></li>');
            });
            olympicsLoaded();
        });
    }
    function olympicsLoaded () {
        $OGSelector
            .removeClass('loading')
            .find('span')
                .text('Choose Olympic Games')
            .end()
            .click();

        $Olympics.find('a').click(function (e) {
            e.preventDefault();

            $OGSelector.find('span').text($(this).text()).attr('data-og_name', $(this).attr('data-og_name'));
            var mid = $(this).attr('href').replace('#', '');

            $EventsSelector
                .parent('.hide')
                .removeClass('hide')
                .end()
                .addClass('loading')
                .find('span').text('Loading...');

            loadEventsFor(mid);
        });
    }

    function loadEventsFor (mid) {
        var query = [{
            "!pd:/time/event/includes_event": [
                {
                    "!index": null,
                    "mid": mid,
                    "type": "/time/event"
                }
            ],
            "mid": null,
            "name": null,
            "sort": "!pd:/time/event/includes_event.!index",
            "type": "/time/event"
        }];
        var params = {
            'key': API_KEY,
            'query': JSON.stringify(query)
        };

        $Events.html('');
        var currentName = ' at the ' + $OGSelector.find('span').attr('data-og_name');
        $.getJSON(mqlServiceUrl + '?callback=?', params, function(response) {
            $.each(response.result, function(i, result) {
                var name = result['name'];
                name = name.replace(currentName, '');
                $Events.append('<li><a href="#' + result['mid'] + '">' + name + '</a></li>');
            });
            eventsLoaded();
        });
    }
    function eventsLoaded () {
        $EventsSelector
            .removeClass('loading')
            .find('span')
                .text('Choose an event')
            .end()
            .click();

        $Events.find('a').click(function (e) {
            e.preventDefault();

            $EventsSelector.find('span').text($(this).text());
            var mid = $(this).attr('href').replace('#', '');

            $DisciplinesSelector
                .parent('.hide')
                .removeClass('hide')
                .end()
                .addClass('loading')
                .find('span').text('Loading...');

            loadDisciplinesFor(mid);
        });
    }

    function loadDisciplinesFor (mid) {
        var query = [{
            "!pd:/time/event/includes_event": [
                {
                    "!index": null,
                    "mid": mid,
                    "type": "/time/event"
                }
            ],
            "mid": null,
            "name": null,
            "sort": "!pd:/time/event/includes_event.!index",
            "type": "/time/event"
        }];
        var params = {
            'key': API_KEY,
            'query': JSON.stringify(query)
        };

        $Disciplines.html('');
        var currentName = $EventsSelector.find('span').text() + ' at the ' + $OGSelector.find('span').attr('data-og_name') + ' – ';
        $.getJSON(mqlServiceUrl + '?callback=?', params, function(response) {
            $.each(response.result, function(i, result) {
                var name = result['name'];
                name = name.replace(currentName, '');
                $Disciplines.append('<li><a href="#' + result['mid'] + '">' + name + '</a></li>');
            });
            disciplinesLoaded();
        });
    }

    function disciplinesLoaded () {
        $DisciplinesSelector
            .removeClass('loading')
            .find('span')
                .text('Choose a discipline')
            .end()
            .click();

            $Disciplines.find('a').click(function (e) {
                e.preventDefault();
                $DisciplinesSelector.find('span').text($(this).text());

                /*
                var hash = $(this).attr('href').replace('#', '');
                loadWinnersOf(hash);
                */
         });
    }

    /*
    function loadWinnersOf (mid) {
        var query = [{
            "type" : "/olympics/olympic_medal_honor",
            "event" : {
                "mid" : mid
            },
            "limit" : 10,
            "medal" : null,
            "medalist" : [],
            "country" : null
        }];
        var params = {
            'key': API_KEY,
            'query': JSON.stringify(query)
        };

        $.getJSON(mqlServiceUrl + '?callback=?', params, function(response) {
            $Winners.html('');
            $.each(response.result, function(i, result) {
                var medalClass = null;
                medalClass = result["medal"].toLowerCase().match(/gold|silver|bronze/);
                if (!medalClass) {
                    medalClass = "unknown";
                } else {
                    medalClass = medalClass[0] + "Medal";
                }
                $Winners.append(
                    '<li class="' + medalClass + '">'
                        + '<a href="#medalist' + i + '" data-country="' + result['country'] + '">'
                        + '<span>'
                        + result.medalist.join('</span>, <span>')
                        + '</span> - ' + result['country']
                        + '</a>'
                        + '</li>');
            });

            $Winners.find('a').click(function (e) {
                e.preventDefault();

                $Sportsmen.html('');
                $(this).find('span').each(function (i, e) {
                    loadPersonalInfo($(e).text());
                });
            })
        });
    }
    function loadPersonalInfo (name) {
        var query =
            'PREFIX foaf: <http://xmlns.com/foaf/0.1/>' +
                'PREFIX dbo: <http://dbpedia.org/ontology/>' +

                'select ?abstract where {' +
                '?p a foaf:Person ;' +
                'foaf:name "' + name + '"@en ;' +
                'dbo:abstract ?abstract .' +
                'FILTER langMatches( lang(?abstract ), "EN" )' +
                '} LIMIT 100';
        var params = {
            'query' : query,
            'default-graph-uri' : 'http://dbpedia.org'
        };
        $.getJSON(dbpSparql, params, function (response) {
            $.each(response.results.bindings, function (i, e) {
                var item = '<li>' +
                    '<h2>' + name + '</h2>' +
                    '<p>' + e.abstract.value + '</p>' +
                    '</li>';
                $Sportsmen.append(item);
            });
        });
    }*/

    loadOlympics();

});