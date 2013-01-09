<?php
if (empty($data)) {
	echo 'No articles found';
} else {
	foreach ($data as $article) {
		echo '<a class="article" href="' . $article['url'] . '" target="_blank">';
		echo '<h3>' . $article['title'] . '</h3>';
		echo '<span class="date" time="' . date('c', strtotime($article['date'])) . '">'
			. date('Y/m/d', strtotime($article['date']))
			. '</span>';

		if (!empty($article['small_image_url'])) {
			echo '<img src="' . $article['small_image_url'] . '" alt="Article photo" ';
			if (!empty($article['small_image_height'])) {
				echo ' height="' . $article['small_image_height'] . '"';
			}if (!empty($article['small_image_width'])) {
				echo ' width="' . $article['small_image_width'] . '"';
			}
			echo ' />';
		}
		echo '<p>' . $article['body'] . '</p>';

		echo '</a>';
	}
}