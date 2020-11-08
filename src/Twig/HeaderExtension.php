<?php

namespace App\Twig;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HeaderExtension extends AbstractExtension
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('categories', [$this, 'getCategories']),
        ];
    }

    /**
     * @return array|Category
     */
    public function getCategories(): array
    {
        return $this->em->getRepository(Category::class)->findBy([
            'parentId' => null
        ]);
    }

}
