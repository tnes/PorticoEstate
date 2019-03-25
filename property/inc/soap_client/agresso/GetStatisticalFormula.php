<?php

	class GetStatisticalFormula
	{

		/**
		 * @var int $templateId
		 */
		protected $templateId = null;

		/**
		 * @var WSCredentials $credentials
		 */
		protected $credentials = null;

		/**
		 * @param int $templateId
		 * @param WSCredentials $credentials
		 */
		public function __construct( $templateId, $credentials )
		{
			$this->templateId	 = $templateId;
			$this->credentials	 = $credentials;
		}

		/**
		 * @return int
		 */
		public function getTemplateId()
		{
			return $this->templateId;
		}

		/**
		 * @param int $templateId
		 * @return GetStatisticalFormula
		 */
		public function setTemplateId( $templateId )
		{
			$this->templateId = $templateId;
			return $this;
		}

		/**
		 * @return WSCredentials
		 */
		public function getCredentials()
		{
			return $this->credentials;
		}

		/**
		 * @param WSCredentials $credentials
		 * @return GetStatisticalFormula
		 */
		public function setCredentials( $credentials )
		{
			$this->credentials = $credentials;
			return $this;
		}
	}