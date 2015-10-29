<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151027_103010_pimpmymatrix_migrate_old_settings extends BaseMigration
{

  /**
   * Any migration code in here is wrapped inside of a transaction.
   *
   * @return bool
   */
  public function safeUp()
  {

    // Migrate old settings across
    $settings = craft()->db->createCommand()
      ->select('settings')
      ->from('plugins')
      ->where('class = :class', array(':class' => 'PimpMyMatrix'))
      ->queryScalar();

    if ($settings)
    {
      $decodedSettings = JsonHelper::decode($settings);

      if (isset($decodedSettings['buttonConfig']))
      {

        PimpMyMatrixPlugin::log('Migrating old settings', LogLevel::Info, true);
        $buttonConfig = JsonHelper::decode($decodedSettings['buttonConfig']);

        foreach ($buttonConfig as $row)
        {
          $field = craft()->fields->getFieldByHandle($row['fieldHandle']);

          $blockTypes = craft()->matrix->getBlockTypesByFieldId($field->id);

          foreach ($row['config'] as $config)
          {

            $selectedBlocktypeId = null;
            foreach ($blockTypes as $blockType)
            {
              if ( $blockType->handle == $config['blockType']['handle'] )
              {
                $selectedBlocktypeId = $blockType->id;
              }
            }

            $pimpedBlockType = new PimpMyMatrix_BlockTypeModel();
            $pimpedBlockType->fieldId           = $field->id;
            $pimpedBlockType->matrixBlockTypeId = $selectedBlocktypeId;
            $pimpedBlockType->fieldLayoutId     = null;
            $pimpedBlockType->groupName         = urldecode($config['group']);
            $pimpedBlockType->context           = 'global';

            $success = craft()->pimpMyMatrix_blockTypes->saveBlockType($pimpedBlockType);
            if (!$success)
            {
              PimpMyMatrixPlugin::log("Config for the field {$row['fieldHandle']} could not be migrated", LogLevel::Info, true);
            }
          }
        }
        PimpMyMatrixPlugin::log('Done migrating old settings', LogLevel::Info, true);
      }
    }

    return true;
  }

}
