<?php
/*=========================================================================
MIDAS Server
Copyright (c) Kitware SAS. 20 rue de la Villette. All rights reserved.
69328 Lyon, FRANCE.

See Copyright.txt for details.
This software is distributed WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.  See the above copyright notices for more information.
=========================================================================*/

/** Tests the functionality of the web API methods */
class ApiControllerTest extends ControllerTestCase
  {

  /** set up tests */
  public function setUp()
    {
    $this->setupDatabase(array('default')); //core dataset
    $this->setupDatabase(array('default'), 'api'); // module dataset
    $this->setupDatabase(array('default'), 'validation'); // module dataset
    $this->enabledModules = array('api', 'validation');
    $this->_models = array('User', 'Folder');
    $this->_daos = array('User', 'Folder');

    parent::setUp();
    }

  /** Invoke the JSON web API */
  private function _callJsonApi($sessionUser = null)
    {
    $this->dispatchUrI($this->webroot.'api/json', $sessionUser);
    return json_decode($this->getBody());
    }

  /** Make sure we got a good response from a web API call */
  private function _assertStatusOk($resp)
    {
    $this->assertNotEquals($resp, false);
    $this->assertEquals($resp->message, '');
    $this->assertEquals($resp->stat, 'ok');
    $this->assertEquals($resp->code, 0);
    $this->assertTrue(isset($resp->data));
    }

  /** Test to see that the response is bad (for testing exceptional cases) */
  private function _assertStatusFailed($resp)
    {
    $this->assertEquals($resp->stat, "fail");
    $this->assertEquals($resp->code, -1);
    }

  /** Authenticate using the default api key */
  private function _loginUsingApiKey()
    {
    $usersFile = $this->loadData('User', 'default');
    $userDao = $this->User->load($usersFile[0]->getKey());

    $modelLoad = new MIDAS_ModelLoader();
    $userApiModel = $modelLoad->loadModel('Userapi', 'api');
    $userApiModel->createDefaultApiKey($userDao);
    $apiKey = $userApiModel->getByAppAndUser('Default', $userDao)->getApikey();

    $this->params['method'] = 'midas.login';
    $this->params['email'] = $usersFile[0]->getEmail();
    $this->params['appname'] = 'Default';
    $this->params['apikey'] = $apiKey;
    $this->request->setMethod('POST');

    $resp = $this->_callJsonApi();
    $this->_assertStatusOk($resp);
    $this->assertEquals(strlen($resp->data->token), 40);

    // **IMPORTANT** This will clear any params that were set before this
    // function was called
    $this->resetAll();
    return $resp->data->token;
    }

  /** Authenticate using the default api key */
  private function _loginUsingApiKeyAsAdmin()
    {
    $usersFile = $this->loadData('User', 'default');
    $userDao = $this->User->load($usersFile[0]->getKey());
    $userDao->setAdmin(1);
    $this->User->save($userDao);

    $modelLoad = new MIDAS_ModelLoader();
    $userApiModel = $modelLoad->loadModel('Userapi', 'api');
    $userApiModel->createDefaultApiKey($userDao);
    $apiKey = $userApiModel->getByAppAndUser('Default', $userDao)->getApikey();

    $this->params['method'] = 'midas.login';
    $this->params['email'] = $usersFile[0]->getEmail();
    $this->params['appname'] = 'Default';
    $this->params['apikey'] = $apiKey;
    $this->request->setMethod('POST');

    $resp = $this->_callJsonApi();
    $this->_assertStatusOk($resp);
    $this->assertEquals(strlen($resp->data->token), 40);

    // **IMPORTANT** This will clear any params that were set before this
    // function was called
    $this->resetAll();
    return $resp->data->token;
    }

} // End of ApiControllerTest
