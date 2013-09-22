<html>
<body>
<?php
#print "<pre>\n";
#print "_POST: ";
#print htmlspecialchars(print_r($_POST, true));
#print "\n";
#print "_FILES: ";
#print htmlspecialchars(print_r($_FILES, true));
#print "\n";
#print htmlspecialchars(print_r(getallheaders(), true));
#
#print "</pre>\n";
#<form method="post" action="upload.php" enctype="multipart/form-data">
#<form method="post" action="upload.php">
#<form method="post" enctype="multipart/form-data">

?>
<h5>PHP-Uploader test file:</h5>
<ol>
<li><a href="testfile1.txt">testfile1.txt</a></li>
<li><a href="testfile2.txt">testfile2.txt</a></li>
<li><a href="testfile3.txt">testfile3.txt</a></li>
<li><a href="testfile4.txt">testfile4.txt</a></li>
</ol>

<a href="phpinfo.php">phpinfo</a><br />

<form method="post" action="upload.php" enctype="multipart/form-data">
<script language="JavaScript">
document.writeln('<input type="text" size="100" name="navigator[userAgent]" value="' + escapeHtml(navigator.userAgent) + '" readonly /><br />');
document.writeln('<input type="text" size="100" name="navigator[platform]" value="' + escapeHtml(navigator.platform) + '" readonly /><br />');
document.writeln('<input type="text" size="100" name="navigator[appCodeName]" value="' + escapeHtml(navigator.appCodeName) + '" readonly /><br />');
document.writeln('<input type="text" size="100" name="navigator[appVersion]" value="' + escapeHtml(navigator.appVersion) + '" readonly /><br />');
</script>
<label for="file">Filename:</label><input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html> 



