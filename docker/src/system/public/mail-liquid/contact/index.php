<?php
//テンプレート読み込み
$page_tpl = file_get_contents(dirname(__FILE__).'/template.html');
//プリンター読み込み
require '../class/contact/printer.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<title>無題ドキュメント</title>
</head>
<body>
<form action="confirm.php" method="post">
	<?php echo $html; ?>
</form>
</body>
</html>
