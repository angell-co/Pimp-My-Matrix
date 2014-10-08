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

  public function init()
  {

    // check its a cp request and that they're logged in
    if ( craft()->request->isCpRequest() && craft()->userSession->isLoggedIn() )
    {

      $buttonConfig = $this->getSettings()['buttonConfig'];

      if ( $buttonConfig !== '' )
      {
        craft()->templates->includeJsResource('pimpmymatrix/js/pimpmymatrix.js');
        craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');
        craft()->templates->includeJs('new Craft.PimpMyMatrix('.$buttonConfig.');');
      }

    }

  }

  public function getName()
  {
    return Craft::t('Pimp My Matrix');
  }

  public function getVersion()
  {
    return '0.1';
  }

  public function getDeveloper()
  {
    return 'Supercool';
  }

  public function getDeveloperUrl()
  {
    return 'http://www.supercooldesign.co.uk';
  }

  public function getSettingsHtml()
  {
    return craft()->templates->render('pimpMyMatrix/settings', array(
      'settings' => $this->getSettings()
    ));
  }

  protected function defineSettings()
  {
    return array(
      'buttonConfig' => array(AttributeType::String)
    );
  }

}
