<?php
if (empty($data)) {
	echo 'No extra photos';
} else {
	echo '<h2>Photos form flickr</h2>';
	echo '<div>';
	foreach ($data as $row) {
		echo '<a target="_blank" href="' . $row['page'] . '"><img src="' . $row['thumb'] . '" alt="flickr thumb"></a>';
	}
	echo '</div>';
}