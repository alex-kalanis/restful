<?php

namespace Picabo\Restful\DI;

use Nette;
use Nette\Bootstrap\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\DI\MissingServiceException;
use Nette\Utils\Validators;
use Picabo\OAuth2\KeyGenerator;
use Picabo\Restful\Application;
use Picabo\Restful\Converters;
use Picabo\Restful\Diagnostics;
use Picabo\Restful\Http;
use Picabo\Restful\IResource;
use Picabo\Restful\Mapping;
use Picabo\Restful\ResourceFactory;
use Picabo\Restful\Security;
use Picabo\Restful\Utils;
use Picabo\Restful\Validation;

/**
 * Picabo RestfulExtension
 * @package Picabo\Restful\DI
 * @author Drahomír Hanák
 * @template Conf of array{
 *     convention: string|null,
 *     timeFormat: string,
 *     cacheDir: string,
 *     jsonpKey: string,
 *     prettyPrint: bool,
 *     prettyPrintKey: string,
 *     routes: array{
 *         generateAtStart: bool,
 *         presentersRoot: string,
 *         autoGenerated: bool,
 *         autoRebuild: bool,
 *         module: string,
 *         prefix: string,
 *         panel: bool
 *     },
 *     security: array{
 *         privateKey: string|null,
 *         requestTimeKey?: string|null,
 *         requestTimeout?: int|null
 *     },
 *     resourceRoute?: array{
 *         mask?: string,
 *         metadata?: string|array{
 *             action?: string|string[],
 *         },
 *         flags?: int,
 *     },
 *     mappers?: array<string, array{
 *         contentType: string,
 *         class: string
 *     }>
 * }
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
     * @var Conf
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
        'resourceRoute' => [
            'mask' => '',
            'metadata' => [],
            'flags' => Application\IResourceRouter::CRUD,
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
        /** @var Conf $config */
        $config = array_merge($this->defaults, (array) $this->getConfig());

        // Additional module
        $this->loadRestful($container, $config);
        $this->loadValidation($container);
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
     * @param Conf $config
     * @throws Nette\Utils\AssertionException
     * @return void
     */
    private function loadRestful(ContainerBuilder $container, array $config): void
    {
        Validators::assert($config['prettyPrintKey'], 'string');
        $this->startLocalRouter($container, $config);

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
            ->setType(Application\MethodOptions::class)
            ->setArguments([$container->getDefinition('router')])
        ;

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
            ->addSetup('$service->addMapper(?, ?)', [IResource::NULL, $this->prefix('@nullMapper')])
        ;

        if (isset($config['mappers'])) {
            foreach ($config['mappers'] as $mapperName => $mapper) {
                $container->addDefinition($this->prefix($mapperName))
                    ->setType($mapper['class']);

                $mapperService = $container->getDefinition($this->prefix('mapperContext'));
                /** @var ServiceDefinition $mapperService */
                $mapperService->addSetup('$service->addMapper(?, ?)', [$mapper['contentType'], $this->prefix('@' . $mapperName)]);
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

        $request = $container->getDefinition('httpRequest');
        /** @var ServiceDefinition $request */
        $request->setFactory($this->prefix('@httpRequestFactory') . '::createHttpRequest');

        $response = $container->getDefinition('httpResponse');
        /** @var ServiceDefinition $response */
        $response->setFactory($this->prefix('@httpResponseFactory') . '::createHttpResponse');

        $container->addDefinition($this->prefix('requestFilter'))
            ->setType(Utils\RequestFilter::class)
            ->setArguments(['@httpRequest', [$config['jsonpKey'], $config['prettyPrintKey']]]);

        $container->addDefinition($this->prefix('methodHandler'))
            ->setType(Application\Events\MethodHandler::class);

        $app = $container->getDefinition('application');
        /** @var ServiceDefinition $app */
        $app->addSetup('$service->onStartup[] = ?', [[$this->prefix('@methodHandler'), 'run']])
            ->addSetup('$service->onError[] = ?', [[$this->prefix('@methodHandler'), 'error']]);
    }

    private function loadValidation(ContainerBuilder $container): void
    {
        $container->addDefinition($this->prefix('validator'))
            ->setType(Validation\Validator::class);

        $container->addDefinition($this->prefix('validationScopeFactory'))
            ->setType(Validation\ValidationScopeFactory::class);

        $container->addDefinition($this->prefix('validationScope'))
            ->setType(Validation\ValidationScope::class)
            ->setFactory($this->prefix('@validationScopeFactory') . '::create');

    }

    /**
     * @param ContainerBuilder $container
     * @param Conf $config
     * @throws Nette\Utils\AssertionException
     * @return void
     */
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

    /**
     * @param ContainerBuilder $container
     * @param Conf $config
     * @return void
     */
    private function loadSecuritySection(ContainerBuilder $container, array $config): void
    {
        $container->addDefinition($this->prefix('security.hashCalculator'))
            ->setType(Security\HashCalculator::class)
            ->addSetup('$service->setPrivateKey(?)', [$config['security']['privateKey']]);

        $container->addDefinition($this->prefix('security.hashAuthenticator'))
            ->setType(Security\Authentication\HashAuthenticator::class)
        ;
        $container->addDefinition($this->prefix('security.timeoutAuthenticator'))
            ->setType(Security\Authentication\TimeoutAuthenticator::class)
            ->setArguments([
                $config['security']['requestTimeKey'] ?? 'timestamp',
                $config['security']['requestTimeout'] ?? 600
            ]);

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
        if ($this->getByType($container, KeyGenerator::class)) {
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

    /**
     * @param ContainerBuilder $container
     * @param Conf $config
     * @return void
     */
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
        // jak to sakra funguje?
        // -> init routeru v hlavnim nette, pripadne tady a pak vyuziti s doplnenim parametru, co jsou v config neonu
        $this->startLocalRouter($container, $config);
        if ($config['routes']['generateAtStart']) {
            /** @var ServiceDefinition $def */
            $def = $container->getDefinition('router');
            $setup = $def->getSetup();
            array_unshift($setup, $statement);
            /** @var ServiceDefinition $def */
            $def = $container->getDefinition('router');
            $def->setSetup($setup);
        } else {
            /** @var ServiceDefinition $def */
            $def = $container->getDefinition('router');
            $def->addSetup($statement);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param Conf $config
     * @return void
     */
    private function startLocalRouter(ContainerBuilder $container, array $config): void
    {
        try {
            $container->getDefinition('router');
        } catch (MissingServiceException) {
            $container->addDefinition('router')
                ->setType(Application\Routes\ResourceRoute::class)
                ->setArguments([
                    $config['resourceRoute']['mask'] ?? '',
                    $config['resourceRoute']['metadata'] ?? [],
                    $config['resourceRoute']['flags'] ?? Application\IResourceRouter::CRUD
                ])
            ;
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param Conf $config
     * @return void
     */
    private function loadResourceRoutePanel(ContainerBuilder $container, array $config): void
    {
        $container->addDefinition($this->prefix('panel'))
            ->setType(Diagnostics\ResourceRouterPanel::class)
            ->setArguments([$config['security']['privateKey'], $config['security']['requestTimeKey'] ?? 'timestamp'])
            ->addSetup('\Tracy\Debugger::getBar()->addPanel(?)', ['@self']);

        /** @var ServiceDefinition $serviceDef */
        $serviceDef = $container->getDefinition('application');
        $serviceDef->addSetup('$service->onStartup[] = ?', [[$this->prefix('@panel'), 'getTab']]);
    }

    /**
     * Before compile
     */
    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        /** @var ServiceDefinition $resourceConverter */
        $resourceConverter = $container->getDefinition($this->prefix('resourceConverter'));
        $services = $container->findByTag(self::CONVERTER_TAG);

        foreach ($services as $service => $args) {
            $resourceConverter->addSetup('$service->addConverter(?)', ['@' . $service]);
        }
    }
}
