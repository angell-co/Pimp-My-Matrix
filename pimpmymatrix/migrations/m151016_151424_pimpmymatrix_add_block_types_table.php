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

		return true;
	}
}
