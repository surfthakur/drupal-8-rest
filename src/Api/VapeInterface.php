<?php

namespace Drupal\vape\Api;

Interface VapeInterface
{
	public function login($username, $password);
	
	public function register($first_name, $last_name, $email, $password, $confirm);
	
	public function get_countries();
}