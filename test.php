<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="app.css" media="screen" rel="stylesheet" type="text/css">
</head>
<body>

	<div class="navbar navbar-inverse">
		<div class="navbar-inner">
			<div class="container">
				<ul class="nav">
					<li class="dropdown">
						<a id="OGSelector" href="#" class="dropdown-toggle loading" data-toggle="dropdown">
							<span>Loading&hellip;</span>
							<b class="caret"></b>
						</a>
						<ul id="Olympics" aria-labelledby="OGSelector" class="dropdown-menu" role="menu"></ul>
					</li>
					<li class="dropdown hide">
						<a id="EventsSelector" href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span>Choose an event</span>
							<b class="caret"></b>
						</a>
						<ul id="Events" aria-labelledby="EventsSelector" class="dropdown-menu" role="menu"></ul>
					</li>
					<li class="dropdown hide">
						<a id="DisciplinesSelector" href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span>Choose a discipline</span>
							<b class="caret"></b>
						</a>
						<ul id="Disciplines" aria-labelledby="DisciplinesSelector" class="dropdown-menu" role="menu"></ul>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container content">

		<ul id="Winners"></ul>
		<div id="Map"></div>

		<div id="InfoblockContainer">
			<div id="Infoblock"></div>
		</div>

		<div id="NewsContainer">
			<h2><a href="http://developer.nytimes.com"><img src="http://graphics8.nytimes.com/packages/images/developer/logos/poweredby_nytimes_30b.png" width="30" height="30">New York Times articles</a></h2>
			<div id="News"></div>
		</div>
		<div id="PhotosContainer">
			<h2>Photos from flickr</h2>
			<div id="Photos"></div>
		</div>

		<div id="Attribution">
			<h3>Attribution</h3>
			<a href="http://wifo5-03.informatik.uni-mannheim.de/flickrwrappr/">flickr&trade; wrappr</a>
			<a href="http://commons.wikimedia.org/wiki/File:Gold_medal.svg">Gold medal icon</a>
			<a href="http://commons.wikimedia.org/wiki/File:Silver_medal.svg">Silver medal icon</a>
			<a href="http://commons.wikimedia.org/wiki/File:Bronze_medal.svg">Bronze medal icon</a>
		</div>
	</div>

	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAaaooRwzP5tfuBZ6k95ro8RWlt1MLwpn8&sensor=false"></script>
	<script src="sort.js"></script>
	<script src="app.js"></script>
</body>
</html>