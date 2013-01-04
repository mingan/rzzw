<?php
foreach ($data as $event) {
	echo '<li><a href="#' . $event['mid'] . '">' . $event['name'] . '</a></li>';
}