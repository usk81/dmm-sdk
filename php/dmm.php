<?php
/**
 * [class description]
 *
 * @category Library
 */
class Dmm
{
	/* Constants
	-------------------------------*/
	const SDK_VERSION = '0.0.1';

	/* Public Properties
	-------------------------------*/
	public static $CURL_OPTS = array(
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_TIMEOUT        => 60,
		CURLOPT_USERAGENT      => 'dmm-sdk-0.0.1',
	);

	/* Protected Properties
	-------------------------------*/
	protected $_apiUrl      = 'http://affiliate-api.dmm.com/';
	protected $_appId       = '';
	protected $_affiliateId = '';
	protected $_operation   = 'ItemList';
	protected $_version     = '2.00';
	protected $_site        = 'DMM.com';
	protected $_service     = '';
	protected $_floor       = '';
	protected $_hits        = 20;
	protected $_sort        = 'rank';
	protected $_params      = array();

	/* Private Properties
	-------------------------------*/
	/* Magic Methods
	-------------------------------*/
	public function __construct($config)
	{
		if(!isset($config['appId']) || !isset($config['affiliateId']))
		{
			return;
		}

		$this->setAppId($config['appId']);
		$this->setAffiliateId($config['affiliateId']);

		date_default_timezone_set('Asia/Tokyo');
	}

	/* Public Methods
	-------------------------------*/
	/**
	 * Set the Application ID.
	 * @param  string $appId The Application ID
	 * @return AbstractDmm
	 */
	public function setAppId($appId)
	{
		$this->_appId = $appId;
		return $this;
	}

	/**
	 * Set the Affiliate ID.
	 * @param  string $affiliateId The Affiliate ID
	 * @return AbstractDmm
	 */
	public function setAffiliateId($affiliateId)
	{
		$this->_affiliateId = $affiliateId;
		return $this;
	}

	/**
	 * Set the API Version.
	 * @param  floor $version The API Version
	 * @return AbstractDmm
	 */
	public function setVersion($version)
	{
		$this->_version = $version;
		return $this;
	}

	/**
	 * Get the Application ID.
	 * @return string The Application ID
	 */
	public function getAppId()
	{
		return $this->_appId;
	}

	/**
	 * Get the Affiliate ID.
	 * @return string The Affiliate ID
	 */
	public function getAffiliateId()
	{
		return $this->_affiliateId;
	}

	/**
	 * Get the API Version.
	 * @return string The API Version
	 */
	public function getVersion()
	{
		return $this->_version;
	}

	/**
	 * set API parameters
	 * @param array $params API parameters
	 * @return  Dmm
	 */
	public function setParameters($params)
	{
		$this->_params = array_merge($this->_params, $params);
		return $this;
	}

	/**
	 * get API parameters
	 * @return array API parameters
	 */
	public function getParameters()
	{
		return $this->_params;
	}

	/**
	 * API request
	 * @param  array  $params API parameters
	 * @param  string $format Response format
	 * @return mixed API response
	 */
	public function api($params=array(), $format='array')
	{
		// default parameters
		$this->setParameters(array(
			'api_id'       => $this->getAppId(),
			'affiliate_id' => $this->getAffiliateId(),
			'operation'    => $this->_operation,
			'version'      => $this->getVersion(),
			'timestamp'    => date('Y-m-d H:i:s'),
			'site'         => $this->_site,
			'hits'         => $this->_hits,
			'sort'         => $this->_sort,
		));

		$this->setParameters($params);

		$api_response = $this->_apiRequest(
			$this->_getUrl($this->getParameters())
		);

		return $api_response;
	}

	/* Protected Methods
	-------------------------------*/
	/**
	 * [_getUrl description]
	 * @param  array  $params [description]
	 * @return [type]
	 */
	protected function _getUrl($params=array())
	{
		$url = $this->_apiUrl;
		if($params)
		{
			$url .= '?' . http_build_query($params, NULL, '&');
		}

		return $url;
	}

	/**
	 * API Request
	 *
	 * This function does not use cURL.
	 * Because DMM API response format is only XML.
	 * 
	 * @param  string $url request url
	 * @return mixed
	 */
	protected function _apiRequest($url)
	{
		if(! $response = file_get_contents($url))
		{
			return array('message' => 'Can not get response');
		}

		// encoding from EUC-JP to UTF-8
		mb_convert_encoding($response,"UTF-8");

		$xml = simplexml_load_string($response);

		return json_decode(json_encode($xml), TRUE);
	}

	/* Private Methods
	-------------------------------*/
}