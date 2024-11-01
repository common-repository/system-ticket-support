<?php
add_filter('sts_theme_current_style_handle', 'sts_remove_theme_current_style_handle');
function sts_remove_theme_current_style_handle($handles) {
	$theme = get_template();

	$theme_handles = array();
	switch ($theme) {
		case 'twentysixteen': {
			$theme_handles = array(
				'twentysixteen-fonts',
				'genericons',
				'twentysixteen-style',
				'twentysixteen-block-style',
				'twentysixteen-ie',
				'twentysixteen-ie8',
				'twentysixteen-ie7',
			);
			break;
		}

		case 'twentyseventeen': {
			$theme_handles = array(
				'twentyseventeen-fonts',
				'twentyseventeen-style',
				'twentyseventeen-block-style',
				'twentyseventeen-colors-dark',
				'twentyseventeen-ie9',
				'twentyseventeen-ie8'
			);

			break;
		}
		case 'twentynineteen': {
			$theme_handles = array(
				'twentynineteen-style',
				'twentynineteen-print-style',
			);
			break;
		}
		case 'twentytwenty': {
			$theme_handles = array(
				'twentytwenty-style',
				'twentytwenty-print-style',
			);
			break;
		}
	}

	return array_merge($handles, $theme_handles);
}

add_filter('sts_theme_current_script_handle', 'sts_remove_theme_current_script_handle');
function sts_remove_theme_current_script_handle($handles) {
	$theme = get_template();

	$theme_handles = array();
	switch ($theme) {
		case 'twentysixteen': {
			$theme_handles = array(
				'twentysixteen-html5',
				'twentysixteen-skip-link-focus-fix',
				'twentysixteen-keyboard-image-navigation',
				'twentysixteen-script',
			);

			break;
		}
		case 'twentyseventeen': {
			$theme_handles = array(
				'html5',
				'twentyseventeen-skip-link-focus-fix',
				'twentyseventeen-navigation',
				'twentyseventeen-global',
				'jquery-scrollto'
			);
			break;
		}

		case 'twentynineteen': {
			$theme_handles = array();

			break;
		}
		case 'twentytwenty': {
			$theme_handles = array();

			break;
		}
	}

	return array_merge($handles, $theme_handles);
}