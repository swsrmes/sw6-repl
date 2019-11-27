<?php

namespace Script;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\RequestCriteriaBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class Api
{
    private string $host;
    private ?string $token = null;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->host = \getenv('APP_URL');
        $this->container = $container;
    }

    public function searchIds(string $entity, Criteria $criteria): array
    {
        $params = $this->container->get(RequestCriteriaBuilder::class)->toArray($criteria);

        return $this->request('/api/search-ids/' . $entity, $params);
    }

    public function search(string $entity, Criteria $criteria): array
    {
        $params = $this->container->get(RequestCriteriaBuilder::class)->toArray($criteria);

        return $this->request('/api/search/' . $entity, $params);
    }

    public function allIds(string $entity, ?Criteria $criteria = null): array
    {
        $all = [];
        $criteria = $criteria ?? new Criteria();

        $criteria->setLimit($criteria->getLimit() ?? 200);

        do {
            $response = $this->searchIds($entity, $criteria);

            $criteria->setOffset($criteria->getOffset() + $criteria->getLimit());

            $all[] = $response['data'];

        } while($response['total'] > 0);

        return array_merge(...$all);
    }

    private function getAccessToken()
    {
        if ($this->token !== null) {
            return $this->token;
        }

        $oauth = [
            'client_id' => 'administration',
            'grant_type' => 'password',
            'scopes' => 'write',
            'username' => 'admin',
            'password' => 'shopware'
        ];

        $response = $this->request('/api/oauth/token', $oauth);

        return $this->token = $response['access_token'];
    }

    private function request(string $url, array $params = [])
    {
        $resource = curl_init($this->host . $url);

        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_POST, true);
        curl_setopt($resource, CURLOPT_POSTFIELDS, json_encode($params));

        $headers = ['Content-Type:application/json', 'Accept:application/json'];
        if ($this->getAccessToken()) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }

        curl_setopt($resource, CURLOPT_HTTPHEADER, $headers);

        return json_decode(curl_exec($resource), true);
    }
}

//
///** @var ContainerInterface $container */
//$container = require  __DIR__ . '/boot.php';
//
//
//
//$api = new Api($container);
//
//return [$api, $container];

