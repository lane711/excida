<?php

$installed_languages = array(
	'english' => 'name',
	'french' => 'name2',
	'german' => 'name3',
	'italian' => 'name4',
	'russian' => 'name5',
	'spanish' => 'name6',
	'bulgarian' => 'name7',
	'croatian' => 'name8',
	'georgian' => 'name9',
	'greek' => 'name10',
	'portuguese' => 'name11',
	'swedish' => 'name12',
	'vietnamese' => 'name13'
);

// Language codes
// Map the languages above with the ISO language code to use
$iso_language_codes = array(
    'english'    => 'en',
    'french'     => 'fr',
    'german'     => 'de',
    'italian'    => 'it',
    'russian'    => 'ru',
    'spanish'    => 'es',
    'bulgarian'  => 'bg',
    'croatian'   => 'hr',
    'georgian'   => 'ka',
    'greek'      => 'el',
    'portuguese' => 'pt',
    'swedish'    => 'sv',
    'vietnamese' => 'vi',
);

// Do not change anything below this line
if ($cookie_language != '')
{
    switch ($cookie_language) {
    // "svenska" name is deprecated. Use "swedish" instead
    case 'svenska':
        $cookie_language = 'swedish';
        break;
    }
    // If language is not available, fallback to default
    if (empty($installed_languages[$cookie_language])) {
        reset($installed_languages);
        $cookie_language = key($installed_languages);
        $language_in = current($installed_languages);
    } else {
	   $language_in = $installed_languages[$cookie_language];
    }
	return $language_in;
}
else
{
	$language_in = 'name';
	return $language_in;
}

?>