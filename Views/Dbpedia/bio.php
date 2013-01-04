<?php
if (empty($data)) {
	echo 'No information found';
} else {
	if (!empty($data['img'])) {
		echo '<img width="175" src="' . $data['img'] . '" alt="Wikipedia photo of ' . $data['name'] . '" >';
	}
	echo '<p>' . $data['abstract'] . '</p>';
}