<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class PageMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_page'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_page');

        if (
            isset($columns['pageImageOverwriteMeta'])
            || !isset($columns['pageImageJumpTo'], $columns['pageImageAlt'], $columns['pageImageTitle'])
        ) {
            return false;
        }

        return $this->connection->executeQuery(
            "SELECT COUNT(*) FROM tl_page WHERE pageImageJumpTo>0 OR pageImageAlt!='' OR pageImageTitle!=''",
        )->fetchOne() > 0;
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("ALTER TABLE tl_page ADD `pageImageOverwriteMeta` char(1) NOT NULL default '', ADD `pageImageUrl` varchar(255) NOT NULL default ''");

        $this->connection->executeStatement("UPDATE tl_page SET pageImageOverwriteMeta='1' WHERE pageImageJumpTo>0 OR pageImageAlt!='' OR pageImageTitle!=''");

        $this->connection->executeStatement("UPDATE tl_page SET pageImageUrl=CONCAT('{{link_url::', pageImageJumpTo, '}}') WHERE pageImageJumpTo>0");

        return $this->createResult(true);
    }
}
