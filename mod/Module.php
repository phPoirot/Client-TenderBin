<?php
namespace Module\TenderBinClient
{
    use Module\TenderBinClient\Handler\HandleTenderBin;
    use Poirot\Application\ModuleManager\Interfaces\iModuleManager;
    use Poirot\Std\Interfaces\Struct\iDataEntity;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Ioc\Container;
    use Poirot\Ioc\Container\BuildContainer;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
    use Poirot\Loader\Interfaces\iLoaderAutoload;
    use Poirot\TenderBinClient\FactoryMediaObject;


    class Module implements Sapi\iSapiModule
        , Sapi\Module\Feature\iFeatureModuleInitSapi
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleInitModuleManager
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleNestServices
    {
        /**
         * @inheritdoc
         */
        function initialize($sapi)
        {
            // Add Media TenderBin Handler
            //
            FactoryMediaObject::addHandler( new HandleTenderBin );

        }

        /**
         * Register class autoload on Autoload
         *
         * priority: 1000 B
         *
         * @param LoaderAutoloadAggregate $baseAutoloader
         *
         * @return iLoaderAutoload|array|\Traversable|void
         */
        function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
        {
            #$nameSpaceLoader = \Poirot\Loader\Autoloader\LoaderAutoloadNamespace::class;
            $nameSpaceLoader = 'Poirot\Loader\Autoloader\LoaderAutoloadNamespace';
            /** @var LoaderAutoloadNamespace $nameSpaceLoader */
            $nameSpaceLoader = $baseAutoloader->loader($nameSpaceLoader);
            $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);
        }

        /**
         * Initialize Module Manager
         *
         * priority: 1000 C
         *
         * @param iModuleManager $moduleManager
         *
         * @return void
         */
        function initModuleManager(iModuleManager $moduleManager)
        {
            // ( ! ) ORDER IS MANDATORY

            if (!$moduleManager->hasLoaded('OAuth2Client'))
                // Load OAuth2 Client To Assert Tokens.
                $moduleManager->loadModule('OAuth2Client');
        }

        /**
         * @inheritdoc
         */
        function initConfig(iDataEntity $config)
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-tenderbin_client');
        }

        /**
         * Get Nested Module Services
         *
         * it can be used to manipulate other registered services by modules
         * with passed Container instance as argument.
         *
         * priority not that serious
         *
         * @param Container $moduleContainer
         *
         * @return null|array|BuildContainer|\Traversable
         */
        function getServices(Container $moduleContainer = null)
        {
            $conf = \Poirot\Config\load(__DIR__ . '/../config/mod-tenderbin_client.services', true);
            return $conf;
        }
    }
}

namespace Module\TenderBinClient
{
    use Poirot\TenderBinClient\Client;

    /**
     * @method static Client ClientTender()
     */
    class Services extends \IOC
    { }
}
