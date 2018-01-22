<?php

namespace Drupal\vape\Form;


class RegisterFormBase extends AbstractFormBase
{
	const VAPE_REGISTER_FORM_ID = 'vape_register_form';
	
	public function getFormId()
	{
		return self::VAPE_REGISTER_FORM_ID;
	}
	
	protected function getApisByName($name)
	{
		return $this->getApis()[$name]['url'];
	}
	
	protected function getApis()
	{
		return [
			'login' => [
				'url' => '/rest/V1/propcom/webservice/user/login',
			],
			
			'register' => [
				'url' => '/rest/V1/propcom/webservice/user/',
			],
			
			'countries' => [
				'url' => '/rest/V1/propcom/webservice/address/countries',
			],
		];
	}
}