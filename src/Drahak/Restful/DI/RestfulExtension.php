<?php

namespace Drahak\Restful\DI;

use Drahak\Restful\Application;
use Drahak\Restful\Converters;
use Drahak\Restful\Diagnostics;
use Drahak\Restful\Http;
use Drahak\Restful\IResource;
use Drahak\Restful\Mapping;
use Drahak\Restful\ResourceFactory;
use Drahak\Restful\Security;
use Drahak\Restful\Utils;
use Drahak\Restful\Validation;
use Nette;
use Nette\Bootstrap\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Utils\Validators;
use Tracy\Debugger;

/**
 * Drahak RestfulExtension
 * @package Drahak\Restful\DI
 * @author Drahomír Hanák
 */
class RestfulExtension extends CompilerExtension
{

    /** Converter tag name */
    public const CONVERTER_TAG = 'restful.converter';

    /** Snake case convention config name */
    public const CONVENTION_SNAKE_CASE = 'snake_case';

    /** Camel case convention config name */
    public const CONVENTION_CAMEL_CASE = 'camelCase';

    /** Pascal case convention config name */
    public const CONVENTION_PASCAL_CASE = 'PascalCase';

    /**
     * Default DI settings
     * @var array
     */
    protected array $defaults = [
        'convention' => NULL,
        'timeFormat' => 'c',
        'cacheDir' => '%tempDir%/cache',
        'jsonpKey' => 'jsonp',
        'prettyPrint' => TRUE,
        'prettyPrintKey' => 'pretty',
        'routes' => [
            'generateAtStart' => FALSE,
            'presentersRoot' => '%appDir%',
            'autoGenerated' => TRUE,
            'autoRebuild' => TRUE,
            'module' => '',
            'prefix' => '',
            'panel' => TRUE
        ],
        'security' => [
            'privateKey' => NULL,
            'requestTimeKey' => 'timestamp',
            'requestTimeout' => 300
        ]
    ];

    /**
     * Register REST API extension
     */
    public static function install(Configurator $configurator): void
    {
        $configurator->onCompile[] = function ($configurator, $compiler): void {
            $compiler->addExtension('restful', new RestfulExtension);
        };
    }

    /**
     * Load DI configuration
     */
    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();

