<?php
include('functions.php');
header('Content-type: text/plain');

	$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

	$sth = $dbh->query('SELECT id FROM next ORDER BY id ASC') or die ('select fail');
	$sth->setFetchMode(PDO::FETCH_ASSOC);
	while ($row = $sth->fetch()) {
			printf("next: [%s] -> [%s]\n", $row['id'], base($row['id']));
	}

	$sth = $dbh->query('SELECT * FROM slugs') or die ('select fail');
	$sth->setFetchMode(PDO::FETCH_ASSOC);

	printf("\n" . '"slug","timestamp","ip","type","length","data"' . "\n");
	while ($row = $sth->fetch()) {
		printf('"%s","%s","%s","%s",%d,"%s"'."\n", $row['slug'], strftime("%Y-%m-%d %H:%M", $row['timestamp']),
			$row['ip'], $row['type'], strlen($row['data']),
//			(($row['type'] != 'redirect') && ($row['type'] != 'text/plain')) ? 'blob' : $row['data']);
			($row['type'] != 'redirect') ? 'blob' : $row['data']);
	}
?>

