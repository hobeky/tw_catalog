<?php

namespace App\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;

class HwiBundleFixUserProvider extends EntityUserProvider
{
    /**
     * Constructor.
     *
     * @param Registry $registry manager registry
     * @param string $class user entity class to load
     * @param array $properties Mapping of resource owners to properties
     * @param string $managerName Optional name of the entity manager to use
     */
    public function __construct(Registry $registry, $class, array $properties, $managerName = null)
    {
        $this->em = $registry->getManager($managerName);
        $this->class = $class;
        $this->properties = array_merge($this->properties, $properties);
    }
}