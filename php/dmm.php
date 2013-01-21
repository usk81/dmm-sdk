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
	public function api($params=array(), $format='xml')
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
	 * [_apiRequest description]
	 * @param  string $url    request url
	 * @param  arrat  $params post parameters
	 * @param  object $ch     [description]
	 * @return mixed
	 */
	protected function _apiRequest($url, $params=NULL, $ch=NULL)
	{
		if(!$ch)
		{
			$ch = curl_init();
		}

		$opt = self::$CURL_OPTS;
		if($params)
		{
			$opts[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');	
		}
		$opts[CURLOPT_URL] = $url;

		if(isset($opts[CURLOPT_HTTPHEADER]))
		{
			$existing_headers = $opts[CURLOPT_HTTPHEADER];
			$existing_headers[] = 'Expect:';
			$opts[CURLOPT_HTTPHEADER] = $existing_headers;
		}
		else
		{
			$opts[CURLOPT_HTTPHEADER] = array('Expect:');
		}

		curl_setopt_array($ch, $opts);
		$result = curl_exec($ch);

		curl_close($ch);
		return $result;
	}

	/* Private Methods
	-------------------------------*/
}