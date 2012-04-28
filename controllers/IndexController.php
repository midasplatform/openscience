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

/** Open Science Index Controller */
class Openscience_IndexController extends Openscience_AppController
{

  public $_models = array('User', 'Item', 'Folder');
  public $_daos = array('Item', 'Folder');
  public $_components = array('Utility');
  public $_forms = array();

  public $_moduleDaos = array();
  public $_moduleModels = array();
  public $_moduleComponents = array();
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
    }

}//end class
