<?php declare(strict_types=1);

namespace Shopware\Search\Gateway\Resource;

use Shopware\Framework\Api2\ApiFlag\Required;
use Shopware\Framework\Api2\Field\FkField;
use Shopware\Framework\Api2\Field\ReferenceField;
use Shopware\Framework\Api2\Field\SubresourceField;
use Shopware\Framework\Api2\Field\TranslatedField;
use Shopware\Framework\Api2\Field\UuidField;

class ApiSearchFieldsResource extends ApiResource
{
    public function __construct()
    {
        parent::__construct('s_search_fields');
        
        $this->fields['name'] = (new StringField('name'))->setFlags(new Required());
        $this->fields['relevance'] = (new IntField('relevance'))->setFlags(new Required());
        $this->fields['field'] = (new StringField('field'))->setFlags(new Required());
        $this->fields['tableID'] = (new IntField('tableID'))->setFlags(new Required());
        
        
//        $this->primaryKeyFields['uuid'] = (new UuidField('uuid'))->setFlags(new Required());
//        $this->fields['name'] = new TranslatedField('name', ApiResourceShop::class, 'uuid');
//        $this->fields['description'] = new TranslatedField('description', ApiResourceShop::class, 'uuid');
//        $this->fields['descriptionLong'] = new TranslatedField('descriptionLong', ApiResourceShop::class, 'uuid');
//        $this->fields['productManufacturer'] = new ReferenceField('productManufacturerUuid', 'uuid', ApiResourceProductManufacturer::class);
//        $this->fields['productManufacturerUuid'] = new FkField('product_manufacturer_uuid', ApiResourceProductManufacturer::class, 'uuid');
//        $this->fields['taxUuid'] = new FKField('tax_uuid', ApiResourceTax::class, 'uuid');
//        $this->fields['details'] = new SubresourceField(ApiResourceProductDetail::class);
//        $this->fields['translations'] = (new SubresourceField(ApiResourceProductTranslation::class, 'languageUuid'))->setFlags(new Required());
    }
}