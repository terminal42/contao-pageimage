<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class ModuleMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function shouldRun(): bool
    {
        return $this->connection->fetchOne(
            "SELECT COUNT(*) FROM tl_module WHERE type='pageImage' OR type='backgroundImage'"
        ) > 0;
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("UPDATE tl_module SET type='pageimage' WHERE type='pageImage'");
        $this->connection->executeStatement("UPDATE tl_module SET type='pageimage', customTpl='mod_pageimage_background' WHERE type='backgroundImage'");

        return $this->createResult(true);
    }
}
