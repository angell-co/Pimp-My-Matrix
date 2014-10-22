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

      $settings = $this->getSettings();

      $buttonConfig = $settings['buttonConfig'];

      if ( $buttonConfig !== '' )
      {
        craft()->templates->includeJsResource('pimpmymatrix/js/pimpmymatrix.js');
        craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');

        // shall we JsonHelper::encode($buttonConfig) ?
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
    return '1.1';
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

    // filter out the non-matrix
    foreach ($fields as $field) {
      if ( $field->type === "Matrix" )
      {

        // add blockTypes
        $blockTypes = array();
        foreach (craft()->matrix->getBlockTypesByFieldId($field->id) as $blockType) {
          $blockTypes[] = array(
            'name' => $blockType->name,
            'handle' => $blockType->handle
          );
        }

        // get any groups for table field from current settings
        $rows = array();
        $settings = JsonHelper::decode($this->getSettings()->buttonConfig);

        if ( $settings )
        {
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
        }

        // sort columns for table field
        $columns = array(
          'label' => array(
            'heading' => Craft::t('Group Name'),
            'type' => 'singleline'
          )
        );

        // make table html
        $table = craft()->templates->renderMacro('_includes/forms', 'editableTableField', array(
          array(
            'label'        => Craft::t('Groups'),
            'id'           => 'pimpmymatrix-grouptable-'.$field->handle,
            'name'         => 'pimpmymatrix-grouptable-'.$field->handle,
            'cols'         => $columns,
            'rows'         => $rows,
            'addRowLabel'  => Craft::t('Add a group'),
            'initJs'       => false
            )
        ));

        // ping the table here?
        $id = craft()->templates->namespaceInputId("pimpmymatrix-grouptable-".$field->handle);
        $name = craft()->templates->namespaceInputName("pimpmymatrix-grouptable-".$field->handle);
        craft()->templates->includeJs("
          var pimpTable = new Craft.EditableTable('".$id."','".$name."', ".JsonHelper::encode($columns).",{
            onAddRow: $.proxy(pimp, 'bindTextchanges', {
              tableId: '".$id."',
              fieldHandle: '".$field->handle."'
            }),
            onDeleteRow: $.proxy(pimp, 'reconstructSelects')
          });

          pimp.bindTextchanges({
            tableId: '".$id."',
            fieldHandle: '".$field->handle."'
          });

          pimpTable.sorter.settings.onSortChange = $.proxy(pimp, 'reconstructSelects');
        ");

        // add all of the above to an output array
        $blockTypesOnFields[] = array(
          'id'         => $field->id,
          'name'       => $field->name,
          'handle'     => $field->handle,
          'blockTypes' => $blockTypes,
          'table'      => $table
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