        // Additional module
        $this->loadRestful($container, $config);
        $this->loadValidation($container, $config);
        $this->loadResourceConverters($container, $config);
        $this->loadSecuritySection($container, $config);
        if ($config['routes']['autoGenerated']) {
            $this->loadAutoGeneratedRoutes($container, $config);
        }
        if ($config['routes']['panel']) {
            $this->loadResourceRoutePanel($container, $config);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     * @throws Nette\Utils\AssertionException
     * @return void
     */
    private function loadRestful(ContainerBuilder $container, array $config): void
    {
        Validators::assert($config['prettyPrintKey'], 'string');

        $container->addDefinition($this->prefix('responseFactory'))
            ->setType(Application\ResponseFactory::class)
            ->addSetup('$service->setJsonp(?)', [$config['jsonpKey']])
            ->addSetup('$service->setPrettyPrintKey(?)', [$config['prettyPrintKey']])
            ->addSetup('$service->setPrettyPrint(?)', [$config['prettyPrint']]);

        $container->addDefinition($this->prefix('resourceFactory'))
            ->setType(ResourceFactory::class);
        $container->addDefinition($this->prefix('resource'))
            ->setFactory($this->prefix('@resourceFactory') . '::create');

        $container->addDefinition($this->prefix('methodOptions'))
            ->setType(Application\MethodOptions::class);

        // Mappers
        $container->addDefinition($this->prefix('xmlMapper'))
            ->setType(Mapping\XmlMapper::class);
        $container->addDefinition($this->prefix('jsonMapper'))
            ->setType(Mapping\JsonMapper::class);
        $container->addDefinition($this->prefix('queryMapper'))
            ->setType(Mapping\QueryMapper::class);
        $container->addDefinition($this->prefix('dataUrlMapper'))
            ->setType(Mapping\DataUrlMapper::class);
        $container->addDefinition($this->prefix('nullMapper'))
            ->setType(Mapping\NullMapper::class);

        $container->addDefinition($this->prefix('mapperContext'))
            ->setType(Mapping\MapperContext::class)
            ->addSetup('$service->addMapper(?, ?)', [IResource::XML, $this->prefix('@xmlMapper')])
            ->addSetup('$service->addMapper(?, ?)', [IResource::JSON, $this->prefix('@jsonMapper')])
            ->addSetup('$service->addMapper(?, ?)', [IResource::JSONP, $this->prefix('@jsonMapper')])
            ->addSetup('$service->addMapper(?, ?)', [IResource::QUERY, $this->prefix('@queryMapper')])
            ->addSetup('$service->addMapper(?, ?)', [IResource::DATA_URL, $this->prefix('@dataUrlMapper')])
            ->addSetup('$service->addMapper(?, ?)', [IResource::FILE, $this->prefix('@nullMapper')])
            ->addSetup('$service->addMapper(?, ?)', [IResource::NULL, $this->prefix('@nullMapper')]);

        if (isset($config['mappers'])) {
            foreach ($config['mappers'] as $mapperName => $mapper) {
                $container->addDefinition($this->prefix($mapperName))
                    ->setType($mapper['class']);

                $container->getDefinition($this->prefix('mapperContext'))
                    ->addSetup('$service->addMapper(?, ?)', [$mapper['contentType'], $this->prefix('@' . $mapperName)]);
            }
        }

        // Input & validation
        $container->addDefinition($this->prefix('inputFactory'))
            ->setType(Http\InputFactory::class);

        // Http
        $container->addDefinition($this->prefix('httpResponseFactory'))
            ->setType(Http\ResponseFactory::class);

        $container->addDefinition($this->prefix('httpRequestFactory'))
            ->setType(Http\ApiRequestFactory::class);

        $container->getDefinition('httpRequest')
            ->setFactory($this->prefix('@httpRequestFactory') . '::createHttpRequest');

        $container->getDefinition('httpResponse')
            ->setFactory($this->prefix('@httpResponseFactory') . '::createHttpResponse');

        $container->addDefinition($this->prefix('requestFilter'))
            ->setType(Utils\RequestFilter::class)
            ->setArguments(['@httpRequest', [$config['jsonpKey'], $config['prettyPrintKey']]]);

        $container->addDefinition($this->prefix('methodHandler'))
            ->setType(Application\Events\MethodHandler::class);

        $container->getDefinition('application')
            ->addSetup('$service->onStartup[] = ?', [[$this->prefix('@methodHandler'), 'run']])
            ->addSetup('$service->onError[] = ?', [[$this->prefix('@methodHandler'), 'error']]);
    }

    private function loadValidation(ContainerBuilder $container, array $config): void
    {
        $container->addDefinition($this->prefix('validator'))
            ->setType(Validation\Validator::class);

        $container->addDefinition($this->prefix('validationScopeFactory'))
            ->setType(Validation\ValidationScopeFactory::class);

        $container->addDefinition($this->prefix('validationScope'))
            ->setType(Validation\ValidationScope::class)
            ->setFactory($this->prefix('@validationScopeFactory') . '::create');

    }

    private function loadResourceConverters(ContainerBuilder $container, array $config): void
    {
        Validators::assert($config['timeFormat'], 'string');

        // Default used converters
        $container->addDefinition($this->prefix('objectConverter'))
            ->setType(Converters\ObjectConverter::class)
            ->addTag(self::CONVERTER_TAG);
        $container->addDefinition($this->prefix('dateTimeConverter'))
            ->setType(Converters\DateTimeConverter::class)
            ->setArguments([$config['timeFormat']])
            ->addTag(self::CONVERTER_TAG);

        // Other available converters
        $container->addDefinition($this->prefix('camelCaseConverter'))
            ->setType(Converters\CamelCaseConverter::class);
        $container->addDefinition($this->prefix('pascalCaseConverter'))
            ->setType(Converters\PascalCaseConverter::class);
        $container->addDefinition($this->prefix('snakeCaseConverter'))
            ->setType(Converters\SnakeCaseConverter::class);

        // Determine which converter to use if any
        if ($config['convention'] === self::CONVENTION_SNAKE_CASE) {
            $container->getDefinition($this->prefix('snakeCaseConverter'))
                ->addTag(self::CONVERTER_TAG);
        } else if ($config['convention'] === self::CONVENTION_CAMEL_CASE) {
            $container->getDefinition($this->prefix('camelCaseConverter'))
                ->addTag(self::CONVERTER_TAG);
        } else if ($config['convention'] === self::CONVENTION_PASCAL_CASE) {
            $container->getDefinition($this->prefix('pascalCaseConverter'))
                ->addTag(self::CONVERTER_TAG);
        }

        // Load converters by tag
        $container->addDefinition($this->prefix('resourceConverter'))
            ->setType(Converters\ResourceConverter::class);
    }

    private function loadSecuritySection(ContainerBuilder $container, array $config): void
    {
        $container->addDefinition($this->prefix('security.hashCalculator'))
            ->setType(Security\HashCalculator::class)
            ->addSetup('$service->setPrivateKey(?)', [$config['security']['privateKey']]);

        $container->addDefinition($this->prefix('security.hashAuthenticator'))
            ->setType(Security\Authentication\HashAuthenticator::class)
            ->setArguments([$config['security']['privateKey']]);
        $container->addDefinition($this->prefix('security.timeoutAuthenticator'))
            ->setType(Security\Authentication\TimeoutAuthenticator::class)
            ->setArguments([$config['security']['requestTimeKey'], $config['security']['requestTimeout']]);

        $container->addDefinition($this->prefix('security.nullAuthentication'))
            ->setType(Security\Process\NullAuthentication::class);
        $container->addDefinition($this->prefix('security.securedAuthentication'))
            ->setType(Security\Process\SecuredAuthentication::class);
        $container->addDefinition($this->prefix('security.basicAuthentication'))
            ->setType(Security\Process\BasicAuthentication::class);

        $container->addDefinition($this->prefix('security.authentication'))
            ->setType(Security\AuthenticationContext::class)
            ->addSetup('$service->setAuthProcess(?)', [$this->prefix('@security.nullAuthentication')]);

        // enable OAuth2 in Restful
        if ($this->getByType($container, \Drahak\OAuth2\KeyGenerator::class)) {
            $container->addDefinition($this->prefix('security.oauth2Authentication'))
                ->setType(Security\Process\OAuth2Authentication::class);
        }
    }

    private function getByType(ContainerBuilder $container, string $type): ?ServiceDefinition
    {
        $definitions = $container->getDefinitions();
        foreach ($definitions as $definition) {
            if (($definition instanceof ServiceDefinition) && ($definition->class === $type)) {
                return $definition;
            }
        }
        return NULL;
    }

    private function loadAutoGeneratedRoutes(ContainerBuilder $container, array $config): void
    {
        $container->addDefinition($this->prefix('routeAnnotation'))
            ->setType(Application\RouteAnnotation::class);

        $container->addDefinition($this->prefix('routeListFactory'))
            ->setType(Application\RouteListFactory::class)
            ->setArguments([$config['routes']['presentersRoot'], $config['routes']['autoRebuild'], $config['cacheDir']])
            ->addSetup('$service->setModule(?)', [$config['routes']['module']])
            ->addSetup('$service->setPrefix(?)', [$config['routes']['prefix']]);

        $container->addDefinition($this->prefix('cachedRouteListFactory'))
            ->setType(Application\CachedRouteListFactory::class)
            ->setArguments([$config['routes']['presentersRoot'], $this->prefix('@routeListFactory')]);

        $statement = new Statement(
            'offsetSet',
            [
                NULL,
                new Statement($this->prefix('@cachedRouteListFactory') . '::create')
            ]
        );
        if ($config['routes']['generateAtStart']) {
            $setup = $container->getDefinition('router')
                ->getSetup();
            array_unshift($setup, $statement);
            $container->getDefinition('router')
                ->setSetup($setup);
        } else {
            $container->getDefinition('router')
                ->addSetup($statement);
        }
    }

    private function loadResourceRoutePanel(ContainerBuilder $container, array $config): void
    {
        $container->addDefinition($this->prefix('panel'))
            ->setType(Diagnostics\ResourceRouterPanel::class)
            ->setArguments([$config['security']['privateKey'], $config['security']['requestTimeKey'] ?? 'timestamp'])
            ->addSetup('\Tracy\Debugger::getBar()->addPanel(?)', ['@self']);

        $container->getDefinition('application')
            ->addSetup('$service->onStartup[] = ?', [[$this->prefix('@panel'), 'getTab']]);
    }

    /**
     * Before compile
     */
    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();

        $resourceConverter = $container->getDefinition($this->prefix('resourceConverter'));
        $services = $container->findByTag(self::CONVERTER_TAG);

        foreach ($services as $service => $args) {
            $resourceConverter->addSetup('$service->addConverter(?)', ['@' . $service]);
        }
    }
}
