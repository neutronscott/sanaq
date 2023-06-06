<?php

//always connected to db... :\
try {
	$dbh = new PDO("sqlite:/home/mute/www/sanaq.db");
} catch(PDOException $e) {
	echo $e->getMessage();
	exit();
}

$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
error_reporting(-1);

function get_slug($slug) {
	global $dbh;

	$sth = $dbh->prepare('SELECT type, data FROM slugs WHERE slug=?');
	$sth->bindParam(1, $slug);
	$nrows = $sth->execute();

	$sth->bindColumn(1, $type, PDO::PARAM_STR);
	$sth->bindColumn(2, $data, PDO::PARAM_LOB);
	if ($sth->fetch(PDO::FETCH_BOUND) === FALSE)
		return FALSE;

	if ($type == 'redirect') {
		header('Status: 302 Found');
		header("Location: $data");
	} else {
		header("Content-Type: $type");
		echo $data;
	}
	return TRUE;
}

function get_next_slug() {
	global $dbh;

	$sth = $dbh->query('SELECT id FROM next WHERE _rowid_=1');
	$sth->bindColumn(1, $id);
	$sth->fetch(PDO::FETCH_BOUND);

	$next = $id + 1;

	$sth = $dbh->prepare('UPDATE next SET id=? WHERE _rowid_=1');
	$sth->bindParam(1, $next, PDO::PARAM_INT);
	$sth->execute();

	return base($id);
}

function insert_data($type, $data, $isfile = FALSE) {
	global $dbh;

	if ($isfile == FALSE) {
		$sth = $dbh->prepare('SELECT type, slug FROM slugs WHERE data=?');
		$sth->bindParam(1, $data);
		$nrows = $sth->execute();
		if ($nrows >= 1) {
			$sth->bindColumn(1, $etype, PDO::PARAM_STR);
			$sth->bindColumn(2, $eslug, PDO::PARAM_STR);
			while ($sth->fetch(PDO::FETCH_BOUND)) {
				if ($etype === $type) {
					return "$eslug";
				}
			}
		}
	}

	$slug = get_next_slug();
	$sth = $dbh->prepare('INSERT INTO slugs (timestamp, ip, slug, type, data) VALUES (?, ?, ?, ?, ?);');

	$now = time();
	$sth->bindParam(1, $now);
	$sth->bindParam(2, $_SERVER['REMOTE_ADDR']);
	$sth->bindParam(3, $slug);
	$sth->bindParam(4, $type);
	if ($isfile)
		$sth->bindParam(5, $data, PDO::PARAM_LOB);
	else
		$sth->bindParam(5, $data);
	$sth->execute();

	return $slug;
}

function handle_file($f) {
	switch ($f['error']) {
	case UPLOAD_ERR_OK:
		// never trust a client to obey a limit !
		if ($f['size'] > 368640) {
			echo "ERROR: file too large [300kB maximum]\n";
			return FALSE;
		}
		$fp = fopen($f['tmp_name'], 'rb');
		return insert_data($f['type'], $fp, TRUE);
		break;
	case UPLOAD_ERR_NO_FILE:
		// not really an error, huh? :)
		break;
	case UPLOAD_ERR_FORM_SIZE:
		echo "ERROR: file too large [300kB maximum]\n";
		break;
	default:
		echo "ERROR with file upload\n";
	}
	return FALSE;
}

function handle_all() {
	$slugs = array();

	// a url
	if (isset($_REQUEST['url']) && strlen($_REQUEST['url']) > 0) {
		$slugs['url'] = insert_data('redirect', $_REQUEST['url']);
	}

	// any other submission is text
	$requests = array_merge($_GET, $_POST); // $_REQUEST may contain cookies..
	foreach ($requests as $k => $v) {
		if ($k == 'url') continue;
		if (strlen($v) > 0)
			$slugs[$k] = insert_data('text/plain', $v);
	}

	// any and all files
	foreach ($_FILES as $name => $farr) {
		if (is_array($farr['error'])) {
			foreach ($farr['error'] as $i => $multi) {
				$keys = array_keys($farr);
				$single = array();
				foreach ($keys as $key)
					$single[$key] = $farr[$key][$i];
				if (($r = handle_file($single)) !== FALSE)
					$slugs[$name . $i] = $r;
			}
		} else {	// single file
			if (($r = handle_file($farr)) !== FALSE)
				$slugs[$name] = $r;
		}
	}

	return $slugs;
}

function base($N, $obase = 62, $ibase = 10) {
	$base64 = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_@";
	$result = '';

	//hrm...
	if ($N == 0)
		return "0";

	//string to integer, rather slow way huh?
	if ($ibase != 10) {
		$tmp = 0;
		for ($i = strlen($N) - 1, $j = 0; $i >= 0; $i--, $j++) {
			$tmp += pow($ibase, $j) * strpos($base64, substr($N, $i, 1));
		}
		$N = $tmp;
	}

	while (($N > 0)) {
		$result .= substr($base64, $N % $obase, 1);
		$N = (int)($N / $obase);
	}
	return strrev($result);
}
?>
