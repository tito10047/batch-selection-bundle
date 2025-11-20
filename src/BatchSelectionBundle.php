<?php

namespace Tito10047\BatchSelectionBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Tito10047\BatchSelectionBundle\DependencyInjection\Compiler\AutoTagIdentifierNormalizersPass;
use Tito10047\BatchSelectionBundle\DependencyInjection\Compiler\AutoTagIdentityLoadersPass;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html
 */
class BatchSelectionBundle extends AbstractBundle
{
	protected string $extensionAlias = 'batch_selection';
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }
    
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new AutoTagIdentifierNormalizersPass());
        $container->addCompilerPass(new AutoTagIdentityLoadersPass());
    }
}