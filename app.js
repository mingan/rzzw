var rzzw = {};
var map = {};

(function () {
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
    this.toggler = function ($toToggle) {
        return $('<a href="#" alt="+" class="toggle btn">-</a>').click(function (e) {
            e.preventDefault();
            $this = $(this);

            $toToggle.toggle($this.text() == '+');

            var tmp = $this.text();
            $this.text($this.attr('alt')).attr('alt', tmp);
        });
    };

    this.removeStringFromElements = function (str, $els) {
        $els.each(function (i, e) {
            $(e).text($(e).text().replace(str, ''));
        });
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
        $Winners.addClass('loading').html('Loading...');
        $Winners.load('dispatch.php?source=freebase&type=winners&params=' + mid, null, function () {
            $Winners
                .removeClass('loading')
                .find('a').click(function (e) {
                    e.preventDefault();
                    _this.loadPersonalInfo($(this));
                });

            map.showMap($Winners);
        });
    }

    this.loadPersonalInfo = function ($link) {
        var $Info = $link.after('<div class="infoblock loading">Loading...</div>').siblings('.infoblock');
        var $Photos = $Info.after('<h3>Photos from flickr</h3><div class="photos"></div>').siblings('.photos').hide();
        var slug = $link.attr('data-wiki');

        $link.after('<h2>' + $link.html() + '</h2>', _this.toggler($link.siblings()));
        $link.remove();

        _this.loadPhotos($Photos, slug);

        $Info.load('dispatch.php?source=dbpedia&type=bio&params=' + slug, null, function () {
            $Info.removeClass('loading');
        });
    }

    this.loadPhotos = function ($Photos, slug) {
        $Photos.load('dispatch.php?source=flickr&type=photos&params=' + slug, null, function () {

            $Photos.show()
                .scroll(function () {
                    var photosLeft = $(this).position()['left'];
                    var $wrapper = $(this).find('>div');
                    var wrapperLeft = $wrapper.position()['left'];

                    $(this)
                        .toggleClass('moreOnLeft', wrapperLeft < photosLeft)
                        .toggleClass('moreOnRight', wrapperLeft + $wrapper.width() - $(this).width() > photosLeft)

                })
                .find('>div')
                    .css('width', $Photos.find('img').length * 262)

        });
    }

}).apply(rzzw);

jQuery(document).ready(function ($) {
    rzzw.loadOlympics();
    map.hideMap();
});
