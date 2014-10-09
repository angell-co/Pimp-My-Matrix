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

        // JsonHelper::encode($buttonConfig) ??????
        craft()->templates->includeJs('var pimp = new Craft.PimpMyMatrix('.$buttonConfig.');');
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
    // get fields
    $fields = craft()->fields->getAllFields();
    $blockTypesOnFields = array();

    // filter out the non-matrix and add blockTypes and eny existign groups
    foreach ($fields as $field) {
      if ( $field->type === "Matrix" )
      {

        $blockTypes = array();
        foreach (craft()->matrix->getBlockTypesByFieldId($field->id) as $blockType) {
          $blockTypes[] = array(
            'name' => $blockType->name,
            'handle' => $blockType->handle
          );
        }

        // get any groups for this field from current settings
        $rows = array();
        $settings = JsonHelper::decode($this->getSettings()->buttonConfig);
        foreach ($settings as $key => $value)
        {

          if ( $settings[$key]['fieldHandle'] === $field->handle )
          {

            foreach ($settings[$key]['config'] as $config)
            {

              $row = array('label' => $config['group']);
              if ( ! in_array($row, $rows) )
              {
                $rows[] = $row;
              }

            }

          }

        }

        $blockTypesOnFields[] = array(
          'id'         => $field->id,
          'name'       => $field->name,
          'handle'     => $field->handle,
          'blockTypes' => $blockTypes,
          'rows'       => $rows
        );
      }
    }

    // ping the buttonConfigurator
    craft()->templates->includeJs('pimp.buttonConfigurator();');

    // load settings template
    return craft()->templates->render('pimpMyMatrix/settings', array(
      'settings'           => $this->getSettings(),
      'blockTypesOnFields' => $blockTypesOnFields
    ));
  }

  protected function defineSettings()
  {
    return array(
      'buttonConfig' => array(AttributeType::String)
    );
  }

}
