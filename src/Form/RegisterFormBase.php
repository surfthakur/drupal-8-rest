<?php

namespace Drupal\vape\Form;

use Drupal\Core\Form\FormStateInterface;
use \Drupal\vape\Helper\Utils;

class RegisterFormBase extends AbstractFormBase
{
	public function getFormId()
	{
		return Utils::VAPE_REGISTER_FORM_ID;
	}
	
	public function buildForm(array $form, FormStateInterface $form_state)
	{
		
		// Select.
		$form['type'] = [
			'#type'         => 'select',
			'#title'        => $this->t('type of connection'),
			'#options'      => $this->getApis(),
			'#empty_option' => $this->t('-select-'),
			'#required'     => true,
		];
		
		
		$form['actions']['#type'] = 'actions';
		
		$form['actions']['submit'] = [
			'#type'        => 'submit',
			'#value'       => $this->t('Submit'),
			'#button_type' => 'primary',
		];
		
		return parent::buildForm($form, $form_state);
	}
	
	protected function getApiOptions()
	{
		$options = [];
		
		foreach ($this->getApis() as $k => $api) {
			$options[$api] = $k;
		}
		
		return $options;
	}
	
	
	protected function getApis()
	{
		return [
			'/rest/V1/propcom/webservice/user/login'        => 'login',
			'/rest/V1/propcom/webservice/user/'             => 'register',
			'/rest/V1/propcom/webservice/address/countries' => 'countries',
		];
	}
}