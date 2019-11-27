<?php /** @noinspection CallableParameterUseCaseInTypeContextInspection */

namespace Script;

use Doctrine\DBAL\Connection;
use Faker\Factory;
use Faker\Generator;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\RequestCriteriaBuilder;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\HttpKernel;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Test\TestDefaults;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreApi
{
    public ContainerInterface $container;
    public string $accessKey;
    public HttpKernel $kernel;
    public string $token;

    public SalesChannelContext $context;
    private Generator $faker;

    public function __construct(HttpKernel $kernel)
    {
        $this->faker = Factory::create();
        $this->kernel = $kernel;
        $this->token = Uuid::randomHex();
        $this->container = $kernel->getKernel()->getContainer();
        $this->accessKey = $this->container->get(Connection::class)
            ->fetchOne(
                'SELECT access_key FROM sales_channel WHERE id = :id',
                ['id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL)]
            );

        $this->container->get(Connection::class)->executeStatement('UPDATE sales_channel SET footer_category_version_id = :version, footer_category_id = (SELECT id FROM category WHERE child_count > 0 AND parent_id IS NOT NULL ORDER BY child_count DESC LIMIT 1) WHERE footer_category_id IS NULL', ['version' => Uuid::fromHexToBytes(Defaults::LIVE_VERSION)]);
        $this->container->get(Connection::class)->executeStatement('UPDATE sales_channel SET service_category_version_id = :version, service_category_id = (SELECT id FROM category WHERE child_count > 0 AND parent_id IS NOT NULL ORDER BY child_count DESC LIMIT 1) WHERE service_category_id IS NULL', ['version' => Uuid::fromHexToBytes(Defaults::LIVE_VERSION)]);

        $this->context = $this->container->get(SalesChannelContextService::class)->get(new SalesChannelContextServiceParameters(TestDefaults::SALES_CHANNEL, $this->token));
    }

    public function home(): Response
    {
        return $this->request('/store-api/category/home', []);
    }

    public function navigation(string $activeId = 'main-navigation'): Response
    {
        return $this->request('/store-api/navigation/' . $activeId . '/main-navigation', []);
    }

    public function footer(string $activeId = 'footer-navigation'): Response
    {
        return $this->request('/store-api/navigation/' . $activeId . '/footer-navigation', []);
    }

    public function service(string $activeId = 'service-navigation'): Response
    {
        return $this->request('/store-api/navigation/' . $activeId . '/service-navigation', []);
    }

    public function shipping_methods(): Response
    {
        return $this->request('/store-api/shipping-method');
    }

    public function payment_methods(): Response
    {
        return $this->request('/store-api/payment-method');
    }

    public function languages(): Response
    {
        return $this->request('/store-api/language');
    }

    public function currencies(): Response
    {
        return $this->request('/store-api/currency');
    }

    public function salutations(): Response
    {
        return $this->request('/store-api/salutation');
    }

    public function countries(): Response
    {
        return $this->request('/store-api/country');
    }

    public function listing(string $categoryId = null, Criteria $criteria = null)
    {
        $criteria = $criteria ?? new Criteria();

        $params = $this->container->get(RequestCriteriaBuilder::class)->toArray($criteria);

        $categoryId = $categoryId ?? $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(id)) FROM category WHERE parent_id IS NOT NULL ORDER BY RAND() LIMIT 1');

        return $this->request('/store-api/category/' . $categoryId, $params);
    }

    public function product(string $productId = null)
    {
        $productId = $productId ?? $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(id)) FROM product ORDER BY RAND() LIMIT 1');

        return $this->request('/store-api/product/' . $productId);
    }

    public function cross_selling(string $productId = null)
    {
        $productId = $productId ?? $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(product_id)) FROM product_cross_selling ORDER BY RAND() LIMIT 1');

        if ($productId === false) {
            $productId = $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(id)) FROM product ORDER BY RAND() LIMIT 1');
        }

        return $this->request('/store-api/product/' . $productId . '/cross-selling');
    }

    public function search(?string $keyword = null)
    {
        $keyword = $keyword ?? $this->container->get(Connection::class)->fetchOne('SELECT name FROM product_translation ORDER BY RAND() LIMIT 1');

        return $this->request('/store-api/search', ['search' => $keyword]);
    }

    public function suggest(?string $keyword = null)
    {
        $keyword = $keyword ?? $this->container->get(Connection::class)->fetchOne('SELECT name FROM product_translation ORDER BY RAND() LIMIT 1');

        return $this->request('/store-api/search-suggest', ['search' => $keyword]);
    }

    public function cart()
    {
        return $this->request('/store-api/checkout/cart');
    }

    public function add_product(string $productId = null)
    {
        if ($productId === null) {
            $productId = $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(id)) FROM product WHERE child_count = 0 OR parent_id IS NOT NULL ORDER BY RAND() LIMIT 1');
        }

        return $this->request('/store-api/checkout/cart/line-item', [
            'items' => [
                ['type' => 'product', 'id' => $productId, 'referencedId' => $productId]
            ]
        ]);
    }

    public function order()
    {
        return $this->request('/store-api/checkout/order');
    }

    public function register()
    {
        $response = $this->request('/store-api/account/register', [
            'storefrontUrl' => $this->container->get(Connection::class)->fetchOne('SELECT url FROM sales_channel_domain WHERE sales_channel_id = :id', ['id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL)]),
            'salutationId' => $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(id)) FROM salutation ORDER BY RAND() LIMIT 1'),
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => Uuid::randomHex(),
            'acceptedDataProtection' => true,
            'billingAddress' => [
                'street' => $this->faker->streetAddress,
                'zipcode' => $this->faker->postcode,
                'city' => $this->faker->city,
                'countryId' => $this->container->get(Connection::class)->fetchOne('SELECT LOWER(HEX(country_id)) FROM sales_channel WHERE id = :id', ['id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL)]),
            ]
        ]);

        $this->token = $response->headers->get('sw-context-token');

        return $response;
    }

    public function logout()
    {
        return $this->request('/store-api/account/logout');
    }

    public function context()
    {
        return $this->request('/store-api/context');
    }

    public function customer()
    {
        return $this->request('/store-api/account/customer');
    }

    private function request(string $url, array $params = [])
    {
        $request = new Request();
        $request->request->add($params);
        $request->server->set('REQUEST_URI', $url);
        $request->setMethod('POST');

        $request->headers->set(PlatformRequest::HEADER_ACCESS_KEY, $this->accessKey);
        $request->headers->set(PlatformRequest::ATTRIBUTE_OAUTH_CLIENT_ID, $this->accessKey);
        $request->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $this->token);
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Accept', 'application/json');

        $response = $this->kernel->handle($request);

        $this->container->get('services_resetter')->reset();

        if ($response->getResponse()->getStatusCode() !== Response::HTTP_OK) {
            \var_dump([
                'url' => $url,
                'content' => $response->getResponse()->getContent()
            ]);

            die();
        }

        return $response->getResponse();
    }

    public function getToken(): string
    {
        return $this->token;
    }
}

//$returnKernel = true;
//
///** @var HttpKernel $kernel */
//$kernel = require  __DIR__ . '/boot.php';
//
//$api = new StoreApi($kernel);
//
//return [$api, $kernel->getKernel()->getContainer()];

