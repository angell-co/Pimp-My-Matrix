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
    return '2.0';
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
    $plugin = craft()->plugins->getPlugin('PimpMyMatrix');
    if (!craft()->plugins->doesPluginRequireDatabaseUpdate($plugin))
    {
      craft()->pimpMyMatrix->loader();
    }
  }

}
