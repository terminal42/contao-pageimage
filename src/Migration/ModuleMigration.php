<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class ModuleMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (null === $schemaManager || !$schemaManager->tablesExist('tl_module')) {
            return false;
        }

        return $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_module WHERE type = BINARY 'pageImage' OR type = BINARY 'backgroundImage'"
        ) > 0;
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("UPDATE tl_module SET type='pageimage' WHERE type='pageImage'");
        $this->connection->executeStatement("UPDATE tl_module SET type='pageimage', customTpl='mod_pageimage_background' WHERE type='backgroundImage'");

        return $this->createResult(true);
    }
}
