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
		// Based on Willem's module
		$this->bootstrap = true;
		$this->config = array(
			'PS_MOD_EXPCUS_CUSNUM' => array(
				'value' => 0, // Default value
				'configurable' => true, // use in _updateConfig, if set to false, the setting will be ignored when saveing settings
			),
			'PS_MOD_EXPCUS_DELIMITER' => array(
				'value' => ';',
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_UTF8' => array(
				'value' => 1,
				'configurable' => true,
			),
		);

		parent::__construct();

		$this->displayName = $this->l('Export customers');
		$this->description = $this->l('Export customer info and address to csv file');
	}

	public function install()
	{
		if (!parent::install() || !$this->installDB() )
			return false;

		return true;
	}


	function installDB()
	{
		$create_table = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'export_customer_fields` (
		`id` INT(10) NOT NULL AUTO_INCREMENT,
		`type` VARCHAR( 255 ) NOT NUL
		`expcusfield` VARCHAR(255) NOT NULL,
		`label` VARCHAR(255) NOT NULL,
		`name` VARCHAR(255) NOT NULL,
		`active` INT(10) DEFAULT 0,
		`position` INT(10) DEFAULT 0,
		PRIMARY KEY (`id`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

		$insert_customer_data = Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."export_customer_fields` (`type`,`expcusfield`,`label`,`name`,`position`) VALUES
		('customer','c.id_customer','Customer ID','Customer ID','1'),
		('customer','c.id_shop_group','Customer Shop Group ID','Customer Shop Group ID','2'),
		('customer','c.id_shop','Customer Shop ID','Customer Shop ID','3'),
		('customer','c.id_gender','Customer Gender ID','Customer Gender ID','4'),
		('customer','c.id_default_group','Customer Default Group ID','Customer Default Group ID','5'),
		('customer','c.id_risk','Customer B2B Risk','Customer B2B Risk','6'),
		('customer','c.company','Customer Company','Customer Company','7'),
		('customer','c.siret','Customer Siret','Customer Siret','8'),
		('customer','c.ape','Customer Ape','Customer Ape','9'),
		('customer','c.firstname','Customer Firstname','Customer Firstname','10'),
		('customer','c.lastname','Customer Lastname','Customer Lastname','11'),
		('customer','c.email','Customer Email','Customer Email','12'),
		('customer','c.passwd','Customer Password Hash','Customer Password Hash','13'),
		('customer','c.last_passwd_gen','Customer Last Password Gen','Customer Last Password Gen','14'),
		('customer','c.birthday','Customer Birthday','Customer Birthday','15'),
		('customer','c.newsletter','Customer Newsletter','Customer Newsletter','16'),
		('customer','c.ip_registration_newsletter','Customer IP Reg Newsletter','Customer IP Reg Newsletter','17'),
		('customer','c.newsletter_date_add','Customer Newsletter Date','Customer Newsletter Date','18'),
		('customer','c.optin','Customer Opt-In','Customer Opt-In','19'),
		('customer','c.website','Customer Website','Customer Website','20'),
		('customer','c.outstanding_allow_amount','Customer B2B Allow Amount','Customer B2B Allow Amount','21'),
		('customer','c.show_public_prices','Customer Show Price','Customer Show Price','22'),
		('customer','c.max_payment_days','Customer B2B Payment Days','Customer B2B Payment Days','23'),
		('customer','c.secure_key','Customer Secure Key','Customer Secure Key','24'),
		('customer','c.note','Customer Note','Customer Note','25'),
		('customer','c.active','Customer Active','Customer Active','26'),
		('customer','c.is_guest','Customer Guest','Customer Guest','27'),
		('customer','c.date_add','Customer Date Add','Customer Date Add','28'),
		('customer','c.date_upd','Customer Date Update','Customer Date Update','29')
		");

		$insert_address_data = Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."export_customer_fields` (`type`,`expcusfield`,`label`,`name`) VALUES
		('address','a.id_address','Address ID','Address ID','30'),
		('address','a.id_country','Address Country ID','Address Country ID','31'),
		('address','a.id_state','Address State ID','Address State ID','32'),
		('address','a.id_manufacturer','Address Manufacturer ID','Address Manufacturer ID','33'),
		('address','a.id_supplier','Address Supplier ID','Address Supplier ID','34'),
		('address','a.id_warehouse','Address Warehouse ID','Address Warehouse ID','35'),
		('address','a.alias','Address Alias','Address Alias','36'),
		('address','a.company','Address Company','Address Company','37'),
		('address','a.firstname','Address Firstname','Address Firstname','38'),
		('address','a.lastname','Address Lastname','Address Lastname','39'),
		('address','a.address1','Address Address1','Address Address1','40'),
		('address','a.address2','Address Address2','Address Address2','41'),
		('address','a.postcode','Address Postcode','Address Postcode','42'),
		('address','a.city','Address City','Address City','43'),
		('address','a.other','Address Other','Address Other','44'),
		('address','a.phone','Address Phone','Address Phone','45'),
		('address','a.phone_mobile','Address Mobilephone','Address Mobilephone','46'),
		('address','a.vat_number','Address VAT','Address VAT','47'),
		('address','a.dni','Address DNI','Address DNI','48'),
		('address','a.date_add','Address Date Add','Address Date Add','49'),
		('address','a.date_upd','Address Date Update','Address Date Update','50'),
		('address','a.active','Address Active','Address Activate','51')
		");
		if ($create_table && $insert_customer_data && $insert_address_data)
			return true;

		return false;
	}

	private function _updateActiveFields()
	{
		if (Tools::isSubmit('submitFieldsCustomer'))
			$type = 'customer';
		elseif (Tools::isSubmit('submitFieldsAddress'))
			$type = 'address';

		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`active`,`name` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `type` = \''.$type.'\'');
		$sqlActive = '';
		$sqlName = '';
		$sqlINArray = array();
		foreach ($result as $field)
		{
			// make sure we got the field in $_POST and that the value does not match db value
			if (isset($_POST[str_replace('.','_',$field['expcusfield']).'_switch']) && $_POST[str_replace('.','_',$field['expcusfield']).'_switch'] != $field['active'])
			{
				$sqlActive .= 'WHEN \''.$field['expcusfield'].'\' THEN \'' .$_POST[str_replace('.','_',$field['expcusfield']).'_switch'].'\' '; // Create sql to update value
				$sqlINArray[$field['expcusfield']] = $field['expcusfield']; // set the field for sql IN
			}

			if (isset($_POST[str_replace('.','_',$field['expcusfield']).'_name']) && $_POST[str_replace('.','_',$field['expcusfield']).'_name'] != $field['name'])
			{
				$sqlName = 'WHEN \''. $field['expcusfield'].'\' THEN \'' .$_POST[str_replace('.','_',$field['expcusfield']).'_name'].'\' '; // Create sql to update value
				$sqlINArray[$field['expcusfield']] = $field['expcusfield']; // set the field for sql IN
			}
		}
		// make sure we got either name or active to update
		if ($sqlActive != '' || $sqlName != '')
		{
			// build the sql command
			$sql = 'UPDATE `'._DB_PREFIX_.'export_customer_fields` SET ';
			// if we got active, add it to sql
			if ($sqlActive != '')
				$sql .= '`active` = CASE `expcusfield` '.$sqlActive.' END';
			// if we got both active and name, we need to add a , at the end of active CASE
			if ($sqlActive != '' && $sqlName != '')
				$sql .= ', ';
			// if we got name, add it to sql
			if ($sqlName != '')
				$sql .= '`name` = CASE `expcusfield` '.$sqlName.' END';
			 // set where with imploded IN array
			$sql .= ' WHERE `expcusfield` IN(\''.implode('\',\'', $sqlINArray).'\')';
			 Db::getInstance()->execute($sql);
		}
	}

	private function _updatePositions()
	{
		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`position` FROM `'._DB_PREFIX_.'export_customer_fields`');
		$sqlPosition = '';
		$sqlINArray = array();
		foreach ($result as $field)
		{
			// make sure we got the field in $_POST and that the value does not match db value
			if (isset($_POST[str_replace('.','_',$field['expcusfield']).'_pos']) && $_POST[str_replace('.','_',$field['expcusfield']).'_pos'] != $field['position'])
			{
				$sqlPosition .= 'WHEN \''.$field['expcusfield'].'\' THEN \'' .$_POST[str_replace('.','_',$field['expcusfield']).'_pos'].'\' '; // Create sql to update value
				$sqlINArray[$field['expcusfield']] = $field['expcusfield']; // set the field for sql IN
			}
		}

		if ($sqlPosition != '')
		{
			// build the sql command
			$sql = 'UPDATE `'._DB_PREFIX_.'export_customer_fields` SET ';
			$sql .= '`position` = CASE `expcusfield` '.$sqlPosition.' END';
			$sql .= ' WHERE `expcusfield` IN(\''.implode('\',\'', $sqlINArray).'\')';
			 Db::getInstance()->execute($sql);
		}
	}

	private function _updateConfig()
	{
		foreach ($this->config as $key => $value)
			if (isset($value['configurable']) && $value['configurable'])
				if (Tools::getValue($key) != '')
					Configuration::updateValue($key, Tools::getValue($key));
	}

	private function _runExport()
	{
		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`name`,`position` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `active` = 1 ORDER BY `position`');
		$sqlNameMerge = array();
		$sqlConstruct = array();
		foreach ($result as $field)
		{
			// if name is in array
			if (isset($sqlNameMerge[$field['name']]))
				// get the index and add the field to sub-aray
				$sqlConstruct[$sqlNameMerge[$field['name']]]['expcusfields'][] = $field['expcusfield'];
			else
			{
				// else add field to new index
				$sqlConstruct[] = array('name' => $field['name'], 'expcusfields' => array($field['expcusfield']));
				// add name and index to array
				$sqlNameMerge[$field['name']] = count($sqlConstruct)-1;
			}
		}

		$sqlCmd = '';
		foreach ($sqlConstruct as $sqlInfo)
		{
			if (isset($sqlInfo['expcusfields'][1]))
			{
				$field = implode(', ', $sqlInfo['expcusfields']);
				$index = 'CONCAT_WS(\' \','.$field.')';
			}
			else
				$index = $sqlInfo['expcusfields'][0];

			$sqlCmd .= $index.' AS \''.$sqlInfo['name'].'\', ';
		}
		$sqlCmd = substr($sqlCmd, 0, -2);

		$sql = 'SELECT '.$sqlCmd.' FROM '._DB_PREFIX_.'customer c
		LEFT JOIN '._DB_PREFIX_.'address a ON (c.id_customer = a.id_customer)
		WHERE c.id_customer > '.Configuration::get('PS_MOD_EXPCUS_CUSNUM').' AND a.deleted = 0 AND c.deleted = 0
		ORDER BY c.id_customer ASC';

		$csvData = Db::getInstance()->executeS($sql);

		$delimiter = Configuration::get('PS_MOD_EXPCUS_DELIMITER');
		$csvNames = array();
		foreach ($sqlNameMerge as $name => $pos)
			$csvNames[] = $name;

		$csvHeader = implode($delimiter, $csvNames);

		$sqlNameMerge = null; // Not needed anymore and must commit seppuku
		$csvNames = null;

		$utf8 = Configuration::get('PS_MOD_EXPCUS_UTF8');
		$csvFile = fopen(dirname(__FILE__).'/export_customers_'.($utf8 ? 'utf8' : 'iso').'.csv', 'w');
		fwrite($csvFile, $csvHeader."\r\n");

		$csvString = '';
		foreach ($csvData as $line)
		{
			$csvString .= str_replace(array("\n", "\r"), ' / ' , implode($delimiter, $line))."\r\n"; // use this awsome code to create one line of csv
		}

		if (!$utf8)
			$csvString = utf8_decode($csvString);

		fwrite($csvFile, $csvString);

		return $this->displayConfirmation($sql);

		//  TODO start extending with diffrent merge seperator, gender replace, country replace and so on.

		// Get active fields from sql
		// build the select
			// how do I check if I should merge two fields?
			// reverse the construct array, key base is name, and if exists, they need to be CONCAT_WS
		// get customer number
		// write sql with join tabels
		// prepare the csv data
		// check if iso or utf8
		// create csv
		// ? change customer number to highest id - or remove that feature?
		// Not used by default, ask on forum, does anyine use it, if so, it should be an option
	}

	public function getContent()
	{
		$debug = '';
		if (Tools::isSubmit('submitGenerell'))
		{
			$this->_updateConfig();
			$debug = $this->_runExport();
		}
		elseif (Tools::isSubmit('submitFieldsCustomer') || Tools::isSubmit('submitFieldsAddress'))
			$this->_updateActiveFields();
		elseif (Tools::isSubmit('submitFieldsPositions'))
			$this->_updatePositions();

		$this->context->smarty->assign(array(
			'version' => $this->version,
			'module_name' => $this->displayName,
			'generell_content' => $this->renderFormGenerell(),
			'customer_fields_content' => $this->renderFormActiveFields('customer'),
			'address_fields_content' => $this->renderFormActiveFields('address'),
			'positions_content' => $this->renderFormPosition(),
			'debug' => $debug,
		));
		return $this->display($this->_path, '/views/templates/admin/admin.tpl');
	}

	private function renderFormGenerell()
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
					'label' => $this->l('Customer Number'),
					'name' => 'PS_MOD_EXPCUS_CUSNUM',
					'hint' => $this->l('First dd must be higher then this number (number not included in export)'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Delimiter'),
					'name' => 'PS_MOD_EXPCUS_DELIMITER',
					'hint' => $this->l('The delimiter to use in csv file'),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Utf8 encoding'),
					'name' => 'PS_MOD_EXPCUS_UTF8',
					'hint' => $this->l('Set the encoding of the file'),
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					),
				),
			),
			'submit' => array(
				'title' => $this->l('Save / Export'),
				'class' => 'btn btn-default pull-right'
				)
			),
		);
		$submit = 'submitGenerell';
		return $this->renderForm($fields_form, $submit);
	}
	
	private function renderFormActiveFields($type)
	{
		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`label` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `type` = \''.$type.'\'');
		foreach ($result as $field)
		{
			$input[] = array(
				'type' => 'switch',
				'label' => $this->l($field['label']),
				'name' => $field['expcusfield'].'_switch',
				'hint' => $this->l('Activate this field'),
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					),
				),
			);
			$input[] = array(
					'type' => 'text',
					'label' => $this->l($field['label']),
					'name' => $field['expcusfield'].'_name',
					'hint' => $this->l('CSV Field name'),
				);
		}

		$fields_form = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Activate '.ucfirst($type).' Fields'),
				'icon' => 'icon-cogs'
			),
			'input' => $input,
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
				)
			),
		);
		$submit = 'submitFields'.ucFirst($type);
		return $this->renderForm($fields_form, $submit);
	}

	public function renderFormPosition()
	{
		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`name`,`position` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `active` = 1 ORDER BY `position`');
		foreach ($result as $field)
		{
			$input[] = array(
				'type' => 'text',
				'label' => $this->l($field['name']),
				'name' => $field['expcusfield'].'_pos',
				'hint' => $this->l('Set position in csv')
			);
		}

		$fields_form = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Set Positions'),
				'icon' => 'icon-cogs'
			),
			'input' => $input,
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
				)
			),
		);
		$submit = 'submitFieldsPositions';
		return $this->renderForm($fields_form, $submit);
	}


	public function renderForm($fields_form, $submit)
	{
		$helper = new HelperForm();
		$helper->submit_action = $submit;
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang =
		Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
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
			$fields_value[$key] = (Configuration::get($key) ? Configuration::get($key) : $value['value']);

		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`active`,`name`,`position` FROM `'._DB_PREFIX_.'export_customer_fields`');
		foreach ($result as $field)
		{
			$fields_value[$field['expcusfield'].'_switch'] = $field['active'];
			$fields_value[$field['expcusfield'].'_name'] = $field['name'];
			$fields_value[$field['expcusfield'].'_pos'] = $field['position'];
		}

		return $fields_value;
	}

	private function _export()
	{
		$delimiter = ";";
			$sql = "SELECT ";
			$end = count($toExport)-1; // count keys in array, and remove 1 to compensate for index 0
			foreach($toExport as $key=>$fields) {
				$sql .= $fields[0] . " AS " . $fields[1]; // Add sql
				if($key != $end) { //if not last key
					$sql .= ", "; // add , to sql
				}
			}
			if (!$customer_id = Configuration::get('PS_MOD_EXPCUS_CUSNUM'))
				$customer_id = 0;
			
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

}
?>
