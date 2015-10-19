<?php
namespace Craft;

/**
 * Pimp My Matrix by Supercool
 *
 * @package   PimpMyMatrix
 * @author    Josh Angell
 * @copyright Copyright (c) 2014, Supercool Ltd
 * @link      http://www.supercooldesign.co.uk
 */

class PimpMyMatrixPlugin extends BasePlugin
{

  public function getName()
  {
    return Craft::t('Pimp My Matrix');
  }

  public function getVersion()
  {
    return '1.3.2';
  }

  public function getDeveloper()
  {
    return 'Supercool';
  }

  public function getDeveloperUrl()
  {
    return 'http://plugins.supercooldesign.co.uk';
  }

  public function init()
  {

    // Move this to somewhere outside of this file for cleanliness
    if ( craft()->request->isCpRequest() && craft()->userSession->isLoggedIn() )
    {

      $segments = craft()->request->getSegments();


      /**
       * Groups configuration
       */
      // Check we’re on the right page for doing the configuration.
      // For now we have to have the entry type saved first.
      if ( count($segments) == 5
           && $segments[0] == 'settings'
           && $segments[1] == 'sections'
           && $segments[3] == 'entrytypes'
           && $segments[4] != 'new'
         )
      {
        craft()->templates->includeJsResource('pimpmymatrix/js/fld.js');
        craft()->templates->includeJsResource('pimpmymatrix/js/settings.js');

        $matrixFieldIds = craft()->db->createCommand()
          ->select('id')
          ->from('fields')
          ->where('type = :type', array(':type' => 'Matrix'))
          ->queryColumn();

        $settings = array(
          'matrixFieldIds' => $matrixFieldIds,
          'context' => 'entrytype:'.$segments[4]
        );

        craft()->templates->includeJs('new PimpMyMatrix.Configurator("#fieldlayoutform", '.JsonHelper::encode($settings).');');
      }

      /**
       * Matrix fields in entry types
       */
      if ( count($segments) == 3
           && $segments[0] == 'entries'
           && $segments[2] != 'new'
         )
      {
        $entryId = explode('-',$segments[2])[0];
        $entry = craft()->entries->getEntryById($entryId);

        if ($entry)
        {
          // Get all the data for the entrytype context regardless of entrytype id
          $blockTypeGroups = craft()->pimpMyMatrix_blockTypeGroups->getBlockTypeGroupsByContext('entrytype', 'context', true);

          if ($blockTypeGroups)
          {
            craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');
            craft()->templates->includeJsResource('pimpmymatrix/js/pimpmymatrix.js');

            $settings = array(
              'blockTypeGroups' => $blockTypeGroups,
              'context' => 'entrytype:'.$entry->type->id
            );
            craft()->templates->includeJs('new PimpMyMatrix.BlockTypeGrouper('.JsonHelper::Encode($settings).');');
          }

        }
      }

    }

  }

}
