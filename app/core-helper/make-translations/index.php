<?php
// name: Make translations
// description: Search all translations within project and automatically generate translate file with $lang as language

$lang = 'en';
c\translate::reloadLocate($lang);
$phrases = [];
$phrases_warning = [];
foreach (c\filedata::filelist('app') as $filename => $t) {

	$file = file_get_contents($filename);
	$token = token_get_all($file);
	foreach ($token as $key => $value) {
		if (is_string($value)) continue;
		if ($key < 4) continue;
		if ($value[1] == 't' && $value[0] == T_STRING) {
			if (
				($token[$key - 1][1] == '::' && $token[$key - 2][1] == 'translate' && $token[$key - 3][1] == '\\' && $token[$key - 4][1] == 'c' && $token[$key + 1] == '(')
				or
				($token[$key - 1][1] == '\\' && $token[$key - 2][1] == 'c' && $token[$key + 1] == '(')
			) {
				if (substr($token[$key + 2][1], 0, 1) == "'" or substr($token[$key + 2][1], 0, 1) == '"') {
					$phrase = substr($token[$key + 2][1], 1, -1);
					$phrases[$phrase] = isset(c\translate::$tDict[$phrase]) ? c\translate::$tDict[$phrase] : '';
				} else {
					$phrases_warning[$token[$key + 2][1]] = '';
				}
			}
		} elseif ($value[1] == 'translate' && $token[$key - 1][1] == '\\' && $token[$key - 2][1] == 'c') {
			if (substr($token[$key + 2][1], 0, 1) == "'" or substr($token[$key + 2][1], 0, 1) == '"') {
				$phrase = substr($token[$key + 2][1], 1, -1);
				$phrases[$phrase] = isset(c\translate::$tDict[$phrase]) ? c\translate::$tDict[$phrase] : '';
			} else {
				$phrases_warning[$token[$key + 2][1]] = '';
			}
		}
	}
}
$content = '<?php
namespace c;
translate::$charset=core::UTF8;
';
foreach ($phrases as $original => $translate) {
	$content .= "translate::dictLP('" . $original . "','" . $translate . "');
";
}
file_put_contents('config/translate_autogenerate_' . $lang . '.php', $content);
var_dump($phrases);
var_dump($phrases_warning);
