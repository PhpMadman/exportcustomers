<?php
/**
* 2015 Madman
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
*  @copyright  2015 Madman
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
		$this->version = '2.0.2';
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
			'PS_MOD_EXPCUS_MERGE_DELIMITER' => array(
				'value' => ' ',
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_CUSTYPE' => array(
				'value' => 2,
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_NEWSLETTER' => array(
				'value' => 2,
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_GENDER_TEXT' => array(
				'value' => 0,
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_COUNTRY_TEXT' => array(
				'value' => 0,
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_CUS_ON' => array(
				'value' => 1,
				'configurable' => true,
			),
			'PS_MOD_EXPCUS_RNT_REP' => array(
				'value' => ' / ',
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
		`type` VARCHAR( 255 ) NOT NULL,
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

		$insert_address_data = Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."export_customer_fields` (`type`,`expcusfield`,`label`,`name`,`position`) VALUES
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

		/** -Special- */
		$insert_special_data = Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."export_customer_fields` (`type`,`expcusfield`,`label`,`name`,`position`) VALUES
		('special','lastvisit','Last Visit','Last Visit','52')
		");
		/* -Special- **/
		if ($create_table && $insert_customer_data && $insert_address_data && $insert_special_data)
			return true;

		return false;
	}

	private function _updateActiveFields()
	{
		if (Tools::isSubmit('submitFieldsCustomer'))
			$type = 'customer';
		elseif (Tools::isSubmit('submitFieldsAddress'))
			$type = 'address';
		elseif (Tools::isSubmit('submitFieldsSpecial'))
			$type = 'special';

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
				if ($_POST[$key] != '')
					Configuration::updateValue($key, $_POST[$key]);
	}

	private function _runExport()
	{
		$debug = '';
		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`name`,`position` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `active` = 1 ORDER BY `position`');
		$sqlNameMerge = array();
		$sqlConstruct = array();
		$mergeDelimiter = Configuration::get('PS_MOD_EXPCUS_MERGE_DELIMITER');
		foreach ($result as &$field)
		{
			// if name is in array
			if (isset($sqlNameMerge[$field['name']]))
				// get the index and add the field to sub-array
				$sqlConstruct[$sqlNameMerge[$field['name']]]['expcusfields'][] = $field['expcusfield'];
			else
			{
				// replace country with name
				if ($field['expcusfield'] == 'a.id_country' && Configuration::get('PS_MOD_EXPCUS_COUNTRY_TEXT'))
					$field['expcusfield'] = '(SELECT `name` FROM `'._DB_PREFIX_.'country_lang` WHERE `id_lang` = '.Context::getContext()->language->id.' AND `id_country` = a.id_country)';

				/** -Special- */
				// Special code for lastvisit key
				if ($field['expcusfield'] == 'lastvisit')
					$field['expcusfield'] = '(SELECT co.date_add FROM '._DB_PREFIX_.'guest g
					LEFT JOIN '._DB_PREFIX_.'connections co ON co.id_guest = g.id_guest
					WHERE g.id_customer = c.id_customer ORDER BY co.date_add DESC
					LIMIT 1 )';
				/* -Special- **/

				// add name and index to array
				$sqlNameMerge[$field['name']] = count($sqlConstruct); // adding this before adding to construct, will same math of -1
				// add field to new index
				$sqlConstruct[] = array('name' => $field['name'], 'expcusfields' => array($field['expcusfield']));
				// add name and index to array
			}
		}

		$sqlCmd = '';
		foreach ($sqlConstruct as $sqlInfo)
		{
			if (isset($sqlInfo['expcusfields'][1]))
			{
				$field = implode(', ', $sqlInfo['expcusfields']);
				$index = 'CONCAT_WS(\''.$mergeDelimiter.'\', '.$field.')';
			}
			else
				$index = $sqlInfo['expcusfields'][0];

			$sqlCmd .= $index.' AS \''.$sqlInfo['name'].'\', ';
		}
		$sqlCmd = substr($sqlCmd, 0, -2);

		$sql = 'SELECT '.$sqlCmd.' FROM '._DB_PREFIX_.'customer c
		LEFT JOIN '._DB_PREFIX_.'address a ON (c.id_customer = a.id_customer)
		WHERE c.id_customer > '.Configuration::get('PS_MOD_EXPCUS_CUSNUM').' AND a.deleted = 0 AND c.deleted = 0
		AND c.active != '.Configuration::get('PS_MOD_EXPCUS_CUS_ON').'
		AND c.is_guest != '.Configuration::get('PS_MOD_EXPCUS_CUSTYPE').'
		AND c.newsletter != '.Configuration::get('PS_MOD_EXPCUS_NEWSLETTER').'
		ORDER BY c.id_customer ASC';

		// prepare variabels
		$delimiter = Configuration::get('PS_MOD_EXPCUS_DELIMITER');
		$csvNames = array();
		$utf8 = Configuration::get('PS_MOD_EXPCUS_UTF8');
		$csvString = '';
		$rntReplace = Configuration::get('PS_MOD_EXPCUS_RNT_REP');

		foreach ($sqlNameMerge as $name => $pos)
			$csvNames[] = $name;

		$csvHeader = implode($delimiter, $csvNames);

		$sqlNameMerge = null; // Not needed anymore and must commit seppuku
		$csvNames = null;

		$csvFile = fopen(dirname(__FILE__).'/export_customers_'.($utf8 ? 'utf8' : 'iso').'.csv', 'w');
		fwrite($csvFile, $csvHeader."\r\n");

		$csvData = Db::getInstance()->ExecuteS($sql);

		// Gender modifier
		if (Configuration::get('PS_MOD_EXPCUS_GENDER_TEXT'))
		{
			$genderName = Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `expcusfield` = \'c.id_gender\'');
			$debug .= 'GenderName : '.$genderName;
			$genderLetter = array(1 => 'M', 2 => 'F');
			foreach ($csvData as &$line)
			{
				if (isset($line[$genderName]))
					if (isset($genderLetter[$line[$genderName]]))
						$line[$genderName] = $genderLetter[$line[$genderName]];
					else
						$line[$genderName] = 'N';
			}
		}
		foreach ($csvData as $line)
			$csvString .= str_replace(array("\r\n", "\n", "\r"), $rntReplace , implode($delimiter, $line))."\r\n"; // use this awsome code to create one line of csv

		if (!$utf8)
			$csvString = utf8_decode($csvString);

		fwrite($csvFile, $csvString);

