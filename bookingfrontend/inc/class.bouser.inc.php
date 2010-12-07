<?php
	class bookingfrontend_bouser
	{
		const ORGNR_SESSION_KEY = 'orgnr';
		
		public
			$orgnr = null;

		protected
			$default_module = 'bookingfrontend',
			$module;
		
		/**
		 * Debug for testing
		 * @access public
		 * @var bool
		 */
		public $debug = false;

		public function __construct() {
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
		}
		
		protected function set_module($module = null)
		{
			$this->module = is_string($module) ? $module : $this->default_module;
		}
		
		public function get_module()
		{
			return $this->module;
		}
		
		public function log_in()
		{
			$this->log_off();
			$this->orgnr = $this->get_user_orgnr_from_auth_header();

/*
			try 
			{
				createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($this->orgnr);
			}
			catch (sfValidatorError $e)
			{
//				return null;
			}
*/
			if ($this->is_logged_in())
			{
				$this->write_user_orgnr_to_session();
			}

			if($this->debug)
			{
				echo 'is_logged_in():<br>';
				_debug_array($this->is_logged_in());
				echo 'Session:<br>';
				_debug_array($_SESSION);
				die();
			}

			return $this->is_logged_in();
		}
		
		public function log_off()
		{
			$this->clear_user_orgnr();
			$this->clear_user_orgnr_from_session();
		}
		
		protected function clear_user_orgnr()
		{
			$this->orgnr = null;
		}
		
		public function get_user_orgnr()
		{
			if(!$this->orgnr)
			{
				$this->orgnr = $this->get_user_orgnr_from_session();
			}
			return $this->orgnr;
		}
		
		public function is_logged_in()
		{
			return !!$this->get_user_orgnr();
		}
		
		public function is_organization_admin($organization_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
				//return false;
			}
			$so = CreateObject('booking.soorganization');
			$organization = $so->read_single($organization_id);

			if ($organization['organization_number'] == '')
			{
				return false;
			}

			return $organization['organization_number'] == $this->orgnr;
		}

		public function is_group_admin($group_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
				//return false;
			}
			$so = CreateObject('booking.sogroup');
			$group = $so->read_single($group_id);
			return $this->is_organization_admin($group['organization_id']);
		}
		
		protected function write_user_orgnr_to_session()
		{
			if (!$this->is_logged_in())
			{
				throw new LogicException('Cannot write orgnr to session unless user is logged on');
			}

			phpgwapi_cache::session_set($this->get_module(), self::ORGNR_SESSION_KEY, $this->get_user_orgnr());
		}
		
		protected function clear_user_orgnr_from_session()
		{
			phpgwapi_cache::session_clear($this->get_module(), self::ORGNR_SESSION_KEY);
		}
		
		protected function get_user_orgnr_from_session()
		{
			try {
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean(phpgwapi_cache::session_get($this->get_module(), self::ORGNR_SESSION_KEY));
			} catch (sfValidatorError $e) {
				return null;
			}
		}
		
		protected function get_user_orgnr_from_auth_header()
		{
			$config		= CreateObject('phpgwapi.config','bookingfrontend');
			$config->read();

			$header_key = isset($config->config_data['header_key']) && $config->config_data['header_key'] ? $config->config_data['header_key'] : 'Osso-User-Dn';
			$header_regular_expression = isset($config->config_data['header_regular_expression']) && $config->config_data['header_regular_expression'] ? $config->config_data['header_regular_expression'] : '/^cn=(.*),cn=users.*$/';

			$headers = getallheaders();

			if(isset($config->config_data['debug']) && $config->config_data['debug'])
			{
				$this->debug = true;
				echo 'headers:<br>';
				_debug_array($headers);
			}

			if(isset($headers[$header_key]) && $headers[$header_key])
			{
				$matches = array();
				preg_match_all($header_regular_expression,$headers[$header_key], $matches);
				$userid = $matches[1][0];

				if($this->debug)
				{
					echo 'matches:<br>';
					_debug_array($matches);
				}

			}

			$options = array();
			$options['soap_version'] = SOAP_1_1;
			$options['location']	= isset($config->config_data['soap_location']) && $config->config_data['soap_location'] ? $config->config_data['soap_location'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1';
			$options['uri']			= isset($config->config_data['soap_uri']) && $config->config_data['soap_uri'] ? $config->config_data['soap_uri'] : '';// 'http://soat1a.srv.bergenkom.no';
			$options['trace']		= 1;

			if(isset($config->config_data['soap_proxy_host']) && $config->config_data['soap_proxy_host'])
			{
				$options['proxy_host']	= $config->config_data['soap_proxy_host'];
			}

			if(isset($config->config_data['soap_proxy_port']) && $config->config_data['soap_proxy_port'])
			{
				$options['proxy_port']	= $config->config_data['soap_proxy_port'];
			}
			$options['encoding']	= isset($config->config_data['soap_encoding']) && $config->config_data['soap_encoding'] ? $config->config_data['soap_encoding'] : 'UTF-8';
			$options['login']		= isset($config->config_data['soap_login']) && $config->config_data['soap_login'] ? $config->config_data['soap_login'] : '';
			$options['password']	= isset($config->config_data['soap_password']) && $config->config_data['soap_password'] ? $config->config_data['soap_password'] : '';

			$wsdl = isset($config->config_data['soap_wsdl']) && $config->config_data['soap_wsdl'] ? $config->config_data['soap_wsdl'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1?wsdl';

			$authentication_method	= isset($config->config_data['authentication_method']) && $config->config_data['authentication_method'] ? $config->config_data['authentication_method'] : '';

			require_once PHPGW_SERVER_ROOT."/bookingfrontend/inc/custom/default/{$authentication_method}";
			
			$external_user = new booking_external_user($wsdl, $options, $userid, $this->debug);
			// test values
			//$external_user = (object) 'ciao'; $external_user->login = 994239929;

			if($this->debug)
			{
				echo 'External user:<br>';
				_debug_array($external_user);
			}
			try
			{
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($external_user->login);
			}
			catch (sfValidatorError $e)
			{
				if($this->debug)
				{
					echo $e->getMessage();
					die();
				}
				return null;
			}
		}
	}
