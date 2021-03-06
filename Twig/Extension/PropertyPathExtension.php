<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\Gluon\Twig\Extension;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Use property path in twig.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class PropertyPathExtension extends \Twig_Extension
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * Constructor.
     *
     * @param PropertyAccessorInterface $propertyAccessor The property accessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('property_path', [$this, 'propertyPath']),
        ];
    }

    /**
     * Get the data defined by property path.
     *
     * @param mixed                             $data         The data
     * @param PropertyPathInterface|string|null $propertyPath The property path
     *
     * @return mixed
     */
    public function propertyPath($data, $propertyPath)
    {
        if (null !== $propertyPath && !$propertyPath instanceof PropertyPathInterface) {
            $propertyPath = new PropertyPath($propertyPath);
        }

        if ($propertyPath instanceof PropertyPathInterface && (\is_object($data) || $data instanceof \ArrayAccess)) {
            $data = $this->propertyAccessor->getValue($data, $propertyPath);
        }

        return $data;
    }
}
