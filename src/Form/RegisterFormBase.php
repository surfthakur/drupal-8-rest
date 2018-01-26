<?php

namespace Drupal\vape\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\vape\DbService;
use Drupal\vape\Helper\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\surflogger\LoggerService;


class RegisterFormBase extends FormBase {
  
  /**
   * @var \Drupal\surflogger\LoggerService
   */
  protected $logger;
  
  /**
   * @var \Drupal\vape\DbService
   */
  protected $vapeDbService;
  
  /**
   * @var
   */
  protected $url;
  
  /**
   * @var
   */
  protected $response;
  
  /**
   * @var
   */
  protected $authToken;
  
  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;
  
  /**
   * RegisterFormBase constructor.
   *
   * @param \Drupal\surflogger\LoggerService           $loggerService
   * @param \Drupal\vape\DbService                     $dbService
   * @param \Drupal\Core\Session\AccountProxyInterface $accountProxy
   */
  public function __construct(
    LoggerService $loggerService,
    DbService $dbService,
    AccountProxyInterface $accountProxy
  ){
    $this->logger = $loggerService;
    $this->vapeDbService = $dbService;
    $this->currentUser = $accountProxy;
  }
  
  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container){
    return new static(
      $container->get('surf.logger'),
      $container->get('vape.dbservice'),
      $container->get('current_user')
    );
  }
  
  public function getFormId(){
    return Utils::VAPE_REGISTER_FORM_ID;
  }
  
  public function buildForm(array $form, FormStateInterface $form_state){
    
    kint(['authToken' => $this->authToken]);
    $form['type'] = [
      '#type'         => 'select',
      '#title'        => $this->t('type of connection'),
      '#options'      => $this->getApiOptions(),
      '#empty_option' => $this->t('-select-'),
      '#required'     => TRUE,
    ];
    
    if ($form_state->get('countrylist')) {
      $form['info'] = [
        '#type'         => 'select',
        '#title'        => $this->t('check this out'),
        '#options'      => $form_state->get('info'),
        '#empty_option' => $this->t('-select-'),
        '#required'     => TRUE,
      ];
    }
    if ($form_state->get('login')) {
      $form['email'] = [
        '#type'     => 'email',
        '#title'    => $this->t('Enter your email'),
        '#required' => TRUE,
      ];
      
      $form['password'] = [
        '#type'     => 'password',
        '#title'    => $this->t('Enter your password'),
        '#required' => TRUE,
      ];
    }
    $form['actions']['#type'] = 'actions';
    
    $form['actions']['submit'] = [
      '#type'        => 'submit',
      '#value'       => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    
    return $form;
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state){
    
    if ($this->authToken) {
      kint(['validate auth token', $this->authToken]);
    }
    
    parent::validateForm($form, $form_state);
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state){
    $type = $form_state->getValue('type');
    
    $this->url = Utils::getApiUrl() . $this->getApiByName($type);
    
    $this->response = NULL;
    
    if ($type == 'countries') {
      $response = $this->getCountries();
      
      $form_state->set('countrylist', $response);
    }
    
    if ($type == 'login') {
      
      $form_state->set('login', 'dfsdsd');
      $email = $form_state->getValue('email');
      $password = $form_state->getValue('password');
      kint(["email" => $email, "password" => $password]);
      
      if (isset($email) && isset($password)) {
        
        $response = $this->login($email, $password);
        kint($response);
        if ($response['meta']['status'] === 200 &&
          $response['meta']['message'] === "success") {
          $this->authToken = $response['data']['token'];
          $this->vapeDbService->setAccessToken(
            $this->currentUser,
            $this->authToken
          );
          
          $this->logger->info(
            "setting accessToken to ",
            ['accessToken' => $this->authToken]
          );
        }
      }
    }
    
    
    $form_state->setRebuild();
  }
  
  protected function login($email, $password){
    $result = Utils::curl(
      $this->url,
      [
        'email'    => $email,
        'password' => $password,
      ],
      'POST'
    );
    
    return $result;
  }
  
  protected function getCountries(){
    
    $token = NULL;
    if ($this->authToken) {
      $token = $this->authToken;
    }
    else {
      $token = $this->vapeDbService->getAccessToken($this->currentUser);
    }
    
    $token = $this->authToken;
    $result = Utils::curl(
      $this->url,
      [],
      'GET',
      ['Authorization: Bearer ' . $token]
    );
    
    if (isset($result['meta']['status']) == 200 && $result['meta']['message']) {
      $result = $result['data'];
    }
    
    
    return $result;
  }
  
  protected function getApiOptions(){
    $options = [];
    foreach ($this->getApis() as $api => $v) {
      $options[$api] = $api;
    }
    
    return $options;
  }
  
  protected function getApiByName($name){
    return $this->getApis()[$name];
  }
  
  protected function getApis(){
    return [
      'login'     => '/rest/V1/propcom/webservice/user/login',
      'register'  => '/rest/V1/propcom/webservice/user/',
      'countries' => '/rest/V1/propcom/webservice/address/countries',
    ];
  }
}