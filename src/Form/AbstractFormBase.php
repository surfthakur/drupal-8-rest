<?php

namespace Drupal\vape\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

abstract class AbstractFormBase extends FormBase
{
	public function buildForm(array $form, FormStateInterface $form_state)
	{
		return $form;
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state)
	{
	
	}
}