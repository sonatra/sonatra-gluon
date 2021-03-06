<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\Gluon\Block\Type;

use Fxp\Component\Block\AbstractType;
use Fxp\Component\Block\BlockInterface;
use Fxp\Component\Block\BlockView;
use Fxp\Component\Bootstrap\Block\Type\DropdownItemType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FabDropdownItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'style' => $options['style'],
            'size' => $options['size'],
            'fab_label' => $options['fab_label'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'style' => null,
            'size' => null,
            'fab_label' => null,
        ]);

        $resolver->setAllowedTypes('style', ['null', 'string']);
        $resolver->setAllowedTypes('size', ['null', 'string']);
        $resolver->setAllowedTypes('fab_label', ['null', 'string']);

        $resolver->setAllowedValues('style', [
            null, 'default', 'primary', 'accent', 'success', 'info', 'warning', 'danger',
        ]);
        $resolver->setAllowedValues('size', [null, 'xs', 'sm', 'lg']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return DropdownItemType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fab_dropdown_item';
    }
}
