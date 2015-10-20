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
		// Create the craft_pimpmymatrix_blocktypegroups table
		craft()->db->createCommand()->createTable('pimpmymatrix_blocktypes', array(
			'fieldId'           => array('column' => 'integer', 'required' => true),
			'matrixBlockTypeId' => array('column' => 'integer', 'required' => true),
			'groupName'         => array('maxLength' => 255, 'column' => 'varchar', 'required' => true),
			'context'           => array('required' => true),
		), null, true);

		// Add foreign keys to craft_pimpmymatrix_blocktypegroups
		craft()->db->createCommand()->addForeignKey('pimpmymatrix_blocktypes', 'fieldId', 'fields', 'id', 'CASCADE', null);
		craft()->db->createCommand()->addForeignKey('pimpmymatrix_blocktypes', 'matrixBlockTypeId', 'matrixblocktypes', 'id', 'CASCADE', null);

		return true;
	}
}
