<?php
/**
* 2014 Madman
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*  @author Madman
*  @copyright  2014 Madman
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/

	if (!defined('_PS_VERSION_'))
	exit;

class ExportCustomers extends Module
{
	public function __construct()
	{
		$this->name = 'exportcustomers';
		$this->tab = 'export';
		$this->version = "2.0";
		$this->author = 'Madman';
		// Based on Willem's module';
		$this->bootstrap = true;
		$this->config = array(
			'PS_MOD_EXPCUS_CUSNUM' => array(
				'value' => 1, // Default value
				'configurable' => true, // use in _updateConfig, if set to false, the setting will be ignored when saveing settings
			),
		);

		parent::__construct();

		$this->displayName = $this->l('Export customers');
		$this->description = $this->l('Export customer info and address to csv file');
	}

	public function install()
	{
		if (!parent::install()
			// OR
		)
			return false;
		return true;
	}

	private function _export()
	{
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
			$sql = "SELECT ";
			$end = count($toExport)-1; // count keys in array, and remove 1 to compensate for index 0
			foreach($toExport as $key=>$fields) {
				$sql .= $fields[0] . " AS " . $fields[1]; // Add sql
				if($key != $end) { //if not last key
					$sql .= ", "; // add , to sql
				}
			}
			// cust_id should be changed to PS_MOD_EXPCUS_CUSNUM
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

	public function getContent()
	{
		$this->_html = '<hr><h2>'.$this->displayName. ' ' . $this->version . '</h2>';
// 		$this->_html.= '<p>'.$this->l('This module allow to make a customers csv file.').'</p>';

		// If we clicked the export button
		if (isset($_POST['exportcustomer'])) {
			$this->_html .= $this->_export();
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

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Settings'),
				'icon' => 'icon-cogs'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Ajax cart'),
					'name' => 'PS_MOD_EXPCUS_CUSNUM',
					'hint' => $this->l('The first id to export'),
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
				)
			),
			// Can I add a second button for export here?
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang =
		Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockCart';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		$fields_value = array();
		foreach($this->config as $key => $value)
			$fields_value[$key] = Configuration::get($key);

		return $fields_value;
	}

}
?>
