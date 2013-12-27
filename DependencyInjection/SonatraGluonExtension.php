<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\GluonBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SonatraGluonExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('response.xml');
        $loader->load('twig.xml');
        $loader->load('assetic.xml');

        if ($config['font']['enabled']) {
            $this->configFonts($config['font'], $container);
        }

        $this->configFontAwesome($config['font_awesome'], $container);
        $this->configCommonAssets($config['common_assets'], $container);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $exts = $container->getExtensions();

        if (isset($exts['sonatra_bootstrap'])) {
            $container->prependExtensionConfig(
                'sonatra_bootstrap',
                array(
                    'common_assets' => array(
                        'stylesheets' => array(
                            'bootstrap' => array(
                                'theme' => '@SonatraGluonBundle/Resources/assetic/less/theme.less',
                                'components' => array(
                                    'variables' => '@SonatraGluonBundle/Resources/assetic/less/variables.less'
                                ),
                            ),
                        ),
                    ),
                )
            );
        }
    }

    /**
     * Configures the fonts resource.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configFonts(array &$config, ContainerBuilder $container)
    {
        $openSans = 'https://fonts.googleapis.com/css?family=Open+Sans:';
        $raleway = 'https://fonts.googleapis.com/css?family=Raleway:';

        foreach ($config['open_sans'] as $font => $enabled) {
            switch ($font) {
                case 'light':
                    $openSans .= !$enabled ? '' : '300';
                    break;
                case 'light_italic':
                    $openSans .= !$enabled ? '' : '300italic';
                    break;
                case 'normal':
                    $openSans .= !$enabled ? '' : '400';
                    break;
                case 'normal_italic':
                    $openSans .= !$enabled ? '' : '400italic';
                    break;
                case 'semi_bold':
                    $openSans .= !$enabled ? '' : '600';
                    break;
                case 'semi_bold_italic':
                    $openSans .= !$enabled ? '' : '600italic';
                    break;
                case 'bold':
                    $openSans .= !$enabled ? '' : '700';
                    break;
                case 'bold_italic':
                    $openSans .= !$enabled ? '' : '700italic';
                    break;
                case 'extra_bold':
                    $openSans .= !$enabled ? '' : '800';
                    break;
                case 'extra_bold_italic':
                    $openSans .= !$enabled ? '' : '800italic';
                    break;
                default:
                   break;
            }

            $openSans .= !$enabled ? '' : ',';
        }

        foreach ($config['raleway'] as $font => $enabled) {
            switch ($font) {
                case 'thin':
                    $raleway .= !$enabled ? '' : '100';
                    break;
                case 'extra_light':
                    $raleway .= !$enabled ? '' : '200';
                    break;
                case 'light':
                    $raleway .= !$enabled ? '' : '300';
                    break;
                case 'normal':
                    $raleway .= !$enabled ? '' : '400';
                    break;
                case 'medium':
                    $raleway .= !$enabled ? '' : '500';
                    break;
                case 'semi_bold':
                    $raleway .= !$enabled ? '' : '600';
                    break;
                case 'bold':
                    $raleway .= !$enabled ? '' : '700';
                    break;
                case 'extra_bold':
                    $raleway .= !$enabled ? '' : '800';
                    break;
                case 'ultra_bold':
                    $raleway .= !$enabled ? '' : '900';
                    break;
                default:
                    break;
            }

            $raleway .= !$enabled ? '' : ',';
        }

        $def = $container->getDefinition('sonatra_gluon.twig.extension.font');
        $def->addArgument(rtrim($openSans, ','));
        $def->addArgument(rtrim($raleway, ','));
    }

    /**
     * Configures the font awesome resource.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configFontAwesome(array &$config, ContainerBuilder $container)
    {
        if ($config['build']) {
            if ($container->hasDefinition('sonatra_gluon.assetic.font_awesome_resource')) {
                $container->getDefinition('sonatra_gluon.assetic.font_awesome_resource')->replaceArgument(0, $config['font']['paths']);
            }

            if ($container->hasDefinition('sonatra_gluon.assetic.font_awesome_resource.stylesheet')) {
                $container->getDefinition('sonatra_gluon.assetic.font_awesome_resource.stylesheet')->replaceArgument(0, $config['cache_directory']);
                $container->getDefinition('sonatra_gluon.assetic.font_awesome_resource.stylesheet')->replaceArgument(1, $config['directory']);
                $container->getDefinition('sonatra_gluon.assetic.font_awesome_resource.stylesheet')->replaceArgument(2, $config['components']);
            }

        } else {
            $container->removeDefinition('sonatra_gluon.assetic.font_awesome_resource');
            $container->removeDefinition('sonatra_gluon.assetic.font_awesome_resource.stylesheet');
        }
    }

    /**
     * Configures the threedubmedia.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configCommonAssets(array &$config, ContainerBuilder $container)
    {
        if (!$config['enabled']) {
            return;
        }

        $this->createAssetServices('stylesheet', $config['stylesheets'], $container);
        $this->createAssetServices('javascript', $config['javascripts'], $container);
    }

    /**
     * Create the resource asset service for stylesheet or javascript.
     *
     * @param unknown          $type
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function createAssetServices($type, array &$config, ContainerBuilder $container)
    {
        $tag = sprintf('sonatra_bootstrap.%s.common', $type);

        foreach ($config as $vendor => $vConfig) {
            foreach ($vConfig as $component => $value) {
                if (is_string($value)) {
                    $id = sprintf('sonatra_gluon.assetic.common_%ss_resource.%s_%s', $type, $vendor, $component);
                    $componentDef = $this->createFileResourceDefinition($value, $tag);

                    $container->setDefinition($id, $componentDef);

                } elseif (is_array($value)) {
                    foreach ($value as $subComponent => $subValue) {
                        if (is_string($subValue)) {
                            $id = sprintf('sonatra_gluon.assetic.common_%ss_resource.%s_%s_%s', $type, $vendor, $component, $subComponent);
                            $componentDef = $this->createFileResourceDefinition($subValue, $tag);

                            $container->setDefinition($id, $componentDef);
                        }
                    }
                }
            }
        }
    }

    /**
     * Create assetic file resource definition.
     *
     * @param string $path
     * @param string $tag
     *
     * @return Definition
     */
    protected function createFileResourceDefinition($path, $tag)
    {
        $definition = new Definition();
        $definition
            ->setClass('Assetic\Factory\Resource\FileResource')
            ->setPublic(true)
            ->addArgument($path)
            ->addTag($tag)
        ;

        return $definition;
    }
}
