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
	const REFER_NOMAL = 'DMM.com';
	const REFER_ADULT = 'DMM.co.jp';

	/* Public Properties
	-------------------------------*/
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
	 * @return Dmm
	 */
	public function setAppId($appId)
	{
		$this->_appId = $appId;
		return $this;
	}

	/**
	 * Set the Affiliate ID.
	 * @param  string $affiliateId The Affiliate ID
	 * @return Dmm
	 */
	public function setAffiliateId($affiliateId)
	{
		$this->_affiliateId = $affiliateId;
		return $this;
	}

	/**
	 * Set the API Version.
	 * @param  floor $version The API Version
	 * @return Dmm
	 */
	public function setVersion($version)
	{
		$this->_version = $version;
		return $this;
	}

	/**
	 * Set Keyword parameter.
	 *
	 * encoding to EUC-JP & set keyword parameter
	 * DMM API request's encode is only EUC-JP
	 *
	 * @param  string $string keyword parameter
	 * @return Dmm
	 */
	public function setKeyword($string)
	{
		if(!empty($string) && is_string($string))
		{
			$params = array(
				'keyword' => mb_convert_encoding($string, 'EUC-JP')
			);
			$this->setParameters($params);
		}
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
	 * Get Keyword parameter.
	 *
	 * get keyword parameter & encoding to UTF-8
	 * DMM API response's encode is EUC-JP
	 *
	 * @return string Keyword parameter
	 */
	public function getKeyword()
	{
		$params = $this->_params;
		return (isset($params['keyword'])) ? mb_convert_encoding($params['keyword'], 'UTF-8') : NULL;
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
	 * set the refer site. (Adult items)
	 * @return Dmm
	 */
	public function referAdult()
	{
		$this->_site = static::REFER_ADULT;
		return $this;
	}

	/**
	 * set the refer site. (Nomal items)
	 * @return Dmm
	 */
	public function referNomal()
	{
		$this->_site = static::REFER_NOMAL;
		return $this;
	}

	/**
	 * API request
	 * @param  array  $params API parameters
	 * @param  string $to_format Response format
	 * @return mixed API response
	 */
	public function api($params=array(), $to_format='array')
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

		if($to_format == 'json')
		{
			$api_response = json_encode($api_response);
		}

		return $this->_formatResponse($api_response, $to_format);
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

		$result = $this->_remapArray(simplexml_load_string($response));
		return (isset($result['result'])) ? $result['result'] : $result;
	}

	/* Private Methods
	-------------------------------*/
	/**
	 * encode object to array & remap
	 * @param  object $obj xml element 
	 * @return array
	 */
	private function _remapArray($obj)
	{
		$arr = array();
		if(is_object($obj))
		{
			$obj = get_object_vars($obj);
		}

		foreach($obj as $key => $val)
		{
			if(is_object($obj[$key]))
			{
				$arr[$key] = $this->_remapArray($val);
			}
			elseif(is_array($val))
			{
				foreach($val as $k => $v)
				{
					if(is_object($v) || is_array($v))
					{
						$arr[$key][$k] = $this->_remapArray($v);
					}
					else
					{
						$arr[$key][$k] = $v;
					}
				}
			}
			else
			{
				$arr[$key] = $val;
			}
		}
		return $arr;
	}

	/**
	 * format response parameter
	 * @param  mixed  $params
	 * @param  string $to_format 
	 * @return mixed
	 */
	private function _formatResponse($params, $to_format='array')
	{
		if($to_format == 'json')
		{
			$result = json_encode($params);
		}
		else
		{
			if(is_object($params))
			{
				$result = $this->_remapArray($params);
			}
			else
			{
				$result = $params;
			}
		}
		return $result;
	}
}