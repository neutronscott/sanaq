<?php
 $slug = '404';
 if (isset($_GET['slug'])) $slug = $_GET['slug']
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<link rel="author" type="text/plain" href="humans.txt" />
	<link rel="icon" type="image/x-icon" href="favicon.ico" />
	<title>No such file or directory</title>
<style type="text/css">
body {
	background-color: black;
	color: silver;
	font-family: "Courier New","DejaVu Sans Mono",Courier,monospace;
	font-size: 10pt;
}
</style>
</head>
<body>
[guest@san ~]$ cat <?php echo $slug; ?><br>
cat: <?php echo $slug; ?>: No such file or directory<br>
[guest@san ~]$ <span style="background-color: lime;">&nbsp;</span><br>
</body>
</html>
