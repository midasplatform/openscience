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

/** Component for api methods */
class Openscience_LinkDataToResultsComponent extends AppComponent
{

  /**
   * Output an multi-array associating data and results
   * @param $dataFolderId the id of the folder containing input items
   * @param $resultFolderId the id of the folder containing result items
   * @return the name of the dashboard
   */
  public function getAssociationArray($dataFolderId, $resultFolderId)
    {

    // Load the models needed
    $modelLoader = new MIDAS_ModelLoader;
    $folderModel = $modelLoader->loadModel('Folder');
    $itemModel = $modelLoader->loadModel('Item');    
    $itemRevisionModel = $modelLoader->loadModel('ItemRevision');
    $metadataModel = $modelLoader->loadModel('Metadata');
    $itemThumbnailModel = $modelLoader->loadModel('Itemthumbnail',
                                                  'thumbnailcreator');
    

    // This needs to be optimized. Right now we're doing a O(n^2) operation when
    // we should be able to do this in O(n). This should be fixed.
    // TODO FIXME
    $resultFolderDao = $folderModel->load($resultFolderId);
    $resultItems = $this->findItemsAtTheBottomOfTheHierarchy($resultFolderDao);
    $ret = array();
    foreach($resultItems as $curItem)
      {
      $result = array();
      $revisionDao = $itemModel->getLastRevision($curItem);
      $metadata = $itemRevisionModel->getMetadata($revisionDao);
      $thumbnailId = $itemThumbnailModel->getByItemId($curItem->getKey())->getKey();
      $result['thumbnail_url'] = 'http://localhost/Midas3/thumbnailcreator/'.
        'thumbnail/item?itemthumbnail=' . $thumbnailId;
      foreach($metadata as $metadataDao)
        {
        $element = $metadataDao->getElement();
        $value = $metadataDao->getValue();
        if($element === 'Source Item')
          {
          $sourceItem = $itemModel->load($value);
          $result['name'] = $this->getNameFromSourceItem($sourceItem);
          }
        else if($element === 'Seed 1: Volume')
          {
          $result[$element] = $value;
          }
        }
      $ret[] = $result;
      }
    return $ret;
    }

  protected function getNameFromSourceItem($itemDao)
    {
    $modelLoader = new MIDAS_ModelLoader;
    $itemModel = $modelLoader->loadModel('Item');    
    $itemRevisionModel = $modelLoader->loadModel('ItemRevision');
    $metadataModel = $modelLoader->loadModel('Metadata');

    $itemName = $itemDao->getName();
    $uid = '';
    $patienName = '';

    $revisionDao = $itemModel->getLastRevision($itemDao);
    $metadata = $itemRevisionModel->getMetadata($revisionDao);
    foreach($metadata as $metadataDao)
      {
      $element = $metadataDao->getElement();
      $qualifier = $metadataDao->getQualifier();
      $value = $metadataDao->getValue();
      if( $element === 'DICOM' && $qualifier === 'SeriesInstanceUID')
        {
        $uid = $value;
        }
      else if( $element === 'DICOM' && $qualifier === 'PatientName')
        {
        $patientName = $value;
        }
      }
    return $patientName . ' ' . $uid . ' ' . $itemName;
    }

  /**
   * Recursively walks a hierarchy to get all the items within it.
   * @return an array of all of the items within a hierarchy
   */
  private function findItemsAtTheBottomOfTheHierarchy($folderDao)
    {
    $ret = $folderDao->getItems();
    $childFolders = $folderDao->getFolders();
    foreach($childFolders as $curFolder)
      {
      $children = $this->findItemsAtTheBottomOfTheHierarchy($curFolder);
      $ret = array_merge($ret, $children);
      }
    return $ret;
    }
    
} // end class
