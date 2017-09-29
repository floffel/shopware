<?php declare(strict_types=1);

namespace Shopware\ShopTemplate\Extension;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Factory\ExtensionInterface;
use Shopware\Search\QueryBuilder;
use Shopware\Search\QuerySelection;
use Shopware\ShopTemplate\Event\ShopTemplateBasicLoadedEvent;
use Shopware\ShopTemplate\Event\ShopTemplateWrittenEvent;
use Shopware\ShopTemplate\Struct\ShopTemplateBasicStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ShopTemplateExtension implements ExtensionInterface, EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ShopTemplateBasicLoadedEvent::NAME => 'shopTemplateBasicLoaded',
            ShopTemplateWrittenEvent::NAME => 'shopTemplateWritten',
        ];
    }

    public function joinDependencies(
        QuerySelection $selection,
        QueryBuilder $query,
        TranslationContext $context
    ): void {
    }

    public function getDetailFields(): array
    {
        return [];
    }

    public function getBasicFields(): array
    {
        return [];
    }

    public function hydrate(
        ShopTemplateBasicStruct $shopTemplate,
        array $data,
        QuerySelection $selection,
        TranslationContext $translation
    ): void {
    }

    public function shopTemplateBasicLoaded(ShopTemplateBasicLoadedEvent $event): void
    {
    }

    public function shopTemplateWritten(ShopTemplateWrittenEvent $event): void
    {
    }
}