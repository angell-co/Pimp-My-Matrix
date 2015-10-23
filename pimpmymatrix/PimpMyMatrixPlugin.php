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

  public function hasCpSection()
  {
    return true;
  }

  public function registerCpRoutes()
  {
    return array(
      // Edit Global Context
      'pimpmymatrix' => array('action' => 'pimpMyMatrix/editGlobalContext'),
    );
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
        craft()->templates->includeCssFile('//fonts.googleapis.com/css?family=Coming+Soon');
        craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');
        craft()->templates->includeJsResource('pimpmymatrix/js/blocktypefieldlayoutdesigner.js');
        craft()->templates->includeJsResource('pimpmymatrix/js/groupsdesigner.js');
        craft()->templates->includeJsResource('pimpmymatrix/js/configurator.js');

        $settings = array(
          'matrixFieldIds' => craft()->pimpMyMatrix->getMatrixFieldIds(),
          'context' => 'entrytype:'.$segments[4]
        );

        craft()->templates->includeJs('new PimpMyMatrix.Configurator("#fieldlayoutform", '.JsonHelper::encode($settings).');');
      }

      /**
       * Matrix fields in entry types
       */
      if ( count($segments) == 3 && $segments[0] == 'entries' )
      {

        if ($segments[2] == 'new')
        {
          $section = craft()->sections->getSectionByHandle($segments[1]);
          $sectionEntryTypes = $section->getEntryTypes();
          $entryType = ArrayHelper::getFirstValue($sectionEntryTypes);
        }
        else
        {
          $entryId = explode('-',$segments[2])[0];
          $entry = craft()->entries->getEntryById($entryId);

          if ($entry)
          {
            $entryType = $entry->type;
          }
        }

        // Get global data
        $globalPimpedBlockTypes = craft()->pimpMyMatrix_blockTypes->getBlockTypesByContext('global', 'context');

        // Get all the data for the entrytype context regardless of entrytype id
        $contextPimpedBlockTypes = craft()->pimpMyMatrix_blockTypes->getBlockTypesByContext('entrytype', 'context', true);

        $pimpedBlockTypes = array_merge($globalPimpedBlockTypes, $contextPimpedBlockTypes);

        if ($pimpedBlockTypes)
        {
          craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');

          // Set up the groups
          craft()->templates->includeJsResource('pimpmymatrix/js/fieldmanipulator.js');
          $settings = array(
            'blockTypes' => $pimpedBlockTypes,
            'context' => 'entrytype:'.$entryType->id
          );
          craft()->templates->includeJs('new PimpMyMatrix.FieldManipulator('.JsonHelper::Encode($settings).');');
        }

      }

    }

  }

}
