<?php

namespace superbig\vzaddress\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use superbig\vzaddress\fields\VzAddressField;

/**
 * m181023_194812_migrate_to_craft_3 migration.
 */
class m181023_194812_migrate_to_craft_3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        (new Query())
            ->createCommand()
            ->update('{{%fields}}', [
                'type' => VzAddressField::class,
            ], [
                'type' => 'VzAddress',
            ])
            ->execute();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181023_194812_migrate_to_craft_3 cannot be reverted.\n";

        return false;
    }
}
