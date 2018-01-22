<?php

namespace Drupal\vape\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\vape\Helper\Utils;

class ApiSettingsForm extends ConfirmFormBase
{
	public function getFormId()
	{
		return Utils::VAPE_SETTING_FORM_ID;
	}
	
	public function buildForm(array $form, FormStateInterface $form_state)
	{
		$config = $this->config(Utils::VAPE_SETTING_NAME);
		
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
				->getEditable(Utils::VAPE_SETTING_NAME)
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