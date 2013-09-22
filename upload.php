<?php


$testfile_md5 = array(
	'testfile1.txt' => 'd41d8cd98f00b204e9800998ecf8427e',
	'testfile2.txt' => '68b329da9893e34099c7d8ad5cb9c940',
	'testfile3.txt' => '3fc6fbc5e4a235f52aa6f98b80e10726',
	'testfile4.txt' => 'db85c608f4c33230627bbabeb3e6b018',
);


print "<pre>\n";
print "_POST: ";
print htmlspecialchars(print_r($_POST, true));
print "\n";
print "_FILES: ";
print htmlspecialchars(print_r($_FILES, true));
print "\n";
print "</pre>\n";


if (isset($_FILES['file'])) {
	$md5 = md5_file($_FILES['file']['tmp_name']);

	if (!isset($testfile_md5[$_FILES['file']['name']])) {
		print "<p style=\"font-size: large; font-weight: bold;\">unknown file, please check MD5 manuali: " . $md5 . "</p>\n";
	} else {
		print "<p style=\"font-size: large; font-weight: bold;\">MD5 sum is " . ($md5 == $testfile_md5[$_FILES['file']['name']] ? " correct " : " <span style=\"color: red\">wrong</span>") . "</p>\n";
		$fp = fopen("status.txt", 'a');
		fwrite($fp, $_POST['navigator']['userAgent'] . "\t" . $_FILES['file']['name'] . "\t" . ($md5 == $testfile_md5[$_FILES['file']['name']] ? "pass" : "fail") . "\n");
		fclose($fp);
	}
} else {
	print "<p style=\"font-size: large; font-weight: bold;\">file missing...</p>\n";
	$fp = fopen("status.txt", 'a');
	fwrite($fp, $_POST['navigator']['userAgent'] . "\tmissing file\t" . "\n");
	fclose($fp);
}

print "memory_get_peak_usage: " . memory_get_peak_usage() . "<br />\n";

