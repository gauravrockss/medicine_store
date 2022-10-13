<?php	
	include_once($class_dir."functions.class.php");
	include_once($class_dir.'phpmailer/PHPMailerAutoload.php');
	
	class contentInterface extends functions {
		function getStaticPageContents($id) {
			$qry = "SELECT * FROM ".PREFIX."staticpages WHERE id=$id";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchBanners() {				
			$qry = "SELECT * FROM ".PREFIX."banners ORDER BY id DESC";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			return $row;
		}

		public function search($name) {
			$qry = "SELECT * FROM ".PREFIX."products WHERE productname LIKE '%$name%' LIMIT 5";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$categoryName = $this->getValueWhereCond(PREFIX."categories","categoryName"," WHERE id='".$obj->categoryID."'");
					$obj->url = SITEURL . 'product-details/' . functions::clean_url($categoryName) . '/' . $obj->id . '/' . functions::clean_url($obj->productname) . '.html';
					$obj->category = $categoryName;
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function checkUser($oauth_provider,$oauth_uid,$fname,$lname,$email,$gender,$locale,$picture) {		
			$prev_query = mysqli_query($this->connLink->conn,"SELECT * FROM ".PREFIX."register WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysql_error($this->connect));
			
			if(mysqli_num_rows($prev_query)>0){
				$qry = "UPDATE ".PREFIX."register SET 
						oauth_provider = '".$oauth_provider."', 
						oauth_uid = '".$oauth_uid."', 
						firstName = '".$fname."', 
						lastName = '".$lname."', 
						email = '".$email."', 
						gender = '".$gender."',
						WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'";
				$update = mysqli_query($this->connLink->conn,$qry);
			}else{
				$qry = "INSERT INTO ".PREFIX."register SET 
						oauth_provider = '".$oauth_provider."', 
						oauth_uid = '".$oauth_uid."', 
						firstName = '".$fname."', 
						lastName = '".$lname."', 
						email = '".$email."', 
						gender = '".$gender."', 
						age = '',
						regDate = '".time()."', 
						status='1',
						ipAddress='".$_SERVER['REMOTE_ADDR']."'";
				$insert = mysqli_query($this->connLink->conn,$qry);
			}
		
			$result = mysqli_query($this->connLink->conn,"SELECT * FROM ".PREFIX."register WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'");
			$record_count = mysqli_num_rows($result);
			
			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
				
		public function addContactRequest($inquiryType) {
			$name = functions::safeString($_REQUEST["txtFullName"]);
			$email = functions::safeString($_REQUEST["txtEmail"]);
			$subject = functions::safeString($_REQUEST["txtSubject"]);
			$message = functions::safeString($_REQUEST["taComment"]);
			
			$qry = "INSERT ".PREFIX."contacts SET
					fullName='$name',
					subject='$subject',
					email='$email',
					message='$message',
					ipAddress='".$_SERVER['REMOTE_ADDR']."',
					inquiryType='$inquiryType'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if($rs) {
				//Send confirmation email
				//Read from Template file
				$myFile = "templates/contact_email_template.txt";
				$fh = fopen($myFile, 'r');
				$data = fread($fh,filesize($myFile));
				fclose($fh);
				
				$data = str_replace("{siteURL}",SITEURL,$data);
				$data = str_replace("{copyRightsText}",COPYRIGHTS_TEXT,$data);
				$data = str_replace("{email}",$email,$data);
				$data = str_replace("{websiteName}",WEBSITE_NAME,$data);
				$data = str_replace("{full_name}",$name,$data);
				$data = str_replace("{subject}",$subject,$data);
				$data = str_replace("{message}",$message,$data);				
				//echo $data;
				//exit;
				
				/*$subject = "Contact Request Received";
				//Create a new PHPMailer instance
				$mail = new PHPMailer;
				$mail->CharSet = 'UTF-8';
				//Set who the message is to be sent from
				$mail->setFrom($this->admin_email, OUTGOING_EMAIL);
				//Set an alternative reply-to address
				$mail->addReplyTo($this->admin_email, OUTGOING_EMAIL);
				//Set who the message is to be sent to
				$mail->addAddress($email, OUTGOING_EMAIL);
				//Set the subject line
				$mail->Subject = $subject;
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($data);
				//Replace the plain text body with one created manually
				$mail->AltBody = 'This is a plain-text message body';
				
				//send the message, check for errors
				if (!$mail->send()) {
					echo "Mailer Error: " . $mail->ErrorInfo;
				}*/
				
				functions::send_flush_mail($email, $this->admin_email, $subject, $data);
				return "success";
			}
			else
			{
				return "dbErr";
			}
		}
		
		public function fetchBrands($array='') {
			$qry = "SELECT * FROM ".PREFIX."brands";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " WHERE id='".functions::safeString($array["id"])."'";
				}
			}
			$qry.= " ORDER BY brandName";
			
			if(!empty($array)) {
				if(array_key_exists("limit",$array)) {
					$qry .= " LIMIT ".functions::safeString($array["limit"])."";
				}
			}
			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchCategoriesHomepage($array=NULL) {
			$qry = "SELECT *, c.id as id, COUNT(p.id) as totalCount FROM ".PREFIX."categories c 
					LEFT JOIN ".PREFIX."products p ON c.id=p.categoryID
					WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND c.id='".functions::safeString($array["id"])."'";
				}
			}
			$qry.= " GROUP BY c.id ORDER BY categoryName ASC";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			return $row;
		}
		
		
		public function fetchProductCategoryByID($productID) {			
			$qry = "SELECT * FROM ".PREFIX."categories WHERE id='$productID'";			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		
		public function fetchCategories($array='') {
			$qry = "SELECT * FROM ".PREFIX."categories WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND id='".functions::safeString($array["id"])."'";
				}
			}
			$qry.= " ORDER BY categoryName";
			
			if(!empty($array)) {
				if(array_key_exists("limit",$array)) {
					$qry .= " LIMIT ".functions::safeString($array["limit"])."";
				}
			}
			//echo $qry;
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}

		public function fetchAttributes() {
			$qry = "SELECT * FROM ".PREFIX."attributes";			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);
			$row = [];

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}

		public function getOptionsByName($name) {
			$all_attributes = $this->fetchAttributes();
			foreach($all_attributes as $value) {
				if($value->name == $name) {
					return $value;
				}
			}
		}
		
		public function fetchProductsRecommended() {
			$qry = "SELECT * FROM ".PREFIX."products ORDER BY rand()";			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function	fetchProducts($array='') {
			if(isset($_GET["page"]) && functions::safeString($_GET["page"]) != "" && is_numeric($_GET["page"]))
				$page = functions::safeString($_GET["page"]);
			else
				$page =1;
				
			$qry = "SELECT * FROM ".PREFIX."products p WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND p.id='".functions::safeString($array["id"])."'";
				}
				if(array_key_exists("categoryID",$array)) {
					$qry .= " AND p.categoryID='".functions::safeString($array["categoryID"])."'";
				}
				
				if(array_key_exists("search_string",$array)) {
					$search_string = functions::safeString($array["search_string"]);
					
					$qry .= " AND (
									productname LIKE '%$search_string%'
									OR productPrice LIKE '%$search_string%'
									OR productDescription LIKE '%$search_string%'
									)";
				}
			}
			//echo $qry;
			$this->totalPages = functions::totalPages($qry);
			return functions::paginationData($qry,$page);
		}
		
		public function	fetchProductsHomePage($array=NULL) {
			$qry = "SELECT * FROM ".PREFIX."products WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("catID",$array)) {
					$qry .= " AND categoryID='".functions::safeString($array["catID"])."'";
				}
			}
			
			$qry .= " LIMIT 500";
			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchProductDetailsByID($productID) {			
			$qry = "SELECT * FROM ".PREFIX."products WHERE id='$productID'";			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function checkCart() {
			$qry = "SELECT * FROM ".PREFIX."cart WHERE sessionid='".functions::safeString($_SESSION["sessionid"])."'";  
			$result = mysqli_query($this->connLink->conn, $qry);
			return $record_count = mysqli_num_rows($result);
		}
		
		//********************* CART STARTS *********************//
		public function checkProductPartInCart( $productID, $attribute) {
			$attribute = $this->getAttribute($productID, $attribute)['attribute'];
			$qry = "SELECT * FROM ".PREFIX."cart_details WHERE pid='$productID' AND attribute='$attribute' AND sessionid='".$_SESSION["sessionid"]."'";
			$rs = mysqli_query($this->connLink->conn,$qry);
		
			if(mysqli_num_rows($rs) > 0) {
				$result = mysqli_fetch_assoc($rs);
				return $result['id'];
			} else {
				return 0;
			}
		}
		
		public function updateCartDetails($productDetailID, $productID, $quantity) {
			$qry = "UPDATE ".PREFIX."cart_details SET 
					qty='$quantity'
					WHERE pid='$productID' 
					AND sessionid='".$_SESSION["sessionid"]."'
					AND proddetid='".$productDetailID."'";
			mysqli_query($this->connLink->conn,$qry);
		}

		public function getAttribute($productID, $attribute) {
			$productDetails = $this->fetchProductDetailsByID($productID, $productDetailID);
			$productDetails[0]->attributes = json_decode($productDetails[0]->attributes, true);
			if(!$productDetails[0]->attributes) $productDetails[0]->attributes = [];
			if($attribute) {
				foreach($productDetails[0]->attributes as $value) {
					if($attribute == $value['name'])
						$price = $value['price'];
				}
				$attribute = explode(',', $attribute);
				$temp = array();
				foreach(json_decode($productDetails[0]->attributes_list) as $key => $value) {
					$temp[$value] = $attribute[$key];
				}
				$attribute = json_encode($temp);
			} else {
				$attribute = json_encode([]);
			}
			if(!$price) $price = $productDetails[0]->productPrice;

			return ['price' => $price, 'attribute' => $attribute];
		}
		
		public function insertCartDetails($productID, $quantity, $attribute) {
			$data = $this->getAttribute($productID, $attribute);
			$price = $data['price'];
			$attribute = $data['attribute'];
			$qry = "INSERT INTO ".PREFIX."cart_details SET 
					pid='$productID',
					sessionid='".$_SESSION["sessionid"]."',
					price='".$price."',
					qty='$quantity',
					attribute='$attribute'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if($rs) return "succ"; else return "dbErr";
		}

		private function getKey($match, $array) {

		}
		
		public function fetchProductPartsByID($productID, $productDetailID='') {
			$qry = "SELECT * FROM ".PREFIX."product_details WHERE pid='$productID'";
			
			if($productDetailID != "")
				$qry .= " AND id='$productDetailID'";

			$result = mysqli_query($this->connLink->conn,$qry);
			$record_count = mysqli_num_rows($result);
			
			if($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function removeProductFromCart($pid) {
			$qry = "DELETE FROM ".PREFIX."cart_details WHERE id='$pid'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if($rs)
				return "succ";
			else
				return "dbErr";
		}
		
		public function applyCoupon() {
			$couponCode = functions::safeString($_POST["txtCouponCode"]);
			
			//Check coupon and it's availability
			$qry = "SELECT * FROM ".PREFIX."coupons WHERE couponCode='$couponCode' AND DATEDIFF(FROM_UNIXTIME(expiryDate),NOW()) > 0";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if(mysqli_num_rows($rs) > 0) {
				$arr = mysqli_fetch_array($rs);
				
				//Update Cart with coupon code and discount percent
				$qryUpd = "UPDATE ".PREFIX."cart SET 
							couponCode='$couponCode', 
							discountPercent='".$arr["discountPercent"]."'
							WHERE sessionid='".$_SESSION["sessionid"]."'";
				$rsUpd = mysqli_query($this->connLink->conn,$qryUpd);
				
				if($rsUpd) return "success"; else return "dbError";
			} else {
				return "coupon_not_found";
			}
		}
		
		public function removeCoupon() {
			$qryUpd = "UPDATE ".PREFIX."cart SET 
						couponCode='', 
						discountPercent='0'
						WHERE sessionid='".$_SESSION["sessionid"]."'";
			$rsUpd = mysqli_query($this->connLink->conn,$qryUpd);
			
			if($rsUpd) return "success"; else return "dbError";
		}
		//********************* CART ENDS *********************//
		
		public function fetchCartDetails() {
			$qry = "SELECT productname, categoryID, imageName1, cd.id as id, attribute, cd.pid, cd.price, cd.qty, (cd.qty*cd.price) as total
					FROM ".PREFIX."products p
					INNER JOIN ".PREFIX."cart_details cd ON p.id=cd.pid
					WHERE sessionid='".$_SESSION["sessionid"]."'";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			} else {
				$row = [];
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchCartCustomerDetails() {
			$qry = "SELECT * FROM ".PREFIX."cart WHERE sessionid='".$_SESSION["sessionid"]."'";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function updateCartQuantity($id, $quantity) {
			$qry = "UPDATE ".PREFIX."cart_details SET 
					qty='".$quantity."' WHERE id='$id'";
			$result = mysqli_query($this->connLink->conn, $qry);

			if($result) return "succ"; else return "dbErr";
		}
		
		public function fetchUserDetailsByID($userID) {
			$qry = "SELECT * FROM ".PREFIX."register WHERE id='$userID'";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		//********************* CHECKOUT STARTS *********************//
		public function updateCartWithUserID() {
			$userID = functions::safeString($_SESSION["uSeRiD"]);
			$qryUpdate = "UPDATE ".PREFIX."cart SET userid=".$userID." WHERE sessionid='".$_SESSION["sessionid"]."'";
			$result = mysqli_query($this->connLink->conn, $qryUpdate);
			
			if($result) {
				$qry = "SELECT * FROM ".PREFIX."register WHERE id='".$userID."'";

				$result = mysqli_query($this->connLink->conn, $qry);
				$record_count = mysqli_num_rows($result);

				if ($record_count) {
					while ($obj = mysqli_fetch_object($result)) {
						$row[] = $obj;
					}
				}
				$this->record_count   = $record_count;
				return $row;
			}
		}
		
		public function placeOrder() {
			if(functions::safeString($_SESSION["uSeRiD"]) != "") {
				$userID = functions::safeString($_SESSION["uSeRiD"]);
			} else {
				$userID = 0;
			}
			
			$txtBillFirstName = functions::safeString($_REQUEST["txtFirstName"]);
			$txtBillLastName = functions::safeString($_REQUEST["txtLastName"]);
			$txtBillAddress = functions::safeString($_REQUEST["txtAddress"]);
			$txtBillCity = functions::safeString($_REQUEST["txtCity"]);
			$txtBillCountry = functions::safeString($_REQUEST["txtCountry"]);
			$txtBillZipCode = functions::safeString($_REQUEST["txtPostcode"]);
			$txtBillPhone = functions::safeString($_REQUEST["txtPhone"]);
			$txtBillEmail = functions::safeString($_REQUEST["txtEmail"]);
			$order_note = functions::safeString($_REQUEST["txtOrderNote"]);
			
			//generating invoice number
			$invoice = date("m/d/Y")."/".functions::randomPrefix(10);
						
			// Updating Billing and Shipping address in cart table
			$qryUpdCart = "UPDATE ".PREFIX."cart SET order_confirm='yes',
							userID='$userID',
							date_received='".date("Y-m-d")."',
							order_status='pending',
							bill_first_name='".$txtBillFirstName."',
							bill_last_name='".$txtBillLastName."',
							bill_address='".$txtBillAddress."',
							bill_city='".$txtBillCity."',
							bill_state='".$txtBillState."',
							bill_country='".$txtBillCountry."',
							bill_zipcode='".$txtBillZipCode."',
							bill_phone='".$txtBillPhone."',
							bill_email='".$txtBillEmail."',
							invoice_no='".$invoice."',
							order_note='$order_note'
							WHERE sessionid='".$_SESSION["sessionid"]."'";
			$result = mysqli_query($this->connLink->conn, $qryUpdCart);
			
			if($result) {
				$this->confirmOrder($_SESSION["sessionid"]);
				return "succ";
			} else {
				return $dbErr;
			}
		}
		
		public function confirmOrder($sessionid) { // When customer pays via PAYPAL and IPN is called
			// Updating Billing and Shipping address in cart table
			//generating invoice numbe
			$discountAmount=0;
			$qryUpdCart = "UPDATE ".PREFIX."cart SET 
							order_confirm='yes',
							date_received='".date("Y-m-d")."',
							order_status='confirm'
							WHERE sessionid='$sessionid'";
			$result = mysqli_query($this->connLink->conn, $qryUpdCart);
			
			if($result) {
				$totalQtyInCart = $this->getValueWhereCond(PREFIX.'cart_details',"SUM(qty)"," WHERE sessionid='".functions::safeString($_SESSION["sessionid"])."'");
	
				if($totalQtyInCart >= $this->minimum_qty) {
					$bulk_discount = $this->discount_percent;
				} else {
					$bulk_discount = 0;
				}
				
				//Shipping Charge
				if($totalQtyInCart >= $this->free_shipping_qty) {
					$shipping_charge = 0;
				} else {
					$shipping_charge = $this->shipping_charge;
				}
				
				//Fetch Cart Items
				$cartCustomerDetails = $this->fetchCartCustomerDetails();
				$listCartDetails = $this->fetchCartDetails();
				
				if(count($listCartDetails) > 0) { 
					$ctr=0;				
					$grandTotal=0;
					
					for($i=0; $i<count($listCartDetails); $i++) {
						$grandTotal = ($grandTotal + $listCartDetails[$i]->total);
						if(($ctr%2) == 0)
							$bgcolor = "#f1f1f1";
						else
							$bgcolor = "#D3E5FA";
						
						$cart_details .='<tr height="40" style="background:'.$bgcolor.'">                	
											<td align="left">'.$listCartDetails[$i]->productname.'</td>
											<td class="invert">' ;
						foreach(json_decode($listCartDetails[$i]->attribute) as $attr_name => $attr_value)  {
							$cart_details .= '<strong>' . $attr_name . '</strong>: ' . $attr_value . '<br>';
						}
                        $cart_details .= '</td>
											<td align="center">'.$listCartDetails[$i]->qty.'</td>
											<td align="right">
												'.CURRENCY_SIGN.' '.number_format($listCartDetails[$i]->price,2);
												$cart_details .= '
											</td>
											<td align="right">
												'.CURRENCY_SIGN.' '.number_format($listCartDetails[$i]->total,2);
												$cart_details .= '
											</td>
										</tr>';
						$ctr++;
					}
					
					$bulk_discount_amount = ($grandTotal*($bulk_discount)/100);
					$grandTotal = ($grandTotal-$bulk_discount_amount);
                    
                    $cart_details .= '<tr>
                    	<th colspan="4" style="text-align:right">Bulk Discount</th>
                        <th style="text-align:right">'.CURRENCY_SIGN.$bulk_discount_amount.'</th>
                    </tr>';
					
					if($cartCustomerDetails[0]->couponCode != "") { 
						$discountAmount = (($grandTotal*$cartCustomerDetails[0]->discountPercent)/100);
						
						$cart_details .= '<tr>
                                            <th colspan="4" style="text-align:right">
                                                Coupon Applied: '.$cartCustomerDetails[0]->couponCode.'['.$cartCustomerDetails[0]->discountPercent.'% ] <br>
                                            </th>
                                            <th style="text-align:right">'.CURRENCY_SIGN.' '.$discountAmount.'</th>
                                        </tr>';
					}
					
					$grandTotal = ($grandTotal-$discountAmount);
                    $taxAmount = (($grandTotal*$this->gstTax)/100);
                    $grandTotal = ($grandTotal+$taxAmount);
					
					$cart_details .= '<tr>
                                        <th colspan="4" style="text-align:right">Tax @'.$this->gstTax.'%</th>
                                        <th style="text-align:right">'.CURRENCY_SIGN.$taxAmount.'</th>
                                    </tr>';
					
					$grandTotal = ($grandTotal+$shipping_charge);
					
					$cart_details .= '<tr>
                                        <th colspan="4" style="text-align:right">Shipping</th>
                                        <th style="text-align:right">'.CURRENCY_SIGN.$shipping_charge.'</th>
                                    </tr>';
					
					$cart_details .='<tr>
						<td colspan="4" style="text-align:right">Grand Total: </td>
						<td style="text-align:right">
							'.CURRENCY_SIGN.' '.$grandTotal.'
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>';
					// Seinding mail to customer
	
					//Read from Template file
					$myFile = "templates/invoice_template.txt";
					$fh = fopen($myFile, 'r');
					$data = fread($fh,filesize($myFile));
					fclose($fh);
				
					$data = str_replace("{siteURL}",SITEURL,$data);
					$data = str_replace("{copyRightsText}",COPYRIGHTS_TEXT,$data);
					$data = str_replace("{full_name}",$cartCustomerDetails[0]->bill_first_name." ".$cartCustomerDetails[0]->bill_last_name,$data);
					$data = str_replace("{invoice_no}",$cartCustomerDetails[0]->invoice_no,$data);
					$data = str_replace("{cart_details}",$cart_details,$data);
					$data = str_replace("{websiteName}",WEBSITE_NAME,$data);
					$data = str_replace("{payment_method}",'Paypal',$data);		
					
					//Billing
					$data = str_replace("{txtBillFirstName}",$cartCustomerDetails[0]->bill_first_name,$data);
					$data = str_replace("{txtBillLastName}",$cartCustomerDetails[0]->bill_last_name,$data);
					$data = str_replace("{txtBillAddress}",$cartCustomerDetails[0]->bill_address,$data);
					$data = str_replace("{txtBillCity}",$cartCustomerDetails[0]->bill_city,$data);
					$data = str_replace("{txtBillState}",$cartCustomerDetails[0]->bill_state,$data);			
					$data = str_replace("{txtBillCountry}",$cartCustomerDetails[0]->bill_country,$data);
					$data = str_replace("{txtBillZipCode}",$cartCustomerDetails[0]->bill_zipcode,$data);
					$data = str_replace("{txtBillEmail}",$cartCustomerDetails[0]->bill_email,$data);
					$data = str_replace("{txtBillPhone}",$cartCustomerDetails[0]->bill_phone,$data);
					
					//echo $data;
					//exit;
				
					//Send Email									
					$subject = "Your order #".$cartCustomerDetails[0]->invoice_no." at ".WEBSITE_NAME." has been placed";
					$mail = new PHPMailer;
					$mail->CharSet = 'UTF-8';
					$mail->setFrom(OUTGOING_EMAIL, OUTGOING_EMAIL_REGISTER);
					$mail->addReplyTo(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addAddress($cartCustomerDetails[0]->bill_email, OUTGOING_EMAIL);
					$mail->Subject = $subject;
					$mail->msgHTML($data);
					
					//send the message, check for errors
					if (!$mail->send()) {
						echo "Mailer Error: " . $mail->ErrorInfo;
					}
					
					//Send Email Copy To Admin
					$subject = "Copy Of Order: Your order #".$invoice." at ".WEBSITE_NAME." has been placed";
					$mail = new PHPMailer;
					//$mail->SMTPDebug = 3;                               
					$mail->CharSet = 'UTF-8';
					$mail->setFrom(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addReplyTo(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addAddress($this->admin_email, OUTGOING_EMAIL);
					$mail->Subject = $subject;
					$mail->msgHTML($data);
					
					//send the message, check for errors
					if (!$mail->send()) {
						echo "Mailer Error: " . $mail->ErrorInfo;
					}
				}
			}
		}
		//********************* CHECKOUT ENDS *********************//
		
		public function fetchCustomerOrders($array='') {
			$qry = "SELECT c.id, c.invoice_no,c.date_received, c.order_status, sum(cd.price) as cartTotal FROM ".PREFIX."cart c
					INNER JOIN ".PREFIX."cart_details cd ON c.sessionid=cd.sessionid
					WHERE order_confirm='yes'";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND c.id='".functions::safeString($array["id"])."'";
				}
				if(array_key_exists("userID",$array)) {
					$qry .= " AND c.userID='".functions::safeString($array["userID"])."'";
				}
			}
			
			$qry .= " GROUP BY c.sessionid ORDER BY c.id DESC";
			
			if(!empty($array)) {
				if(array_key_exists("limit",$array)) {
					$qry .= " LIMIT ".functions::safeString($array["limit"]);
				}
			}
			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchCustmerOrderCartItems($array='') {
			$qry = "SELECT productName, p.imageName, cd.id as id, cd.price, cd.qty, (cd.qty*cd.price) as total, bill_first_name, p.imageName
					FROM ".PREFIX."products p
					INNER JOIN ".PREFIX."cart_details cd ON p.id=cd.pid
					INNER JOIN ".PREFIX."cart c ON c.sessionid=cd.sessionid
					WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("orderID",$array)) {
					$qry .= " AND c.id='".functions::safeString($array["orderID"])."'";
				}
				if(array_key_exists("userID",$array)) {
					$qry .= " AND c.userID='".functions::safeString($array["userID"])."'";
				}
			}
			
			$qry .= " ORDER BY c.id DESC";
			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchCustomerOrderCartDetails() {
			$qry = "SELECT * FROM ".PREFIX."cart WHERE sessionid='".functions::safeString($_SESSION["sessionid"])."'";
			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function updateCart() {
			$bill_first_name = functions::safeString($_POST["txtFirstName"]);
			$bill_last_name = functions::safeString($_POST["txtLastName"]);
			$bill_email = functions::safeString($_POST["txtEmail"]);
			$bill_company_name = functions::safeString($_POST["txtCompanyName"]);
			$bill_address = functions::safeString($_POST["txtAddress"]);
			$bill_country = functions::safeString($_POST["txtCountry"]);
			$bill_city = functions::safeString($_POST["txtCity"]);
			$bill_zipcode = functions::safeString($_POST["txtPostcode"]);
			$bill_phone = functions::safeString($_POST["txtPhone"]);
			
			$qry = "UPDATE ".PREFIX."cart SET 
					bill_first_name='$bill_first_name',
					bill_last_name='$bill_last_name',
					bill_email='$bill_email',
					bill_company_name='$bill_company_name',
					bill_address='$bill_address',
					bill_country='$bill_country',
					bill_city='$bill_city',
					bill_zipcode='$bill_zipcode',
					bill_phone='$bill_phone'
					WHERE sessionid='".functions::safeString($_SESSION["sessionid"])."'";
			$result = mysqli_query($this->connLink->conn, $qry);

			if($result) return "succ"; else return "dbErr";
		}
		
		public function getCartTotal() {
			$qry = "SELECT SUM(price*qty) FROM ".PREFIX."cart_details WHERE sessionid='".functions::safeString($_SESSION["sessionid"])."'";			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				$arr = mysqli_fetch_array($result);
				return $arr[0];
			} else { return 0; }
			
			$this->record_count   = $record_count;
			return $row;
		}
			
		public function doLogin() {
			$email = strtolower(functions::safeString($_POST['txtEmail']));
			$password = functions::generateEncryptedPassword($_POST['txtPassword']);
			
			$qry = "SELECT * FROM ".PREFIX."register WHERE LOWER(email)='$email' AND password='$password'";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);
			
			if($record_count > 0) {
				$arr = mysqli_fetch_array($result);
				
				if($arr['status'] == 1) {
					$_SESSION["uSeRiD"] = $arr["id"];
					$_SESSION["uSeRnAmE"] = substr($arr["firstName"],0,1)." ".$arr["lastName"];
					
					//set cookie to store login
					if(functions::safeString($_POST["chkRememberMe"]) != "") {
						$cookie_name = "uSeRiD";
						$cookie_value = $arr["id"];
						setcookie($cookie_name, $cookie_value, time() + (86400 * 7), "/"); // 86400 = 1 day
						setcookie('uSeRnAmE',$arr["username"], time() + (86400 * 7), "/"); // 86400 = 1 day
					}
					if(functions::safeString($_REQUEST["_act"]) != "") {
						$redirectURL = base64_decode($_REQUEST["_act"]);
						header("location:$redirectURL");
					} else {
						header("location:".SITEURL.'accounts.php');
					}					
				} else if($arr['status'] == 2) {
					return 2;
				} else if($arr['status'] == 0) {
					return 0;
				}
			} else {
				return 4;
			}
		}	
		public function confirmRegsitration() {
			$email = functions::safeString($_REQUEST["uid"]);
			$confirmationKey = functions::safeString($_REQUEST["key"]);
			
			$qry = "SELECT * FROM ".PREFIX."register WHERE email='$email' AND confirmationKey='$confirmationKey'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if(mysqli_num_rows($rs) > 0) {
				$arr = mysqli_fetch_array($rs);		
				$status = $arr["status"];
				
				if($status == 0) {
					$qryUpd = "UPDATE ".PREFIX."register SET status=1 where email='$email' and confirmationKey='$confirmationKey'";
					mysqli_query($this->connLink->conn,$qryUpd);
					
					return "succ";
				} else if($status == 1) {
					return 1;
				} else if($status == 2) {
					return 2;
				}
			} else {
				return 3;
			}
		}
		
		public function getUserDetailsByID() {
			$qry = "SELECT * FROM ".PREFIX."register WHERE id='".functions::safeString($_SESSION["uSeRiD"])."'";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);

			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
				
		public function updateUserProfile() {
			$firstName = functions::safeString($_POST['txtFirstName']);
			$lastName = functions::safeString($_POST['txtLastName']);
			$customerPostcode = functions::safeString($_POST['txtPostcode']);
			$customerCountry = functions::safeString($_POST['txtCountry']);
			$customerCity = functions::safeString($_POST['txtCity']);
			$customerAddress = functions::safeString($_POST['txtAddress']);
			$phoneNumber = functions::safeString($_POST['txtPhone']);
			
			$qry = "UPDATE ".PREFIX."register SET
					firstName='$firstName',
					lastName='$lastName',
					customerPostcode='$customerPostcode',
					customerCountry='$customerCountry',
					customerCity='$customerCity',
					customerAddress='$customerAddress',
					phoneNumber='$phoneNumber'
					WHERE id='".functions::safeString($_SESSION["uSeRiD"])."'";
			$result = mysqli_query($this->connLink->conn, $qry);
			
			if($result) return "success"; else return "dbError";
		}
		
		public function processRegistration() {
			$email = strtolower(functions::safeString($_POST["txtEmail"]));
			$phone = strtolower(functions::safeString($_POST["txtPhone"]));
			$firstName = functions::safeString($_POST["txtFirstName"]);
			$lastName = functions::safeString($_POST["txtLastName"]);
			$password = functions::generateEncryptedPassword($_POST["txtPassword"]);
			$status = 0;
			$date_created = time();
			$confirmationKey = functions::randomPrefix(30);	
			
			$isEmailExists = self::checkEmailExists($email);
			
			if($isEmailExists == true) {
				return "emailExists";
			} else {
				$qry = "INSERT INTO ".PREFIX."register SET
						email='".$email."',
						phoneNumber='".$phone."',
					   `password`='".$password."', 
						firstName='".$firstName."',
						lastName='".$lastName."',
						date_created='".$date_created."',
						status='".$status."',
						confirmationKey='$confirmationKey'";
				$rs = mysqli_query($this->connLink->conn,$qry);
				
				if($rs) {
					$myFile = "templates/registration_confirmation_template.txt";
					$fh = fopen($myFile, 'r');

					$data = fread($fh,filesize($myFile));
					fclose($fh);			

					$data = str_replace("{siteURL}",SITEURL,$data);
					$data = str_replace("{copyRightsText}",COPYRIGHTS_TEXT,$data);
					$data = str_replace("{username}",$username,$data);
					$data = str_replace("{email}",$email,$data);
					$data = str_replace("{phone_no}",$phoneNo,$data);
					$data = str_replace("{key}",$confirmationKey,$data);
					$data = str_replace("{website_name}",WEBSITE_NAME,$data);					
					//echo $data;
					//exit;
			
					//Send Email
					$subject = "Account Activation";
					$mail = new PHPMailer;
					$mail->CharSet = 'UTF-8';
					$mail->setFrom(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addReplyTo(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addAddress($email, OUTGOING_EMAIL);
					$mail->Subject = $subject;
					$mail->msgHTML($data);
					
					//send the message, check for errors
					if (!$mail->send()) {
						echo "Mailer Error: " . $mail->ErrorInfo;
					}
					
					//Send Email to Administrator
					$myFile = "templates/registration_confirmation_template_admin.txt";
					$fh = fopen($myFile, 'r');
					$data = fread($fh,filesize($myFile));
					fclose($fh);
					
					$data = str_replace("{siteURL}",SITEURL,$data);
					$data = str_replace("{copyRightsText}",COPYRIGHTS_TEXT,$data);
					$data = str_replace("{website_name}",WEBSITE_NAME,$data);
					$data = str_replace("{full_name}",$firstName." ".$lastName,$data);
					$data = str_replace("{email}",$email,$data);					
					//echo $data;
					//exit;
					
					if (!$mail->send()) {
						echo "Mailer Error: " . $mail->ErrorInfo;
					}
					
					//Send Email
					$subject = WEBSITE_NAME." New User Registration";
					$mail = new PHPMailer;
					$mail->CharSet = 'UTF-8';
					$mail->setFrom(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addReplyTo(OUTGOING_EMAIL, OUTGOING_EMAIL);
					$mail->addAddress($this->admin_email, OUTGOING_EMAIL);
					$mail->Subject = $subject;
					$mail->msgHTML($data);
					
					//send the message, check for errors
					if (!$mail->send()) {
						echo "Mailer Error: " . $mail->ErrorInfo;
					}
					return "regSucc";
				} else {
					return "dbError";
				}
			}
		}
		
		public function changePassword($userID) {
			$oldPass = functions::generateEncryptedPassword($_REQUEST["txtOldPassword"]);
			$newPass = functions::generateEncryptedPassword($_REQUEST["txtPassword"]);
						
			$qry = "SELECT * FROM ".PREFIX."register WHERE `password`='$oldPass' and id='$userID'";
			$result = mysqli_query($this->connLink->conn, $qry);			
			$record_count = mysqli_num_rows($result);
			
			if($record_count) {
				$qryUpd = "UPDATE ".PREFIX."register set `password`='$newPass' WHERE id='$userID'";
				$res = mysqli_query($this->connLink->conn, $qryUpd);
				
				if($res) {
					return "1";
				} else {
					return "0";
				}
			} else {
				return "2";
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function forgotPassword() {
			$email = strtolower(functions::safeString($_REQUEST["txtEmail"]));
			$confirmationKey = functions::randomPrefix(30);
			
			//******** CHECK IF EMAIL ADDRESS ENTERED EXISTS IN THE DATABASE OR NOT
			$isExists = self::checkEmailExists($email);
			
			if($isExists == true) {
				$userDetails = self::getUserDetails($email);
				
				$arr = $userDetails;
				$userID = $arr["id"];
				$fullName = $arr["username"];
				
				//Insert into password request table
				$qryIns = "INSERT INTO ".PREFIX."reset_password 
						SET userID='$userID',
						confirmationKey='$confirmationKey',
						dateAdded='".time()."',
						status='0'";
				$rs = mysqli_query($this->connLink->conn,$qryIns);
				
				$resetID = mysqli_insert_id($this->connLink->conn);
				
				//Send password reset link to user's email
				//Read from Template file
				$myFile = "templates/forgot_password_template.txt";
				$fh = fopen($myFile, 'r');
				$data = fread($fh,filesize($myFile));
				fclose($fh);
				
				$data = str_replace("{siteURL}",SITEURL,$data);
				$data = str_replace("{copyRightsText}",COPYRIGHTS_TEXT,$data);
				$data = str_replace("{first_name}",$fullName,$data);
				$data = str_replace("{id}",$resetID,$data);
				$data = str_replace("{key}",$confirmationKey,$data);
				$data = str_replace("{websiteName}",WEBSITE_NAME,$data);
				$data = str_replace("{user_email}",$email,$data);
								
				//echo $data;
				//exit;
				
				//Send Email				
				$subject = WEBSITE_NAME." password reset request";
				$mail = new PHPMailer;
				$mail->CharSet = 'UTF-8';
				$mail->setFrom(OUTGOING_EMAIL, OUTGOING_EMAIL);
				$mail->addReplyTo(OUTGOING_EMAIL, OUTGOING_EMAIL);
				$mail->addAddress($email, OUTGOING_EMAIL);
				$mail->Subject = $subject;
				$mail->msgHTML($data);
				
				if (!$mail->send()) {
					echo "Mailer Error: " . $mail->ErrorInfo;
				}				
				
				return "fgpassSucc";
			} else {
				return "emailErr";
			}
		}

		private function getUserDetails($email) {
			$qry = "SELECT * FROM ".PREFIX."register WHERE LOWER(email)='$email'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if(mysqli_num_rows($rs) > 0) { 
				return mysqli_fetch_array($rs);
			}
			else
				return false;
		}
		
		private function checkEmailExists($email) {
			$qry = "SELECT * FROM ".PREFIX."register WHERE LOWER(email)='$email'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if(mysqli_num_rows($rs) > 0)
				return true;
			else
				return false;
		}
		
		public function checkLinkStatus($resetID, $key) {
			$qry = "SELECT * FROM ".PREFIX."reset_password 
					WHERE id=$resetID 
					AND confirmationKey='$key'
					AND DATEDIFF(NOW(),DATE(FROM_UNIXTIME(dateAdded))) < 1
					AND status=0";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if(mysqli_num_rows($rs) <= 0) {
				return "recErr";
			} else {
				return "recSucc";
			}
		}
		
		public function resetPassword($resetID,$key) {
			$password = functions::generateEncryptedPassword($_REQUEST["txtPassword"]);
			
			$qry = "UPDATE ".PREFIX."reset_password r
					LEFT JOIN ".PREFIX."register m ON m.id=r.userID
					SET m.password='$password', r.status=1
					WHERE r.id=$resetID and r.confirmationKey='$key'";
			$rs = mysqli_query($this->connLink->conn,$qry);
			
			if($rs) {
				return "resetSucc";
			} else {
				return "dbErr";
			}
		}
		
		public function getUserOrders($userID) {
			if(isset($_GET["page"]) && functions::safeString($_GET["page"]) != "" && is_numeric($_GET["page"]))
				$page = functions::safeString($_GET["page"]);
			else
				$page =1;
				
			$qry = "SELECT * FROM ".PREFIX."cart WHERE order_confirm='yes' AND userID='$userID' ORDER BY id DESC";
			$this->totalPages = functions::totalPages($qry);
			return functions::paginationData($qry,$page);
		}
		
		public function postReview($array=NULL) {
			$userID = functions::safeString($_SESSION["uSeRiD"]);
			$userRating = functions::safeString($_POST["star"]);
			$reviewDesc = functions::safeString($_POST["taReview"]);
			
			$qry = "INSERT INTO ".PREFIX."user_reviews SET
					userID='".$array['userID']."',
					productID='".$array['productID']."',
					userRating='$userRating',
					userReview='$reviewDesc',
					datePosted='".time()."',
					status='0'";
			$rs = mysqli_query($this->connLink->conn, $qry);
			
			if($rs)
				return "success";
			else
				return "dbErr";
		}
		
		public function fetchProductsToWriteReview($array=NULL) {
			$qry = "SELECT * FROM ".PREFIX."cart c 
					INNER JOIN ".PREFIX."cart_details cd ON c.sessionid=cd.sessionid 
					INNER JOIN ".PREFIX."products p ON p.id=cd.pid
					WHERE p.id NOT IN(SELECT productID FROM ".PREFIX."user_reviews WHERE userID='".$array["userID"]."')";
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND r.id='".functions::safeString($array["id"])."'";
				}
				if(array_key_exists("productID",$array)) {
					$qry .= " AND p.id='".functions::safeString($array["productID"])."'";
				}
				if(array_key_exists("userID",$array)) {
					$qry .= " AND c.userID='".functions::safeString($array["userID"])."'";
				}
			}
			
			$qry .= " GROUP BY cd.pid ORDER BY c.id DESC";
			
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);
			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function getUserReviews($array=NULL) {
			if(isset($_GET["page"]) && functions::safeString($_GET["page"]) != "" && is_numeric($_GET["page"]))
				$page = functions::safeString($_GET["page"]);
			else
				$page =1;
				
			$qry = "SELECT * FROM ".PREFIX."user_reviews r
					INNER JOIN ".PREFIX."products p ON p.id=r.productID
					INNER JOIN ".PREFIX."register reg ON reg.id=r.userID
					WHERE 1=1";
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND r.id='".functions::safeString($array["id"])."'";
				}
				if(array_key_exists("productID",$array)) {
					$qry .= " AND p.id='".functions::safeString($array["productID"])."'";
				}
				if(array_key_exists("userID",$array)) {
					$qry .= " AND r.userID='".functions::safeString($array["userID"])."'";
				}
				if(array_key_exists("status",$array)) {
					$qry .= " AND r.status='".functions::safeString($array["status"])."'";
				}
			}
			
			$this->totalPages = functions::totalPages($qry);
			return functions::paginationData($qry,$page);
		}
		
		//BLOG
		public function fetchBlogCategoriesCount($array='') {
			$qry = "SELECT *, c.id as id, count(b.id) as totalPosts FROM ".PREFIX."blog_category c 
					LEFT JOIN ".PREFIX."blog b ON c.id=b.categoryID WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND c.id='".functions::safeString($array["id"])."'";
				}
			}
			$qry.= " GROUP BY c.id ORDER BY categoryName";
				
			if(!empty($array)) {
				if(array_key_exists("limit",$array)) {
					$qry .= " LIMIT ".functions::safeString($array["limit"])."";
				}
			}
			//echo $qry;
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);
			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchBlogCategories($array='') {
			$qry = "SELECT * FROM ".PREFIX."blog_category WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("id",$array)) {
					$qry .= " AND id='".functions::safeString($array["id"])."'";
				}
				if(array_key_exists("category_url",$array)) {
					$qry .= " AND category_url='".functions::safeString($array["category_url"])."'";
				}
			}
			$qry.= " ORDER BY categoryName";
				
			if(!empty($array)) {
				if(array_key_exists("limit",$array)) {
					$qry .= " LIMIT ".functions::safeString($array["limit"])."";
				}
			}
			//echo $qry;
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);
			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchRecentBlog() {
			$qry = "SELECT * FROM ".PREFIX."blog ORDER BY id DESC LIMIT 4";
			$result = mysqli_query($this->connLink->conn, $qry);
			$record_count = mysqli_num_rows($result);
			if ($record_count) {
				while ($obj = mysqli_fetch_object($result)) {
					$row[] = $obj;
				}
			}
			
			$this->record_count   = $record_count;
			return $row;
		}
		
		public function fetchBlog($array=NULL) {
			if(isset($_GET["page"]) && functions::safeString($_GET["page"]) != "" && is_numeric($_GET["page"]))
				$page = functions::safeString($_GET["page"]);
			else
				$page =1;
				
			$qry = "SELECT *, b.id as id FROM ".PREFIX."blog b
					INNER JOIN ".PREFIX."blog_category c ON c.id=b.categoryID
					WHERE 1=1";
			
			if(!empty($array)) {
				if(array_key_exists("categoryID",$array)) {
					$qry .= " AND categoryID='".functions::safeString($array["categoryID"])."'";
				}
				if(array_key_exists("id",$array)) {
					$qry .= " AND b.id='".functions::safeString($array["id"])."'";
				}
				if(array_key_exists("search_string",$array)) {
					$qry .= " AND (
								LOWER(b.blogTitle) LIKE '%".functions::safeString($array["search_string"])."%'
								OR LOWER(b.blogDescription) LIKE '%".functions::safeString($array["search_string"])."%'
								OR LOWER(b.blogAuthor) LIKE '%".functions::safeString($array["search_string"])."%'
							)";
				}
			}
			
			$qry .= " ORDER BY b.id DESC";
			//echo $qry;
			$this->totalPages = functions::totalPages($qry);
			return functions::paginationData($qry,$page);
		}
		//END BLOG
	}
?>