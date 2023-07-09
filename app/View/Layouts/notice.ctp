<html lang="ko">
<head>
<meta name="viewport" content="width=480" />
<title>Notices</title>
<style type="text/css">
body,div, img {margin:0px;padding:0px}
img {border:0}
</style>
</head>
<body onload="window.scrollTo(0, 1);">
<?php foreach($notices as $notice):?>
<?php echo $notice['Notice']['contents']?>
<br>
<?php endforeach;?>
</body>
</html>