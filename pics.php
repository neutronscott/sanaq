<?php
include('functions.php');
header('Content-type: text/html');

	$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

	$sth = $dbh->query('SELECT id FROM next ORDER BY id ASC') or die ('select fail');
	$sth->setFetchMode(PDO::FETCH_ASSOC);
	while ($row = $sth->fetch()) {
			printf("next: [%s] -> [%s]<br>\n", $row['id'], base($row['id']));
	}

	$sth = $dbh->query('SELECT * FROM slugs WHERE type LIKE "image/%"') or die ('select fail');
	$sth->setFetchMode(PDO::FETCH_ASSOC);

	printf("<br>\n" . '"slug","timestamp","ip","type","size"' . "<br>\n");
	while ($row = $sth->fetch()) {
		printf('"%s","%s","%s","%s","%s"'."<br>\n", $row['slug'], strftime("%Y-%m-%d %H:%M", $row['timestamp']),
			$row['ip'], $row['type'], strlen($row['data']));
		printf('<img src="http://san.aq/%s"><br>', $row['slug']);
	}
?>
