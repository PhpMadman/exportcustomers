<?php

class ExportCustomers extends Module
{
	function __construct()
	{
		$this->name = 'exportcustomers';
		$this->tab = 'Export';
		$this->version = "1.4.1";
		$this->author = 'Madman'; //. Based on Willem's module';

		parent::__construct();

		$this->displayName = $this->l('Export customers');
		$this->description = $this->l('Module for export customers to CSV file.');
	}

        function install()
        {
                if (!parent::install())
                        return false;
        return true;
        }

	function getContent()
	{
		$this->_html = '<hr><h2>'.$this->displayName. ' ' . $this->version . '</h2>';
		$this->_html.= '<p>'.$this->l('This module allow to make a customers csv file.').'</p>';

		$delimiter = ";";
		$toExport = array(
			array('c.id_customer','Id'),
			array('c.firstname','Firstname'),
			array('c.lastname','Lastname'),
			array('c.email','Email'),
			array('c.website','Website'),
			array('c.siret','Orgnr'),
			array('c.company','Company'),
			array('a.firstname','aFirstname'),
			array('a.lastname','aLastname'),
			array('a.address1','Address1'),
			array('a.address2','Address2'),
			array('a.postcode','Postcode'),
			array('a.phone','Phone'),
			array('a.phone_mobile','Mobilephone'),
		);

		/*
ADDRESS FIELDS						CUSTOMER FIELDS
		id_address								id_customer
		id_country								id_shop_group
		id_state									id_shop
		id_customer								id_gender
		id_manufacturer							id_default_group
		id_supplier								id_risk
		id_warehouse							company
		alias									siret
		company								ape
		lastname								firstname
		firstname								lastname
		address1								email
		address2								passwd
		postcode								last_passwd_gen
		city										birthday
		other									newsletter
		phone									ip_registration_newsletter
		phone_mobile							newsletter_date_add
		vat_number								optin
		dni										website
		date_add								outstanding_allow_amount
		date_upd								show_public_prices
		active									max_payment_days
												secure_key
												note
												active
												is_guest
												date_add
												date_upd
			*/

		// If we clicked the export button
		if (isset($_POST['exportcustomer'])) {
			$sql = "SELECT ";
			$end = count($toExport)-1; // count keys in array, and remove 1 to compensate for index 0
			foreach($toExport as $key=>$fields) {
				$sql .= $fields[0] . " AS " . $fields[1]; // Add sql
				if($key != $end) { //if not last key
					$sql .= ", "; // add , to sql
				}
			}
			if (isset($_POST["cust_id"])) {
				$cust_id = $_POST["cust_id"];
			}  else {
				$cust_id = file_get_contents(dirname(__FILE__).'/id.dat');
				if(!$cust_id) {
					$cust_id = 0;
				}
			}

			// this sql limits the export to the customers that have address
			$sql .= " FROM "._DB_PREFIX_."customer c, "._DB_PREFIX_."address a
			WHERE c.id_customer = a.id_customer AND c.id_customer > $cust_id AND a.deleted = 0 AND c.deleted = 0
			ORDER BY a.id_customer ASC";
			// id_customer must be higher then cust_id. So if is is 6, customer that is exported will be 7

			$orderlist = Db::getInstance()->ExecuteS($sql);

			// Create the utf8 csv file
			$file = fopen(dirname(__FILE__).'/export_customers_utf8.csv', 'w');
				$firstline = "";
			foreach($toExport as $key=>$fields) {

				$firstline .= $fields[1];
				if($key != $end) { //if not last key
					$firstline .= $delimiter;
				}
			}
			fwrite($file, $firstline."\r\n");
			foreach($orderlist AS $orderline){
			$string = implode($delimiter, $orderline);
				fwrite($file, $string ."\r\n");
			}
			fclose($file);

			// Create the iso csv file
			$file = fopen(dirname(__FILE__).'/export_customers_iso.csv', 'w');
				$firstline = "";
			foreach($toExport as $key=>$fields) {

				$firstline .= $fields[1];
				if($key != $end) { //if not last key
					$firstline .= $delimiter;
				}
			}
			fwrite($file, $firstline."\r\n");
			foreach($orderlist AS $orderline){
			$string = implode($delimiter, $orderline);
				fwrite($file, utf8_decode($string) ."\r\n");
			}
			fclose($file);

			// Get the id of the latest customer
			$sql = "SELECT MAX(id_customer) FROM "._DB_PREFIX_."customer";
			$maxid = Db::getInstance()->ExecuteS($sql);
			$file = fopen(dirname(__FILE__).'/id.dat', 'w');
			fwrite($file,$maxid[0]['MAX(id_customer)']);
			fclose($file);

			$this->_html.= 'Export completed<br>';
			$this->_html .= "Next export will start at customer nr: " . ($maxid[0]['MAX(id_customer)']+1) . "<br><br>";

			$this->_html.= '<a href="'.Tools::getHttpHost(true).__PS_BASE_URI__.'modules/exportcustomers/export_customers_utf8.csv" target="_blank">Download export_customers_utf8.csv</a><br>';
			$this->_html.= '<a href="'.Tools::getHttpHost(true).__PS_BASE_URI__.'modules/exportcustomers/export_customers_iso.csv" target="_blank">Download export_customers_iso.csv</a><br>';

			return $this->_html;


		}
		else
		{
			$file_id = file_get_contents(dirname(__FILE__).'/id.dat');
			if(!$file_id) {
				$file_id = 0;
			}
			$this->_html .= '<form method="post">
				This export will start with customer number: <input type="text" value="' . ($file_id+1) . '" name="cust_id" style="width:20px;" /><br>
				<input type="submit" name="exportcustomer" value="'.$this->l('Export file').'" />
			</form>';
			$this->_html .='
			<h3 style="margin-top:2em;">'.$this->l('Explanation extraction  :').'</h3>
			<dl>
				<dt><i class="champ">'.$this->l('Custno').'</i> :</dt><dd style="padding: 0.2em 0 0.6em 2em;">'.$this->l('Identification customers').'</dd>
				<dt><i class="champ">'.$this->l('Gender;').'</i> :</dt><dd style="padding: 0.2em 0 0.6em 2em;">'.$this->l('(1/2) 1 is a man; 2 is a woman and 9 not known;').'</dd>

			</dl>
			';
			$this->_html .= '<hr>';
			return $this->_html;
		}
	}
}
?>
