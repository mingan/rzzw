<?php
foreach ($data as $games) {
	echo '<li><a href="#' . $games['mid'] . '" data-og_name="' . $games['name'] . '">'
		. $games['name'] . ', ' . $games['host_city'] . '</a></li>';
}