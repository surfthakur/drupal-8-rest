<?php

namespace Drupal\vape\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\vape\Helper\Utils;

class RegisterFormBase extends AbstractFormBase
{
	public function getFormId()
	{
		return Utils::VAPE_REGISTER_FORM_ID;
	}
	
	public function buildForm(array $form, FormStateInterface $form_state)
	{
		
		
		$form['type'] = [
			'#type'         => 'select',
			'#title'        => $this->t('type of connection'),
			'#options'      => $this->getApiOptions(),
			'#empty_option' => $this->t('-select-'),
			'#required'     => true,
		];
		
		if($form_state->get('info'))
		{
			$form['info'] = [
				'#type'         => 'select',
				'#title'        => $this->t('check this out'),
				'#options'      => $form_state->get('info'),
				'#empty_option' => $this->t('-select-'),
				'#required'     => true,
			];
		}
		
		$form['actions']['#type'] = 'actions';
		
		$form['actions']['submit'] = [
			'#type'        => 'submit',
			'#value'       => $this->t('Submit'),
			'#button_type' => 'primary',
		];
		
		return parent::buildForm($form, $form_state);
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state)
	{
		$type = $form_state->getValue('type');
		
		$url = Utils::getApiUrl() . $this->getApiByName($type);
		
		$response = "no result";
		
		if ($type == 'countries') {
			$result = Utils::curl($url, [], 'GET', ['Authorization: Bearer TOKEN_GOES_HERE']);
			$response = [];
			foreach ($result['data'] as $item) {
				$response[$item['value']] = $item['label'];
			}
		}
		
		
		$form_state->set('info',$response);
		$form_state->setRebuild();
	}
	
	protected function getApiOptions()
	{
		$options = [];
		foreach ($this->getApis() as $api => $v) {
			$options[$api] = $api;
		}
		
		return $options;
	}
	
	protected function getApiByName($name)
	{
		return $this->getApis()[$name];
	}
	
	protected function getApis()
	{
		return [
			'login'     => '/rest/V1/propcom/webservice/user/login',
			'register'  => '/rest/V1/propcom/webservice/user/',
			'countries' => '/rest/V1/propcom/webservice/address/countries',
		];
	}
}