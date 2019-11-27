<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Media\DataAbstractionLayer\MediaIndexer;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Media\Message\GenerateThumbnailsHandler;
use Shopware\Core\Content\Media\Message\GenerateThumbnailsMessage;
use Shopware\Core\Content\Product\DataAbstractionLayer\ProductIndexer;
use Shopware\Core\Content\Product\DataAbstractionLayer\ProductStreamUpdater;
use Shopware\Core\Content\Product\DataAbstractionLayer\SearchKeywordUpdater;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Storefront\Framework\Seo\SeoUrlRoute\SeoUrlUpdateListener;
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

    public function generateThumbnails()
    {
        $this->getContainer()->get(Connection::class)
            ->executeStatement('DELETE FROM media_thumbnail');

        $iterator = $this->getContainer()->get(IteratorFactory::class)
            ->createIterator($this->getContainer()->get(MediaDefinition::class));

        $context = Context::createDefaultContext();

        $handler = $this->getContainer()->get(GenerateThumbnailsHandler::class);

        $time = microtime(true);
        while ($ids = $iterator->fetch()) {
            $message = new GenerateThumbnailsMessage();
            $message->setMediaIds($ids);
            $message->withContext($context);

            $handler->handle($message);
        }

        echo sprintf('%s: time[%s]', str_pad("generating", 20), round(microtime(true)-$time, 5));
    }

    public function indexMedia()
    {
        $indexer = $this->getContainer()->get(MediaIndexer::class);

$time = microtime(true);
        $offset = null;
        while ($message = $indexer->iterate($offset)) {
            $indexer->handle($message);
            $offset = $message->getOffset();
        }
        echo sprintf('%s: time[%s]', str_pad("indexer", 20), round(microtime(true)-$time, 5));

    }

    public function indexProducts()
    {
        $indexer = $this->getContainer()->get(ProductIndexer::class);

        $time = microtime(true);
        $offset = null;

        $max = 1;

        $calls = 0;

        while ($message = $indexer->iterate($offset)) {
            $message->setSkip([
                ProductIndexer::SEARCH_KEYWORD_UPDATER,
                SeoUrlUpdateListener::PRODUCT_SEO_URL_UPDATER
            ]);
            $indexer->handle($message);

            $offset = $message->getOffset();

            if ($max === null) {
                continue;
            }

            $calls ++;
            if ($calls >= $max) {
                break;
            }
        }

        echo PHP_EOL . sprintf('%s: time[%s]', str_pad("indexer", 20), round(microtime(true)-$time, 5));
        echo PHP_EOL;
    }
}

try {
    $x = new Main(require __DIR__ . '/boot/boot_prod.php');

//    $x->generateThumbnails();
//    $x->indexMedia();
    $x->indexProducts();
} catch (\Throwable $e) {
    dd($e);
}

