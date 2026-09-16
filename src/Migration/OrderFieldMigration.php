<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Migration;

use Contao\CoreBundle\Migration\Version500\AbstractOrderFieldMigration;

class OrderFieldMigration extends AbstractOrderFieldMigration
{
    protected function getTableFields(): array
    {
        return [
            'tl_page' => [
                'pageImageOrder' => 'pageImage',
            ],
        ];
    }
}
