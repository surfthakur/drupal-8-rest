<?php

namespace Drupal\vape\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

class apiSettingsForm extends ConfirmFormBase
{
	const VAPE_SETTING_FORM_ID = 'vape_api_admin_settings';
	const VAPE_SETTING_NAME = 'vape.settings';
	
	public function getFormId()
	{
		return self::VAPE_SETTING_FORM_ID;
	}
	
	public function buildForm(array $form, FormStateInterface $form_state)
	{
		$config = $this->config(self::VAPE_SETTING_NAME);
		
		$form['url'] = [
			'#type'          => 'textfield',
			'#title'         => $this->t('Api URL'),
			'#default_value' => $config->get('url'),
		];
		
		return parent::buildForm($form, $form_state);
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state)
	{
		try {
			$this->configFactory()
				->getEditable(self::VAPE_SETTING_NAME)
				->set(
					'url',
					$form_state->getValue('url')
				)->save();
		} catch (\Exception $e) {
			var_dump($e->getMessage());
		}
	}
	
	public function getCancelUrl()
	{
	
	}
	
	public function getQuestion()
	{
	
	}
}