// 		$debug .= $this->displayConfirmation($sql);
		return $debug;

	}

	public function getContent()
	{
		$debug = '';
		if (Tools::isSubmit('submitGenerell'))
		{
			$this->_updateConfig();
			$debug = $this->_runExport();
			$this->context->smarty->assign('download_link',
				'<a href="'.Tools::getHttpHost(true).__PS_BASE_URI__.'modules/exportcustomers/export_customers_'.(Configuration::get('PS_MOD_EXPCUS_UTF8') ? 'utf8' : 'iso').'.csv" target="_blank">export_customers_utf8.csv</a>');
		}
		elseif (Tools::isSubmit('submitFieldsCustomer') || Tools::isSubmit('submitFieldsAddress') || Tools::isSubmit('submitFieldsSpecial'))
			$this->_updateActiveFields();
		elseif (Tools::isSubmit('submitFieldsPositions'))
			$this->_updatePositions();

		$this->context->smarty->assign(array(
			'version' => $this->version,
			'module_name' => $this->displayName,
			'generell_content' => $this->renderFormGenerell(),
			'customer_fields_content' => $this->renderFormActiveFields('customer'),
			'address_fields_content' => $this->renderFormActiveFields('address'),
			'special_fields_content' => $this->renderFormActiveFields('special'),
			'positions_content' => $this->renderFormPosition(),
			'debug' => $debug,
		));
		if ($this->_is16()) {
            return $this->display($this->_path, '/views/templates/admin/admin.tpl');
        } else {
            return $this->display($this->_path, '/views/templates/admin/admin-1.5.tpl');
        }
	}

	private function renderFormGenerell()
	{
		$custype = array(array('value' => 2, 'name' => $this->l('Both'),),
			array('value' => 1, 'name' => $this->l('Customer'),),
			array('value' => 0, 'name' => $this->l('Guest'),),
		);
		$newsletter = array(array('value' => 2, 'name' => $this->l('Does not matter'),),
			array('value' => 1, 'name' => $this->l('With newsletter'),),
			array('value' => 0, 'name' => $this->l('Without newsletter'),),
		);
		$activeCus = array(array('value' => 2, 'name' => $this->l('All customers'),),
			array('value' => 1, 'name' => $this->l('Disabled only'),),
			array('value' => 0, 'name' => $this->l('Enabled only'),),
		);
		$switchValues = array(
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
					);
		$switchType = $this->_setSwitchType();

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
					'type' => $switchType,
					'label' => $this->l('Utf8 encoding'),
					'name' => 'PS_MOD_EXPCUS_UTF8',
					'hint' => $this->l('Set the encoding of the file'),
					'values' => $switchValues,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Merge delimiter (default: (space) )'),
					'name' => 'PS_MOD_EXPCUS_MERGE_DELIMITER',
					'hint' => $this->l('The merge delimiter to use in csv file'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Customer / Guest'),
					'name' => 'PS_MOD_EXPCUS_CUSTYPE',
					'hint' => $this->l('Select what type of customers to export'),
					'options' => array(
						'query' => $custype,
						'id' => 'value',
						'name' => 'name',
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Newline replacement'),
					'name' => 'PS_MOD_EXPCUS_RNT_REP',
					'hint' => $this->l('Replace newlines and tabs in fields with this string'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Newsletter'),
					'name' => 'PS_MOD_EXPCUS_NEWSLETTER',
					'hint' => $this->l('Select if export with newsletter or not'),
					'options' => array(
						'query' => $newsletter,
						'id' => 'value',
						'name' => 'name',
					),
				),
				array(
					'type' => $switchType,
					'label' => $this->l('Replace Gender ID'),
					'name' => 'PS_MOD_EXPCUS_GENDER_TEXT',
					'hint' => $this->l('Replace Gender id with the letters:').' M / F / N',
					'values' => $switchValues,
				),
				array(
					'type' => $switchType,
					'label' => $this->l('Replace Country ID'),
					'name' => 'PS_MOD_EXPCUS_COUNTRY_TEXT',
					'hint' => $this->l('Replace Country id with name using your language'),
					'values' => $switchValues,
				),
				array(
					'type' => 'select',
					'label' => $this->l('Active customers only'),
					'name' => 'PS_MOD_EXPCUS_CUS_ON',
					'hint' => $this->l('Export only active customers'),
					'options' => array(
						'query' => $activeCus,
						'id' => 'value',
						'name' => 'name',
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
        if (!$this->_is16()) {
            foreach ($fields_form['form']['input'] as &$array) {
                if ($array['type'] == "radio") {
                    $array['class'] = 't';
                }
                $array['desc'] = $array['hint'];
                unset($array['hint']);
            }
        }
		return $this->renderForm($fields_form, $submit);
	}

	private function renderFormActiveFields($type)
	{
		$switchType = $this->_setSwitchType();
		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`label` FROM `'._DB_PREFIX_.'export_customer_fields` WHERE `type` = \''.$type.'\'');
		foreach ($result as $field)
		{
			$input[] = array(
				'type' => $switchType,
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
        if (!$this->_is16()) {
            foreach ($fields_form['form']['input'] as &$array) {
                if ($array['type'] == "radio") {
                    $array['class'] = 't';
                }
                $array['desc'] = $array['hint'];
                unset($array['hint']);
            }
        }
		return $this->renderForm($fields_form, $submit);
	}

	public function renderFormPosition()
	{
        $input = array();
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
        if (!$this->_is16()) {
            foreach ($fields_form['form']['input'] as &$array) {
                if ($array['type'] == "radio") {
                    $array['class'] = 't';
                }
                $array['descr'] = $array['hint'];
                unset($array['hint']);
            }
        }
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
			$fields_value[$key] = (Configuration::get($key) !== false ? Configuration::get($key) : $value['value']);

		$result = Db::getInstance()->ExecuteS('SELECT `expcusfield`,`active`,`name`,`position` FROM `'._DB_PREFIX_.'export_customer_fields`');
		foreach ($result as $field)
		{
			$fields_value[$field['expcusfield'].'_switch'] = $field['active'];
			$fields_value[$field['expcusfield'].'_name'] = $field['name'];
			$fields_value[$field['expcusfield'].'_pos'] = $field['position'];
		}

		return $fields_value;
	}

	private function _setSwitchType()
	{
		if ($this->_is16())
			return 'switch';
		else
			return 'radio';
	}

	private function _is16()
	{
		if (version_compare(_PS_VERSION_, '1.6', '>=') >= 1)
			return true;

		return false;
	}
}
?>
