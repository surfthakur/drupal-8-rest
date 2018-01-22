<?php

namespace Drupal\vape\Helper;

use Drupal\Component\Serialization\Json;

class Utils
{
	/**
	 * @param        $url
	 * @param array  $params
	 * @param string $method
	 * @param array  $header
	 * @return mixed
	 */
	public function curl($url, $params = [], $method = "POST", $header = [])
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
			
			return $result;
		} catch (\Exception $e) {
			
			var_dump($e->getMessage());
		}
	}
}