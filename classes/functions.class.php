<?php
	class functions {
		public $per_page=12;
		public $start;
		public $pages;
		public $admin_email;
		public $footer_text;
		public $record_count;
		public $connLink;
		public $gstTax;
		public $minimum_qty;
		public $discount_percent;
		public $shipping_charge;
		public $free_shipping_qty;
		
		public function __Construct() {
			$this->connLink = new connection();
			//Set common Meta contents, site title
			$qry = "SELECT * FROM ".PREFIX."settings WHERE id=1";
			$result = mysqli_query($this->connLink->conn,$qry);
			
			if(mysqli_num_rows($result) > 0) {
				$arr = mysqli_fetch_array($result);
				$this->admin_email = stripslashes($arr["admin_email"]);
				$this->site_title = stripslashes($arr["site_title"]);
				$this->meta_keywords = stripslashes($arr["meta_keywords"]);
				$this->meta_desc = stripslashes($arr["meta_desc"]);
				$this->gstTax = stripslashes($arr["gstTax"]);
				$this->minimum_qty = stripslashes($arr["minimum_qty"]);
				$this->discount_percent = stripslashes($arr["discount_percent"]);
				$this->shipping_charge = stripslashes($arr["shipping_charge"]);
				$this->free_shipping_qty = stripslashes($arr["free_shipping_qty"]);
			}
		}
		
		public function paginationData($query,$page) {
			if($page <1)
				$page = 1;
			$start = ($page-1)*$this->per_page;
			$qry = $query." LIMIT ".$start.",".$this->per_page;
			$result = mysqli_query($this->connLink->conn, $qry) or die("Error!");
			$record_count = mysqli_num_rows($result);
			$this->pages = $this->totalPages($query);
			
			if ($record_count > 0) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			//$this->last_insert_id = mysql_insert_id($link_id);
			return $row;			
		}
		
		public function totalPages($query) {
			$result = mysqli_query($this->connLink->conn,$query);
			$record_count = mysqli_num_rows($result);
			return ceil($record_count/$this->per_page);	
		}
		
		public static function pagesBreadcrumb($totalPages, $query_string='') {
			if($_REQUEST["page"] == "" || !isset($_REQUEST["page"]))
				$page=1;
			else {
				$page = $_REQUEST["page"];
				if($page < 1)
					$page =1;
			}
						
			if($page > 1) {
				if($_REQUEST["q"] != "") {
					echo '<li><a class="previous" href="'.self::getPageURL().'?page=1&'.$query_string.'">&#8249;&#8249;</a></li>';
					echo '<li><a class="previous" href="'.self::getPageURL().'?page='.($page-1).'&'.$query_string.'">&#8249;</a></li>';
				} else {
					echo '<li><a class="previous" href="'.self::getPageURL().'?page=1&'.$query_string.'">&#8249;&#8249;</a></li>';
					echo '<li><a class="previous" href="'.self::getPageURL().'?page='.($page-1).'&'.$query_string.'">&#8249;</a></li>';
				}
			}
			
			functions::pagesBreadcrumb1($totalPages, $query_string);
			
			if($page < $totalPages) {
				if($_REQUEST["q"] != "") {
					echo '<li><a class="next" href="'.self::getPageURL().'?page='.($page+1).'&'.$query_string.'">&#8250;</a></li>';
					echo '<li><a class="next" href="'.self::getPageURL().'?page='.($totalPages).'&'.$query_string.'">&#8250;&#8250;</a></li>';
				}
				else
				{
					echo '<li><a class="next" href="'.self::getPageURL().'?page='.($page+1).'&'.$query_string.'">&#8250;</a></li>';
					echo '<li><a class="next" href="'.self::getPageURL().'?page='.($totalPages).'&'.$query_string.'">&#8250;&#8250;</a></li>';
				}
			}
		}
		
		public static function pagesBreadcrumb1($totalPages, $query_string='') {
			if($_REQUEST["page"] == "" || !isset($_REQUEST["page"]))
				$page=1;
			else {
				$page = $_REQUEST["page"];
				if($page < 1)
					$page =1;
			}
			
			$upperPageLimit = $page-5;			
			$lowerPageLimit = $page+5;
			
			if($upperPageLimit <=1) {
				$startPage = 1;
				$lowerPageLimit = 10;
			} else
				$startPage = $upperPageLimit;
			
			if($lowerPageLimit >=$totalPages) {
				$lowerPageLimit = $totalPages;
			}
			
			for($i=$startPage; $i<=$lowerPageLimit; $i++) {
				if($i==$page)
					echo '<li class="active"><a href="'.self::getPageURL().'?page='.($i).'&'.$query_string.'"> '.$i.'</a></li>';
				else
					echo '<li><a href="'.self::getPageURL().'?page='.($i).'&'.$query_string.'"> '.$i.'</a></li>';
			}			
		}
		
		public static function checkUserSession() {
			if($_SESSION["uSeRiD"] == "" || !isset($_SESSION["uSeRiD"])) {
				return false;
			} else {
				return true;
			}
		}
		
		public static function isValidEmail($email) {
			$email = strtolower($email);
			$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
			
			if(preg_match($pattern, $email)) {
				return true;
			} else {
				return false;
			}
		}
		
		public static function space2nbsp($str) {
			return $str = str_replace(" ", '&nbsp;', $str);	
		}
		
		public static function safeString($str) {
			$link = new connection();
			$str = trim($str);
			$str = strip_tags($str,"<br />");
			$pat = array("\r\n", "\n\r", "\n", "\r"); // remove returns
			$str = str_replace($pat, '<br />', $str);
			$str = htmlentities($str, ENT_QUOTES, 'UTF-8'); // convert funky chars to html entities
			$str = str_replace(array("&lt;br /&gt;", "&lt;br&gt;", "&lt;br&nbsp;/&gt;"), array("<br />", "<br>", "<br>"), $str);
			$str = mysqli_real_escape_string($link->conn,$str);
			$str = stripslashes($str);
			return $str;
			//return addslashes($str);
		}
		
		public static function deSerializeString($str) {
			return $str = stripslashes(htmlspecialchars_decode(nl2br($str)));
		}
		
		public static function br2nl($str) {
			return $str = str_replace("<br />","\n", $str);
			return $str = str_replace("<br>","\n", $str);
		}
				
		public static function getEXT($str) {
			$t="";
			$string =$str;
			$tok = strtok($string,".");

			while($tok) {
				$t=$tok;
				$tok = strtok(".");
			}
			 return $t;
		}
		
		// This Function Cleans Text Strings. Used for URL Re-Writing
		public static function clean_url($str, $replace=array(), $delimiter='-') {
			setlocale(LC_ALL, 'UTF8');
			if( !empty($replace) ) {
				$str = str_replace((array)$replace, ' ', $str);
			}
		
			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
			return $clean;
		}
		
		public static function setCombo($tablename,$id,$field_name,$select_value,$where_clause='') {
			$link = new connection();
			echo $qry = "SELECT * FROM ".$tablename." ".$where_clause;
			$rs = mysqli_query($link->conn, $qry);
		
			while($row = mysqli_fetch_object($rs)) {
				if($select_value == $row->$id)
					$str = $str."<option selected=selected value='".$row->$id."'>".stripslashes($row->$field_name)."</option>";
				else
					$str = $str."<option value='".$row->$id."'>".stripslashes($row->$field_name)."</option>";
			}
			return $str;
		}
		
		

		public function getValue($tablename,$fieldname,$comparefield,$dispfield) {
			$qry = "SELECT $dispfield FROM $tablename WHERE $fieldname='$comparefield'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			$arr = mysqli_fetch_array($rs);
			
			return $arr[$dispfield];
		}

		public function getValueWhereCond($tablename,$dispfield,$whereClause) {
			$qry = "SELECT $dispfield FROM $tablename $whereClause";
			$rs = mysqli_query($this->connLink->conn,$qry);
			$arr = mysqli_fetch_array($rs);
			
			return $arr[$dispfield];
		}

		public static function getValueString($tablename,$fieldname,$comparefield,$dispfield) {
			$qry = "Select $dispfield from $tablename where `$fieldname`='$comparefield'";
			$rs = mysql_query($qry);
			$arr = mysql_fetch_array($rs);
			
			return $arr[$dispfield];
		}
		
		public static function getTime($time) {
			$periods = array(VAR_SECOND, VAR_MINUTE, VAR_HOUR, VAR_DAY, VAR_WEEK, VAR_MONTH, VAR_YEAR, VAR_DECADE);
			$lengths = array("60","60","24","7","4.35","12","10");
			
			$now = time();
			
			$difference     = $now - $time;
			$tense         = VAR_AGO;
			
			for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
				$difference /= $lengths[$j];
			}
			
			$difference = round($difference);
			
			if($difference != 1) {
				$periods[$j].= "s";
			}
			
			return "$difference $periods[$j] ".VAR_AGO;
		}
		
		public static function getPageURL() {
			return parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
		}
		
		public static function getPageName() {
			$currentFile = $_SERVER["PHP_SELF"];
			$parts = Explode('/', $currentFile);
			return $parts[count($parts) - 1];
		}
		
		public static function chkFileExtention($file) {
			if($file != "") {
				$file_extension = self::getEXT($file);
	
				if( strtoupper($file_extension) == "JPG" || strtoupper($file_extension) == "JPEG" || strtoupper($file_extension) == "GIF" || strtoupper($file_extension) == "PNG") {
					return true;
				} else {
					return false;
				}
			}
		}
		
		public static function generateEncryptedPassword($pass) {
			return sha1(md5($pass));
		}
		
		public static function filterName($name, $filter = "[^A-Za-z0-9]") {
			return preg_match("~" . $filter . "~iU", $name) ? false : true;
		}

		// Random Alphanumeric Key Generator
		public static function randomPrefix($length) {
			$random= "";
			srand((double)microtime()*1000000);
			$data = "AbcDE123IJKLMN67QRSTUVWXYZ";
			$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
			$data .= "0FGH45OP89";
			
			for($i = 0; $i < $length; $i++) {
				$random .= substr($data, (rand()%(strlen($data))), 1);
			}
			return $random;
		}
		
		public static function strleft($s1, $s2) {
			return substr($s1, 0, strpos($s1, $s2));
		}
		
		public static function selfURL() {
			$s = empty($_SERVER["HTTPS"]) ? '' : (($_SERVER["HTTPS"] == "on") ? "s" : ""); 
			$protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); 
			$protocol = $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI']; 
			
			$p = explode("?",$protocol);
			return $p[0];
		}
		
		public static function sort_alpha() {
			$tmpva="";
			$current_url = $_SERVER['PHP_SELF'];
		
			foreach($_GET as $V=>$K) {
				if($V!="srt")
					$tmpva.="&".$V."=".$K;
			}
			
			$str = "<a class='pink-link' href='".self::getPageName()."'>All</a>&nbsp;";
			
			for($i=65;$i<=90;$i++) {
				$str.= "<a class='pink-link' href='$current_url?srt=".chr($i)."'>".chr($i)."</a>&nbsp;";
			}
			
			return $str;
		}
		
		public static function validateCatalogFile($file) {
			if($file != "") {
				$file_extension = $this->getEXT($file);
	
				if( strtoupper($file_extension) == "PDF" || strtoupper($file_extension) == "XLS" || strtoupper($file_extension) == "XLSX" || strtoupper($file_extension) == "DOC" || strtoupper($file_extension) == "DOCX" || strtoupper($file_extension) == "PPT" || strtoupper($file_extension) == "PPTX") {
					return true;
				} else {
					return false;
				}
			}
		}

		//*************FLUSH MAIL SENDING FUNCTION STARTS*************//
		public static function send_flush_mail($to, $from, $subject, $message) {
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
			
			// Create email headers
			$headers .= 'From: '.$from."\r\n".
						'Reply-To: '.$from."\r\n" .
						'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);
		}
		//*************FLUSH MAIL SENDING FUNCTION ENDS*************//
	}

?>