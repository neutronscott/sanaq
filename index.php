<?php
require_once('functions.php');
if (isset($_GET['slug'])) {
	if (get_slug($_GET['slug']) === FALSE)
		include('error404.php');
	exit();
}
$slugs = handle_all();
if (!empty($slugs)) {
	header('Content-type: text/plain');
	foreach ($slugs as $k => $v)
		printf("%s\thttp://san.aq/%s\n", $k, $v);
	exit();
}
include('index.html');
?>
