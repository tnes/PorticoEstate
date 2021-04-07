<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage cron
	 * @version $Id: sjekk_manglende_fakturamottak_BK.php 16075 2016-12-12 15:26:41Z sigurdne $
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default sjekk_manglende_fakturamottak_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');
	phpgw::import_class('phpgwapi.datetime');

	class sjekk_manglende_fakturamottak_BK extends property_cron_parent
	{

		var $connection,
			$soap_url,
			$soap_username,
			$soap_password,
			$config_invoice;

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('property');
			$this->function_msg	 = 'Manglende fakturamottak i Agresso';
			/**
			 * Bruker konffigurasjon fra '.ticket' - fordi denne definerer oppslaget mot fullmaktsregisteret ved bestilling.
			 */
			$config					 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.ticket'));
			$this->soap_url			 = $config->config_data['external_register']['url'];
			$this->soap_username	 = $config->config_data['external_register']['username'];
			$this->soap_password	 = $config->config_data['external_register']['password'];
			$this->config_invoice	 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			require_once PHPGW_SERVER_ROOT . '/property/inc/soap_client/agresso/autoload.php';
		}

		function execute()
		{
			$start = time();

			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/art
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/ansvar?id=013000
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/objekt?id=5001
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/prosjekt?id=5001
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/tjeneste?id=88010
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/leverandorer?leverandorNr=722920
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/manglendevaremottak


			/*
			 *
			 * Nye:
			 * agresso/ordre?id=
			 * agresso/bilag?id=
			 *
			 */


			if ($this->debug)
			{

			}
			set_time_limit(2000);

			try
			{
				$this->check_missing();
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][]	 = array('msg' => $msg);
		}

		function cron_log( $receipt = '' )
		{

			$insert_values = array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
			);

			$insert_values = $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
				. "VALUES ($insert_values)";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		private function check_missing()
		{
			$data = (array)$this->get_data();

			$this->check_if_received($data);
			$send = CreateObject('phpgwapi.send');

			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				_debug_array($data);
			}

			$subject = 'Manglende fakturamottak i Agresso';
			$from	 = "Ikke svar<IkkeSvar@Bergen.kommune.no>";
			$to		 = "Sigurd.Nes@bergen.kommune.no";
			$cc		 = "";
			$bcc	 = "";

			if ($data)
			{
				$body = '<pre>' . print_r($data, true) . '</pre>';
				try
				{
					$rc = $send->msg('email', $to, $subject, $body, '', $cc, $bcc, $from, '', 'html');
				}
				catch (Exception $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}
		}

		function check_if_received( &$data )
		{

			foreach ($data as &$valueset)
			{
				if(empty($valueset['voucher_no']))
				{
					continue;
				}
/*
           [tab] => A
            [account] => 123015
            [voucher_no] => 921097628
            [order_id] => 45045416
            [ext_inv_ref] => 100296
            [amount] => 545.03
            [step] => Forsinkelse til varer er mottatt
            [xwf_state] => Arbeidsflyt pågår
            [apar_id] => 100497
            [xapar_id] => BERGEN ELEKTROSERVICE AS
            [voucher_date] => 2021-03-17T00:00:00+01:00
            [due_date] => 2021-04-30T00:00:00+02:00
*/



				$sql	 = <<<SQL
					SELECT fakturamottak.regtid
					FROM ( SELECT  fm_ecobilagoverf.regtid
						FROM fm_ecobilagoverf WHERE external_voucher_id = '{$valueset['voucher_no']}'
							UNION ALL
						SELECT fm_ecobilag.regtid
						FROM fm_ecobilag WHERE external_voucher_id = '{$valueset['voucher_no']}'
						 ) fakturamottak
SQL;
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$regtid	 = $this->db->f('regtid');

				$valueset['Mottatt_fra_Agresso'] = $regtid ? $regtid : 'Ikke mottatt';


				if($regtid)
				{
					$date	 = new DateTime($regtid);
					if( (time() - $date->getTimestamp() ) > (24*3600))
					{
						$fil_katalog = $this->config_invoice->config_data['export']['path'];
						
						$filename = "{$fil_katalog}/V3_varemottak_{$valueset['order_id']}_{$valueset['voucher_no']}.xml";

						//Sjekk om filen eksisterer
						if (file_exists($filename))
						{
							if($this->transfer($filename))
							{
								$valueset['Filnavn'] = $filename;
								$valueset['overført på nytt'] = date('Y-m-d H:i:s', time() + phpgwapi_datetime::user_timezone());
							}
						}
					}
				}
			}
		}

		function transfer($filename)
		{
			$content = file_get_contents($filename);

			$transfer_ok = false;

			if (!$debug && ($this->config_invoice->config_data['common']['method'] == 'ftp' || $this->config_invoice->config_data['common']['method'] == 'ssh'))
			{
				if (!$connection = $this->connection)
				{
					$connection = $this->phpftp_connect();
				}

				$basedir = $this->config_invoice->config_data['export']['remote_basedir'];
				if ($basedir)
				{
					$remote_file = $basedir . '/' . basename($filename);
				}
				else
				{
					$remote_file = basename($filename);
				}

				switch ($this->config_invoice->config_data['common']['method'])
				{
					case 'ftp';
						$tmp		 = tmpfile();
						fwrite($tmp, $content);
						rewind($tmp);
						$transfer_ok = ftp_fput($connection, $remote_file, $tmp, FTP_BINARY);
						fclose($tmp);
						//	$transfer_ok = ftp_put($connection, $remote_file, $filename, FTP_BINARY);
						break;
					case 'ssh';
						$sftp		 = ssh2_sftp($connection);
						$stream		 = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
						fwrite($stream, $content);
						$transfer_ok = @fclose($stream);
						break;
					default:
						$transfer_ok = false;
				}
			}
			return $transfer_ok;
		}

		function phpftp_connect()
		{
			$server		 = $this->config_invoice->config_data['common']['host'];
			$user		 = $this->config_invoice->config_data['common']['user'];
			$password	 = $this->config_invoice->config_data['common']['password'];
			$port		 = 22;

			switch ($this->config_invoice->config_data['common']['method'])
			{
				case 'ftp';
					if ($connection = ftp_connect($server))
					{
						ftp_login($connection, $user, $password);
					}
					break;
				case 'ssh';
					if (!function_exists("ssh2_connect"))
					{
						die("function ssh2_connect doesn't exist");
					}
					if (!($connection = ssh2_connect("$server", $port)))
					{
						$message = "fail: unable to establish connection";
						_debug_array($message);
						//$receipt['error'][]= array('msg' => $message);
					}
					else
					{
						// try to authenticate with username root, password secretpassword
						if (!ssh2_auth_password($connection, $user, $password))
						{
							$message = "fail: unable to authenticate";
							_debug_array($message);
							//$receipt['error'][]= array('msg' => $message);
						}
					}
					break;
			}
			$this->connection = $connection;
			return $connection;
		}

		/**
		 * Curl-versjonen, via tjeneste hos SDI
		 * @return array
		 * @throws Exception
		 */
		function get_data_old()
		{
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/manglendevaremottak
			$url		 = "{$this->soap_url}/manglendevaremottak";
			$username	 = $this->soap_username; //'portico';
			$password	 = $this->soap_password; //'********';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (!$httpCode)
			{
				throw new Exception("No connection: {$url}");
			}
			curl_close($ch);

			$result = json_decode($result, true);

			return $result;
		}


		/**
		 * Ny spørring direkte i Agresso/UBW
		 * @return array
		 */
		function get_data()
		{
			$this->debug = false;

			$username				 = 'WEBSER';
			$password				 = 'wser10';
			$client					 = 'BY';

			$TemplateId				 = '12770'; //Spørring på varemottak


			$service	 = new \QueryEngineV201101(array(
				'trace' => 1,
				'location' => $this->soap_url,
				'uri'		=> 'http://services.agresso.com/QueryEngineService/QueryEngineV201101'
				),$this->soap_url);
			$Credentials = new \WSCredentials();
			$Credentials->setUsername($username);
			$Credentials->setPassword($password);
			$Credentials->setClient($client);

			// Get the default settings for a template (templateId)
			try
			{
				$searchProp = $service->GetSearchCriteria(new \GetSearchCriteria($TemplateId, true, $Credentials));
				if ($this->debug)
				{
					echo "SOAP HEADERS:\n" . $service->__getLastRequestHeaders() . PHP_EOL;
					echo "SOAP REQUEST:\n" . $service->__getLastRequest() . PHP_EOL;
				}
			}
			catch (SoapFault $fault)
			{
				echo '<pre>';
				print_r($fault);
				echo '</pre>';
				$msg = "SOAP Fault:\n faultcode: {$fault->faultcode},\n faultstring: {$fault->faultstring}";
				echo $msg . PHP_EOL;
				trigger_error(nl2br($msg), E_USER_ERROR);
			}

			//Kriterier
			//		_debug_array($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties());

			$input									 = new InputForTemplateResult($TemplateId);
			$options								 = $service->GetTemplateResultOptions(new \GetTemplateResultOptions($Credentials));

//			_debug_array($options);
			$options->RemoveHiddenColumns			 = true;
			$options->ShowDescriptions				 = true;
			$options->Aggregated					 = false;
			$options->OverrideAggregation			 = false;
			$options->CalculateFormulas				 = false;
			$options->FormatAlternativeBreakColumns	 = false;
			$options->FirstRecord					 = false;
			$options->LastRecord					 = false;

			$input->setTemplateResultOptions($options);
			// Get new values to SearchCriteria (if that’s what you want to do
			$input->setSearchCriteriaPropertiesList($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList());
			//Retrieve result

			$result = $service->GetTemplateResultAsDataSet(new \GetTemplateResultAsDataSet($input, $Credentials));

			$data = $result->getGetTemplateResultAsDataSetResult()->getTemplateResult()->getAny();
			if ($this->debug)
			{
				echo "SOAP HEADERS:\n" . $service->__getLastRequestHeaders() . PHP_EOL;
				echo "SOAP REQUEST:\n" . $service->__getLastRequest() . PHP_EOL;
			}

			$xmlparse	 = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result	 = $xmlparse->parse($data);

			if ($var_result)
			{
				$ret = $var_result['Agresso'][0]['AgressoQE'];
			}
			else
			{
				$ret = array();
			}

			return $ret;
		}
	}