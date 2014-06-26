<?php

namespace Shopware\Bundle\SearchBundle\DBAL\SortingHandler;

use Shopware\Bundle\SearchBundle\DBAL\PriceHelper;
use Shopware\Bundle\SearchBundle\DBAL\SortingHandlerInterface;
use Shopware\Bundle\SearchBundle\Sorting\PriceSorting;
use Shopware\Bundle\SearchBundle\SortingInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Context;
use Shopware\Components\Model\DBAL\QueryBuilder;

class PriceSortingHandler implements SortingHandlerInterface
{
    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @param PriceHelper $priceHelper
     */
    function __construct(PriceHelper $priceHelper)
    {
        $this->priceHelper = $priceHelper;
    }

    /**
     * Checks if the passed sorting can be handled by this class
     * @param SortingInterface $sorting
     * @return bool
     */
    public function supportsSorting(SortingInterface $sorting)
    {
        return ($sorting instanceof PriceSorting);
    }

    /**
     * Handles the passed sorting object.
     * Extends the passed query builder with the specify sorting.
     * Should use the addOrderBy function, otherwise other sortings would be overwritten.
     *
     * @param SortingInterface|PriceSorting $sorting
     * @param QueryBuilder $query
     * @param Context $context
     * @return void
     */
    public function generateSorting(
        SortingInterface $sorting,
        QueryBuilder $query,
        Context $context
    ) {
        $selection = $this->priceHelper->getCheapestPriceSelection(
            $context->getCurrentCustomerGroup()
        );

        $this->priceHelper->joinPrices(
            $query,
            $context->getCurrentCustomerGroup(),
            $context->getFallbackCustomerGroup()
        );

        $query->addSelect($selection . ' as cheapest_price');

        $query->addOrderBy('cheapest_price', $sorting->getDirection())
            ->addOrderBy('products.id', $sorting->getDirection());
    }

}