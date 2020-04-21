<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twc\MakerBundle\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry as LegacyManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;

final class EntityClassGenerator
{
    private $generator;
    private $doctrineHelper;

    public function __construct(Generator $generator, DoctrineHelper $doctrineHelper)
    {
        $this->generator = $generator;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function generateEntityClass(
        ClassNameDetails $entityClassDetails,
        ClassNameDetails $repoClassDetails,
        bool $apiResource, bool $withPasswordUpgrade = false): string
    {
        $tableName = $this->doctrineHelper->getPotentialTableName($entityClassDetails->getFullName());

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            'Doctrine/Entity.tpl.php',
            [
                'repository_full_class_name' => $repoClassDetails->getFullName(),
                'api_resource' => $apiResource,
                'should_escape_table_name' => $this->doctrineHelper->isKeyword($tableName),
                'table_name' => $tableName,
            ]
        );

        $entityAlias = strtolower($entityClassDetails->getShortName()[0]);
        $this->generator->generateClass(
            $repoClassDetails->getFullName(),
            'Doctrine/Repository.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_alias' => $entityAlias,
                'with_password_upgrade' => $withPasswordUpgrade,
                'doctrine_registry_class' => interface_exists(ManagerRegistry::class) ? ManagerRegistry::class : LegacyManagerRegistry::class,
            ]
        );

        return $entityPath;
    }
}
