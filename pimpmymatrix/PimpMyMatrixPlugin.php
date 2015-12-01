<?php
namespace Craft;

/**
 * Pimp My Matrix by Supercool
 *
 * @package   PimpMyMatrix
 * @author    Josh Angell
 * @copyright Copyright (c) 2015, Supercool Ltd
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
    return '2.1.2';
  }

  public function getSchemaVersion()
  {
    return '2.0.0';
  }

  public function getDescription()
  {
    return Craft::t('Enhance a busy Matrix field by organising block types');
  }

  public function getDeveloper()
  {
    return 'Supercool';
  }

  public function getDeveloperUrl()
  {
    return 'http://plugins.supercooldesign.co.uk';
  }

  public function getDocumentationUrl()
  {
    return 'http://plugins.supercooldesign.co.uk/plugin/pimp-my-matrix/docs';
  }

  public function getSettingsUrl()
  {
    return 'pimpmymatrix';
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
    $plugin = craft()->plugins->getPlugin('PimpMyMatrix');
    if (!craft()->plugins->doesPluginRequireDatabaseUpdate($plugin))
    {
      craft()->pimpMyMatrix->loader();
    }
  }

  /**
   * Require Craft 2.5
   *
   * @return bool
   * @throws Exception
   */
  public function onBeforeInstall()
  {
    if (version_compare(craft()->getVersion(), '2.5', '<'))
    {
      throw new Exception('Pimp My Matrix requires Craft CMS 2.5+ in order to run.');
    }
  }

}
