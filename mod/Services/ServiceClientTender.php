<?php
namespace Module\TenderBinClient\Services;

use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\OAuth2Client\Federation\TokenProvider\TokenFromOAuthClient;
use Poirot\OAuth2Client\Grant\Container\GrantPlugins;
use Poirot\TenderBinClient\Client;


class ServiceClientTender
    extends aServiceContainer
{
    const CONF = 'client_tender';

    /** @var string Service Name */
    protected $name = 'clientTender';

    protected $serverUrl;


    /**
     * Create Service
     *
     * @return Client
     * @throws \Exception
     */
    function newService()
    {
        $serverUrl = $this->serverUrl;

        if ( empty($serverUrl) )
            throw new \Exception('Server Url For Client Tenderbin Not Set.');

        /** @var \Poirot\OAuth2Client\Client $oauthClient */
        $oauthClient = $this->services()->get('/module/OAuth2Client/services/OAuthClient');
        $c = new Client(
            $serverUrl
            , new TokenFromOAuthClient(
                $oauthClient
                , $oauthClient->withGrant(GrantPlugins::CLIENT_CREDENTIALS, ['scopes' => ['tenderbin']])
            )
        );

        return $c;
    }


    // ..

    /**
     * @param mixed $serverUrl
     */
    function setServerUrl($serverUrl)
    {
        $this->serverUrl = $serverUrl;
    }
}
