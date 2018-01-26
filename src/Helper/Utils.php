<?php

namespace Drupal\vape\Helper;

use Drupal\Component\Serialization\Json;

class Utils
{
	const VAPE_SETTING_FORM_ID = 'vape_api_admin_settings';
	const VAPE_SETTING_NAME = 'vape.settings';
	const VAPE_REGISTER_FORM_ID = 'vape_register_form';
	
	/**
	 * @param        $url
	 * @param array  $params
	 * @param string $method
	 * @param array  $header
	 * @return mixed
	 */
	public static function curl($url, $params = [], $method = "POST", $header = [])
	{
		try {
			curl_init();
			$data = $params;
			$data_string = Json::encode($data);
			$headers = [
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string),
			];
			$headers = array_merge($headers, $header);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($ch);
			curl_close($ch);
			$result = Json::decode($result);
			kint($result);
			return $result;
			
		} catch (\Exception $e) {
			
			var_dump($e->getMessage()); die();
		}
	}
	
	public static function getApiUrl()
	{
		return \Drupal::configFactory()->get(self::VAPE_SETTING_NAME)->getRawData()['url'];
	}
}