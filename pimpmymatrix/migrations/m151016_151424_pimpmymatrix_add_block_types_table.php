<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151016_151424_pimpmymatrix_add_block_types_table extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{

		// Create the craft_pimpmymatrix_blocktypes table
		PimpMyMatrixPlugin::log('Adding the craft_pimpmymatrix_blocktypes table', LogLevel::Info, true);
		craft()->db->createCommand()->createTable('pimpmymatrix_blocktypes', array(
			'fieldId'           => array('column' => 'integer', 'required' => true),
			'matrixBlockTypeId' => array('column' => 'integer', 'required' => true),
			'fieldLayoutId'     => array('column' => 'integer', 'required' => false),
			'groupName'         => array('maxLength' => 255, 'column' => 'varchar', 'required' => true),
			'context'           => array('required' => true),
		), null, true);

		// Add foreign keys to craft_pimpmymatrix_blocktypes
		PimpMyMatrixPlugin::log('Adding foreign keys to craft_pimpmymatrix_blocktypes', LogLevel::Info, true);
		craft()->db->createCommand()->addForeignKey('pimpmymatrix_blocktypes', 'fieldId', 'fields', 'id', 'CASCADE', null);
		craft()->db->createCommand()->addForeignKey('pimpmymatrix_blocktypes', 'matrixBlockTypeId', 'matrixblocktypes', 'id', 'CASCADE', null);
		craft()->db->createCommand()->addForeignKey('pimpmymatrix_blocktypes', 'fieldLayoutId', 'fieldlayouts', 'id', 'SET NULL', null);

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
