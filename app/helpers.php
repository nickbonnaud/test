<?php

/**
 * Display a flash message
 *
 * @param string $title
 * @param string $message
 * @return \App\Http\Flash
 */
function flash($title = null, $message = null) {
	$flash = app('App\Http\Flash');

	if (func_num_args() == 0) {
		return $flash;
	}

	return $flash->info($title, $message);
}


/**
 * The path to a given profile
 *
 * @param App\Profile $profile
 * @return string
 */
function profile_path(App\Profile $profile) {
	return route('profiles.show', ['profiles' => $profile->id]);
}

function clean_newlines($string) {
    $string = str_replace("\r\n", "\n", $string);
    $string = str_replace("\r", "\n", $string);
    return $string;
};

/**
 * Return a string of HTML, wrapping sections of text in <p>s using newlines as delimiters
 * @param string $text
 * @return string $html
 */
function html_newlines_to_p($text) {
    $text = clean_newlines($text);
    $html = explode("\n", $text);
    $html = array_map(function($string) {
        return empty($string) ? '&nbsp;' : $string;
    }, $html);
    return '<p>' . implode('</p><p>', $html) . '</p>';
}