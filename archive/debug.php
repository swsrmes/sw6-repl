<?php

namespace Script;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Main
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function run()
    {
        $customerRepository = $this->getContainer()->get('customer.repository');

        $criteria = new Criteria();
        $criteria->addAssociation('language.categoryTranslations');
        $criteria->setLimit(2);

        $customers = $this->getContainer()->get('customer.repository')
            ->search($criteria, Context::createDefaultContext())
            ->getEntities();

        /** @var CustomerEntity $customer */
        foreach ($customers as $customer) {
            if ($customer->getLanguage()->getCategoryTranslations()) {
                dump($customer->getLanguage()->getCategoryTranslations()->count());
            } else {
                dump(0);
            }
        }
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

