<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\Block\Type;

use Sonatra\Bundle\BlockBundle\Block\AbstractType;
use Sonatra\Bundle\BlockBundle\Block\BlockBuilderInterface;
use Sonatra\Bundle\BlockBundle\Block\BlockView;
use Sonatra\Bundle\BlockBundle\Block\BlockInterface;
use Sonatra\Bundle\BlockBundle\Block\Exception\InvalidConfigurationException;
use Sonatra\Bundle\BlockBundle\Block\Util\BlockUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Panel Buttons Block Type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class PanelButtonsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildBlock(BlockBuilderInterface $builder, array $options)
    {
        if ($options['scrollable']) {
            $builder->add('_nav_scrollable', 'nav_scrollable');
            $builder->get('_nav_scrollable')->add('_navButtonGroup', 'block', array(
                'attr' => array(
                    'class' => $options['class_nav'],
                ),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BlockInterface $child, BlockInterface $block, array $options)
    {
        // scrollable
        if (BlockUtil::isValidBlock('nav_scrollable', $child)) {
            if ($block->getAttribute('already_nav_scrollable')) {
                $navScrollable = $block->get($block->getAttribute('already_nav_scrollable'));

                foreach ($navScrollable->all() as $name => $action) {
                    $child->add($action);
                }

                $block->remove($block->getAttribute('already_nav_scrollable'));

            } else {
                $block->setAttribute('already_nav_scrollable', $child->getName());
            }

            $attr = $child->getOption('attr');
            $attr['data-class-nav'] = $options['class_nav'];
            $child->setOption('attr', $attr);

        // button
        } elseif (BlockUtil::isValidBlock('button', $child)) {
            $parent = $this->findParentButtons($block);

            if ($parent !== $block) {
                $parent->add($child);
            }

            if (null === $child->getOption('size')) {
                $child->setOption('size', $options['button_size']);
            }

        // other
        } else {
            $msg = 'The "panel_buttons" child block (name: "%s") must be a "button" block type';
            throw new InvalidConfigurationException(sprintf($msg, $child->getName()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(BlockView $view, BlockInterface $block, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'scrollable' => $options['scrollable'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(BlockView $view, BlockInterface $block, array $options)
    {
        if (!is_scalar($view->vars['value'])) {
            $view->vars['value'] = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
            'scrollable'   => true,
            'class_nav'    => 'nav-btn-group',
            'button_size'  => 'sm',
        ));

        $resolver->setAllowedTypes(array(
            'scrollable'  => 'bool',
            'class_nav'   => 'string',
            'button_size' => 'string',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'panel_buttons';
    }

    /**
     * Finds the parent block of buttons.
     *
     * @param BlockInterface $block
     *
     * @return BlockInterface
     */
    protected function findParentButtons(BlockInterface $block)
    {
        if ($block->getAttribute('block_parent_buttons')) {
            return $block->getAttribute('block_parent_buttons');
        }

        $parent = $block;

        if ($block->getOption('scrollable')) {
            foreach ($block->all() as $name => $child) {
                if (BlockUtil::isValidBlock('nav_scrollable', $child)) {
                    foreach ($child->all() as $name => $subChild) {
                        if (BlockUtil::isValidBlock('block', $subChild)) {
                            $parent = $subChild;
                            break;
                            break;
                        }
                    }
                }
            }
        }

        $block->setAttribute('block_parent_buttons', $parent);

        return $parent;
    }
}