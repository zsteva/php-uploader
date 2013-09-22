<?php

/*
    PHP Uploader -- solution for large file upload on Apache/PHP
    Copyright (C) 2013 Zeljko Stevanovic <zsteva@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class phpuploader {
	private $uploader_debug = true;
	private $debug_dir = '/tmp/upload';
	private $tmp_dir = '/tmp';

	private $fp = false;
	private $content_type = '';
	private $file_name = '';
	private $field_name = '';
	private $tmpfname = '';
	private $size = 0;
	private $content = '';
	private $boundary = '';

	private $uniq_key = '';

	function __construct() {
		$this->uniq_key = md5(time() . '-' . $_SERVER['REMOTE_ADDR']);

	}

	function get_uniq_key() {
		return $this->uniq_key;
	}

	function detect_content_type() {
		$headers = getallheaders();
		if (isset($headers['Content-Type']) && preg_match('/^multipart\/form-data-alternate; boundary=(.*)$/', $headers['Content-Type'], $matches)) {
			$this->boundary = $matches[1];
			return true;
		}
		return false;
	}

	function append_data($buf) {
		if ($this->fp) {
			$this->size += strlen($buf);
			fwrite($this->fp, $buf);
		} else {
			$this->content .= $buf;
		}
	}

	function finish_data() {
		if ($this->fp) {
			fclose($this->fp);
			$_FILES[''.$this->field_name] = array(
				'name' => $this->file_name,
				'type' => $this->content_type,
				'tmp_name' => $this->tmpfname,
				'error' => 0,
				'size' => $this->size,
			);
			$this->fp = null;
		} else if ($this->field_name != '') {
			if (preg_match("/^(.*?)\[\]$/", $this->field_name, $matches)) {
				if (!is_array($_POST[''.$matches[1]])) {
					$_POST[''.$matches[1]] = array();
				}
				$_POST[''.$matches[1]][] = $this->content;
			} else if (preg_match("/^(.*?)\[(.+)\]$/", $this->field_name, $matches)) {
				if (!is_array($_POST[''.$matches[1]])) {
					$_POST[''.$matches[1]] = array();
				}
				$_POST[''.$matches[1]][''.$matches[2]] = $this->content;
			} else {
				$_POST[''.$this->field_name] = $this->content;
			}
		}

		$this->content = '';
		$this->content_type = '';
		$this->file_name = '';
		$this->field_name = '';
		$this->tmpfname = '';

	}

	function parse_subheader($buf) {
		foreach (explode("\r\n", $buf) as $line) {
			if (preg_match("/^Content-Disposition:\s*(.*)$/", $line, $matches2)) {
				$remaind = $matches2[1];
				while (1) {
					if (preg_match("/^\s*form-data(.*)$/", $remaind, $matches3)) {
						$remaind = $matches3[1];
					} else if (preg_match("/^\s*name=\"([^\"]*)\"(.*)$/", $remaind, $matches3)) {
						$this->field_name = $matches3[1];
						$remaind = $matches3[2];
					} else if (preg_match("/^\s*filename=\"([^\"]*)\"(.*)$/", $remaind, $matches3)) {
						$this->file_name = $matches3[1];
						$remaind = $matches3[2];
					} else {
						break;
					}
					$remaind = preg_replace("/^\s*;\s*/", '', $remaind);
				}
			}
			if (preg_match("/^Content-Type:\s*(.*)$/", $line, $matches2)) {
				$this->content_type = $matches2[1];
			}

		}

		if ($this->file_name != '') {
			$this->tmpfname = tempnam($this->tmp_dir, $this->tmp_file_prefix);
			$this->fp = fopen($this->tmpfname, 'c');
			$this->size = 0;
		}
	}

	function parse_input() {
		if (!$this->detect_content_type()) {
			return;
		}
;
		$buf = "\r\n";
		$fpdump = false;
		if ($this->uploader_debug)
			$fpdump = fopen($this->debug_dir . '/' . $this->uniq_key . '.raw', 'c');

		$rawInput = fopen('php://input', 'r');

		while (1) {
			if (!feof($rawInput)) {
				$buf2 = fread($rawInput, 8192 - strlen($buf));
				$buf .= $buf2;
				if ($this->uploader_debug)
					fwrite($fpdump, $buf2);
				unset($buf2);
			}

			if (preg_match("/^(.*?)\r\n--" . $this->boundary . "\r\n(.*?)\r\n\r\n(.*)$/s", $buf, $matches)) {

				$this->append_data($matches[1]);
				$this->finish_data();

				$this->parse_subheader($matches[2]);

				$buf = $matches[3];
			} else if (preg_match("/^(.*?)\r\n--" . $this->boundary . "--/s", $buf, $matches)) {
				$buf = $matches[1];
				break;
			} else {
				# write only half buffer to file. we dont like to miss next header.
				$len = strlen($buf);
				if ($len >= 8000) {
					$len = 4096;
					$this->append_data(substr($buf, 0, $len));
					$buf = substr($buf, $len);
				}

				# fail safe for malformed input.
				#if (feof($rawInput)) break;
			}
		}

		$this->append_data($buf);
		$this->finish_data();
		
		fclose($rawInput);
		if ($this->uploader_debug)
			fclose($fpdump);
	}
}

set_time_limit(0);
$uploader = new phpuploader();
$uploader->parse_input();


