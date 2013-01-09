<?php
if (empty($data)) {
	echo 'No information found';
} else {
	echo '<h2>' . $data['name'] . '</h2>';
	if (!empty($data['img'])) {
		echo '<img width="175" src="' . $data['img'] . '" alt="Wikipedia photo of ' . $data['name'] . '" >';
	}
	if (!empty($data['nyt'])) {
		echo '<span class="nytUrl" data-nyt="' . $data['nyt'] . '" />';
	}
	echo '<p>' . $data['abstract'] . '</p>';
}