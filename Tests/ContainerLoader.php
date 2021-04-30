<?php

namespace OAuth2\ServerBundle\Tests;

use Exception;
use OAuth2\ServerBundle\Command\CreateClientCommand;
use OAuth2\ServerBundle\Controller\AuthorizeController;
use OAuth2\Server;
use OAuth2\ServerBundle\Manager\ClientManager;
use OAuth2\ServerBundle\Storage\AuthorizationCode;
use OAuth2\ServerBundle\Storage\ClientCredentials;
use OAuth2\ServerBundle\Storage\Scope;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

/**
 * Class ContainerLoader
 */
class ContainerLoader
{
    /**
     * @param  array $configs
     *
     * @return ContainerBuilder
     *
     * @throws Exception
     */
    public static function buildTestContainer($configs = array()): ContainerBuilder
    {
        if (!isset($_SERVER['CONTAINER_CONFIG'])) {
            throw new Exception('Must set CONTAINER_CONFIG in phpunit.xml or environment variable');
        }

        $container = new ContainerBuilder();
        $locator   = new FileLocator(__DIR__ . '/..');
        $loader    = new XmlFileLoader($container, $locator);

        $loader->load($_SERVER['CONTAINER_CONFIG']);

        $openIdConfig = <<<EOF
<?xml version="1.0"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <parameters>
        <parameter key="oauth2.storage.authorization_code.class">OAuth2\ServerBundle\Storage\OpenID\AuthorizationCode</parameter>

        <parameter key="oauth2.server.config" type="collection">
            <parameter key="use_openid_connect">true</parameter>
            <parameter key="issuer">oauth2-server-bundle</parameter>
        </parameter>
        <parameter key="kernel.cache_dir">test</parameter>
    </parameters>
    <services>
        <service id="session.storage" class="Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface" />
    </services>
</container>
EOF;
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../vendor/symfony/framework-bundle/Resources/config');
        $exclude = array(
            'debug.xml',
        );
        foreach ($finder->depth('== 0') as $file) {
            if (!in_array($file->getFilename(), $exclude)) {
                $loader->load($file->getRealPath());
            }
        }

        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../vendor/symfony/security-bundle/Resources/config');
        foreach ($finder->depth('== 0') as $file) {
            $loader->load($file->getRealPath());
        }

        file_put_contents($tmpFile = tempnam(sys_get_temp_dir(), 'openid-config'), $openIdConfig);
        $loader->load($tmpFile);
        foreach ($configs as $file) {
            $loader->load($file);
        }

        if ($container->hasDefinition(AuthorizationCode::class)) {
            $definition = $container->getDefinition(AuthorizationCode::class);
            $definition->setClass(\OAuth2\ServerBundle\Storage\OpenID\AuthorizationCode::class);
        }

        $publicServices =  array(
            Scope::class,
            ClientCredentials::class,
            Server::class,
            AuthorizeController::class,
            ClientManager::class,
            CreateClientCommand::class,
        );
        foreach ($publicServices as $service) {
            if ($container->hasDefinition($service)) {
                $definition = $container->getDefinition($service);
                $definition->setPublic(true);
            }
        }

        $container->setParameter('kernel.debug', '');
        $container->setParameter('kernel.charset', 'UTF-8');
        $container->setParameter('kernel.container_class', 'test');
        $container->setParameter('kernel.cache_dir', __DIR__.'/../cache');
        $container->setParameter('kernel.project_dir', __DIR__.'/..');
        $container->setParameter('kernel.root_dir', __DIR__.'/..');
        $container->setParameter('kernel.error_controller', '');
        $container->setParameter('kernel.default_locale', 'en_US');
        $container->setParameter('debug.file_link_format', '');
        $container->setParameter('validator.translation_domain', 'messages');
        $container->setParameter('profiler.storage.dsn', __DIR__.'/..');
        $container->setParameter('request_listener.http_port', 80);
        $container->setParameter('request_listener.https_port', 443);
        $container->setParameter('security.access.always_authenticate_before_granting', false);
        $container->setParameter('security.authentication.manager.erase_credentials', false);
        $container->setParameter('router.resource', '');
        $container->compile();

        return $container;
    }
}
