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

/** Open Science Dashboard Controller */
class Openscience_DashboardController extends Openscience_AppController
{

  public $_models = array('User', 'Item', 'Folder');
  public $_daos = array('Item', 'Folder');
  public $_components = array('Utility');
  public $_forms = array();

  public $_moduleDaos = array('Anatomicalarea', 'Algorithm', 'Resultset');
  public $_moduleModels = array('Anatomicalarea', 'Algorithm', 'Resultset');
  public $_moduleComponents = array('LinkDataToResults');
  public $_moduleForms = array();

  /**
   * @method initAction()
   *  Index Action (first action when we access the application)
   */
  function init()
    {
    } // end method indexAction

  /** index action*/
  function indexAction()
    {
    $this->view->header = "Open Science Dashboard";
    $this->view->areas = $this->Openscience_Anatomicalarea->getAll();    
    }

  function areaAction()
    {
    $id = $this->_getParam('id');
    $this->view->header = "Open Science Dashboard";
    $area = $this->Openscience_Anatomicalarea->load($id);
    $this->view->area = $area;
    $this->view->algorithms = $area->getAlgorithms();
    }

  function algorithmAction()
    {
    $id = $this->_getParam('id');
    $this->view->header = "Open Science Dashboard";
    $algorithm = $this->Openscience_Algorithm->load($id);
    $resultsets = $this->Openscience_Algorithm->getLatestsResultsets($algorithm);
    $this->view->algorithm = $algorithm;
    $this->view->resultsets = $resultsets;
    }

  function detailAction()
    {
    $resultsetId = $this->_getParam('resultsetId');
    $detail = $this->Openscience_Resultset->load($resultsetId);
    $componentLoader = new MIDAS_ComponentLoader();
    $linkComponent = $componentLoader->loadComponent('LinkDataToResults',
                                                     'openscience');
    $jsonComponent = $componentLoader->loadComponent('Json');
    $contentArray = $jsonComponent::decode($detail->getContents());
    $this->view->results = $contentArray;
    $this->view->date = $detail->getDate();
    }

}//end class
