var dbpSparql = 'http://dbpedia.org/sparql';

jQuery(document).ready(function ($) {
    var $OGSelector = $('#OGSelector');
    var $Olympics = $('#Olympics');

    var $EventsSelector = $('#EventsSelector');
    var $Events = $('#Events');

    var $DisciplinesSelector = $('#DisciplinesSelector');
    var $Disciplines = $('#Disciplines');

    var $Winners = $('#Winners');
    var $Map = $('#Map');//.hide();
    var map = new google.maps.Map(
        document.getElementById("Map"),
        {
            zoom: 2,
            center: new google.maps.LatLng(0, 0),

            mapTypeId : google.maps.MapTypeId.ROADMAP
        }
    );
    var geocoder = new google.maps.Geocoder();


    function repmoveStringFromElements (str, $els) {
        $els.each(function (i, e) {
            $(e).text($(e).text().replace(str, ''));
        });
    }

    function loadOlympics () {
        $Olympics.load('dispatch.php?source=freebase&type=games', null, function () {
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
            var mid = $(this).attr('href').replace('#', '');;

            loadEventsFor(mid);
        });
    }

    function loadEventsFor (mid) {
        $EventsSelector
            .parent('.hide')
            .removeClass('hide')
            .end()
            .addClass('loading')
            .find('span').text('Loading...');

        $Events.load('dispatch.php?source=freebase&type=events&params=' + mid, null, function () {
            var currentName = new RegExp(' at (the)? ' + $OGSelector.find('span').attr('data-og_name') + '?');
            repmoveStringFromElements(currentName, $Events.find('li a'));
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

            loadDisciplinesFor(mid);
        });
    }

    function loadDisciplinesFor (mid) {
        $DisciplinesSelector
            .parent('.hide')
            .removeClass('hide')
            .end()
            .addClass('loading')
            .find('span').text('Loading...');

        $Disciplines.load('dispatch.php?source=freebase&type=disciplines&params=' + mid, null, function () {
            var currentName = new RegExp($EventsSelector.find('span').text() + ' at (the)? ' + $OGSelector.find('span').attr('data-og_name') + '?' + ' – ');
            repmoveStringFromElements(currentName, $Disciplines.find('li a'));
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

                var hash = $(this).attr('href').replace('#', '');
                loadWinnersOf(hash);
         });
    }

    function loadWinnersOf (mid) {
        $Winners.addClass('loading').html('Loading...');
        $Winners.load('dispatch.php?source=freebase&type=winners&params=' + mid, null, function () {
            $Winners
                .removeClass('loading')
                .find('a').click(function (e) {
                    e.preventDefault();
                    loadPersonalInfo($(this));
                });

            showMap();
        });
    }

    function loadPersonalInfo ($link) {
        var $Info = $link.after('<div class="infoblock loading">Loading...</div>').siblings('.infoblock');
        var $Photos = $Info.after('<div class="photos" id="Lightbox' + Math.round(Math.random() * 10000) + '"></div>').siblings('.photos');
        var slug = $link.attr('data-wiki');

        loadPhotos($Photos, slug);

        $Info.load('dispatch.php?source=dbpedia&type=bio&params=' + slug, null, function () {
            $Info.removeClass('loading');
        });
    }

    function loadPhotos ($Photos, slug) {
        $Photos.load('dispatch.php?source=flickr&type=photos&params=' + slug);
    }

    loadOlympics();

    var markers = [];
    function showMap () {
        $Map.show();
        $.each(markers, function (i, m) {
            m.setMap(null);
        });
        markers = [];

        var bounds = new google.maps.LatLngBounds();
        $Winners.find('li.medal').each(function (i, e) {
            geocode($(e).attr('data-country'), function (p) {
                bounds.extend(p);
                map.fitBounds(bounds);
            });
        });

    }

    function geocode(query, callback) {
        geocoder.geocode( {
            'address': query
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var pos = new google.maps.LatLng(results[0].geometry.location.Ya, results[0].geometry.location.Za);
                var marker = new google.maps.Marker({
                    map : map,
                    position : pos,
                    title : query
                });
                markers.push(marker);
                callback(pos);
            } else {
                alert("Geocode was not successful for the following reason: " + status);
            }
        });
    }
});
