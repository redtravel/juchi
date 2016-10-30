<?php
/**
 * Locale definition
 *
 * IMPORTANT: Try to keep the locale names short, they take away valuable space on the screen!
 *
 * Documentation of the keys:
 *  - 'messages':            The directory where the locale's files are. (may seem redundant but allows to have fr-FR and fr-CA
 *                           tap into the same language file.)
 *  - 'charset':             Character set of the locale's messages files.
 *  - 'transliteration_map': An array of non-ASCII chars and their transliterated ASCII equivalents.
 *                           It's used in replace_special_chars() to find and replace non-ASCII chars in post and category slugs.
 *                           You can define your own pattern, e.g. to replace "U" with "you" use 'U'=>'you'.
 *                           See /locales/ru_RU/ru-RU.locale.php for details.
 */
$locale_defs['zh-CN'] = array(
		'name' => NT_('Chinese (CN) utf8'),
		'messages' => 'zh_CN',
		'charset' => 'utf-8',
		'datefmt' => 'j.m.Y',
		'timefmt' => 'H:i:s',
		'startofweek' => 1,
	);
?>