var rzzw = {};
var map = {};

(function () {
    var _this = this;
    var cache = {};

    var $Map = $('#Map');
    var map = new google.maps.Map(
        document.getElementById("Map"),
        {
            zoom: 2,
            center: new google.maps.LatLng(0, 0),

            mapTypeId : google.maps.MapTypeId.ROADMAP
        }
    );
    var geocoder = new google.maps.Geocoder();

    var markers = [];

    this.hideMap = function () {
        $Map.hide();
    }
    this.showMap = function ($Winners) {
        $Map.show();
        $.each(markers, function (i, m) {
            m.setMap(null);
        });
        markers = [];

        var bounds = new google.maps.LatLngBounds();
        $Winners.find('li.medal').each(function (i, e) {
            _this.geocode($(e).attr('data-country'), function (p) {
                bounds.extend(p);
                map.fitBounds(bounds);
                if (map.getZoom() > 6) {
                    map.setZoom(6);
                }
            });
        });

    }

    this.addMarker = function (country, position, callback) {
        var marker = new google.maps.Marker({
            map : map,
            position : position,
            title : country
        });
        markers.push(marker);
        callback(position);
    }
    this.geocode = function (query, callback) {
        if (cache[query] != undefined) {
            _this.addMarker(query, cache[query], callback);
        } else {
            geocoder.geocode( {
                'address': query
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0].types.indexOf("country") != -1) {
                        var pos = new google.maps.LatLng(results[0].geometry.location.Ya, results[0].geometry.location.Za);
                        _this.addMarker(query, pos, callback)
                        cache[query] = pos;
                    }
                } else {
                    alert("Geocode was not successful for the following reason: " + status);
                }
            });
        }
    }
}).apply(map);

(function () {
    var _this = this;
    var $OGSelector = $('#OGSelector');
    var $Olympics = $('#Olympics');

    var $EventsSelector = $('#EventsSelector');
    var $Events = $('#Events');

    var $DisciplinesSelector = $('#DisciplinesSelector');
    var $Disciplines = $('#Disciplines');

    var $Winners = $('#Winners');
    var $Infoblock = $('#Infoblock');
    var $News = $('#News');
    var $Photos = $('#Photos');

    $Winners.parent().hide();
    $Infoblock.parent().hide();
    $News.parent().hide();
    $Photos.parent().hide();

    this.removeStringFromElements = function (str, $els) {
        $els.each(function (i, e) {
            $(e).text($(e).text().replace(str, ''));
        });
    }

    this.clearDetails = function () {
        $Winners.html('');
        $Infoblock.html('');
        $News.html('');
        $Photos.html('');
    }

    this.loadOlympics = function () {
        $Olympics.load('dispatch.php?source=freebase&type=games', null, function () {
            _this.olympicsLoaded();
        });
    }
    this.olympicsLoaded = function () {
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

            _this.loadEventsFor(mid);
        });
    }

    this.loadEventsFor = function (mid) {
        _this.clearDetails();
        $EventsSelector
            .parent('.hide')
            .removeClass('hide')
            .end()
            .addClass('loading')
            .find('span').text('Loading...');

        $Events.load('dispatch.php?source=freebase&type=events&params=' + mid, null, function () {
            var currentName = new RegExp(' at (the)? ' + $OGSelector.find('span').attr('data-og_name') + '?');
            _this.removeStringFromElements(currentName, $Events.find('li a'));
            _this.eventsLoaded();
        });
    }
    this.eventsLoaded = function () {
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

            _this.loadDisciplinesFor(mid);
        });
    }

    this.loadDisciplinesFor = function (mid) {
        _this.clearDetails();
        $DisciplinesSelector
            .parent('.hide')
            .removeClass('hide')
            .end()
            .addClass('loading')
            .find('span').text('Loading...');

        $Disciplines.load('dispatch.php?source=freebase&type=disciplines&params=' + mid, null, function () {
            var currentName = new RegExp($EventsSelector.find('span').text() + ' at (the)? ' + $OGSelector.find('span').attr('data-og_name') + '?' + ' – ');
            _this.removeStringFromElements(currentName, $Disciplines.find('li a'));
            _this.disciplinesLoaded();
        });
    }
    this.disciplinesLoaded = function () {
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
            _this.loadWinnersOf(hash);
        });
    }

    this.loadWinnersOf = function (mid) {
        _this.clearDetails();
        $Winners.addClass('loading').parent().show();
        $Winners.load('dispatch.php?source=freebase&type=winners&params=' + mid, null, function () {
            $Winners
                .removeClass('loading')
                .find('a').click(function (e) {
                    e.preventDefault();
                    _this.loadPersonalInfo($(this));
                    window.location.hash = '#Infoblock';
                });

            map.showMap($Winners);
        });
    }

    this.loadPersonalInfo = function ($link) {
        var slug = $link.attr('data-wiki');

        $Photos.html('');
        $News.html('');

        $Infoblock
            .html('')
            .parent()
                .show()
            .end()
            .addClass('loading')
            .load('dispatch.php?source=dbpedia&type=bio&params=' + slug, null, function () {
                $Infoblock.removeClass('loading');

                _this.loadNews($Infoblock.find('.nytUrl').attr('data-nyt'));
            });

        _this.loadPhotos(slug);
    }

    this.loadPhotos = function (slug) {
        $Photos.load('dispatch.php?source=flickr&type=photos&params=' + slug, null, function () {
            $Photos
                .parent()
                    .show()
                .end()
        });
    }

    this.loadNews = function (nytUrl) {
        $News.parent().show();
        if (!nytUrl || nytUrl == '') {
            $News.html('No articles found');
            return;
        }

        $News.addClass('loading')
            .load('dispatch.php?source=nytdata&type=data&params=' + nytUrl, null, function () {
                $News.removeClass('loading');
            });
    }

}).apply(rzzw);

jQuery(document).ready(function ($) {
    rzzw.loadOlympics();
    map.hideMap();
});
