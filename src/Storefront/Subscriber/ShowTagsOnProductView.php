<?php declare(strict_types=1);

namespace Sas\SasTags\Storefront\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ShowTagsOnProductView implements EventSubscriberInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $tagRepository;

    /**
     * ShowTagsOnProductView constructor.
     * @param EntityRepositoryInterface $tagRepository
     */
    public function __construct(EntityRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'getAllTags'
        ];
    }

    /**
     * @param ProductPageLoadedEvent $event
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    public function getAllTags(ProductPageLoadedEvent $event): void
    {

        $tagIds = $event->getPage()->getProduct()->getTagIds();

        if($tagIds == null) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('id', $tagIds));

        $tagResults = $this->tagRepository->search($criteria, $event->getContext());

        $event->getPage()->addExtension('tags', $tagResults);
    }
}
