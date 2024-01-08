<?php

namespace Tukan\Checkout\Plugin;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\AttributeMetadataConverter;
use Magento\Customer\Model\AttributeMetadataDataProvider;

class SaveCustomAddressAttributes
{
    /** @var AttributeMetadataConverter  */
    protected $attributeMetadataConverter;
    /** @var AttributeMetadataDataProvider  */
    protected $attributeMetadataDataProvider;
    /** @var array  */
    protected $additionalAddressAttributes = array(
        'pic_id'
    );

    /**
     * SaveCustomAddressAttributes constructor.
     * @param AttributeMetadataConverter $attributeMetadataConverter
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        AttributeMetadataConverter $attributeMetadataConverter,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->attributeMetadataConverter = $attributeMetadataConverter;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * Work around to ensure that custom address attribute get saved
     * @param \Magento\Customer\Model\Metadata\AddressMetadata $subject
     * @param array $attributes
     * @return array
     */
    public function afterGetAttributes(\Magento\Customer\Model\Metadata\AddressMetadata $subject, $attributes)
    {
        // Loop through our custom address attributes
        foreach ($this->additionalAddressAttributes as $additionalAddressAttribute) {
            // Only add attribute if ti hasn't already been added
            if (!isset($attributes[$additionalAddressAttribute])) {
                // Create an instance of our attribute
                try {
                    $attribute = $this->attributeMetadataDataProvider
                        ->getAttribute(
                            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                            $additionalAddressAttribute
                        );
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    // If for whatever reason our attribute doesn't exist then return the original array
                    return $attributes;
                }

                if ($attribute) {
                    // If we have retrieved an attribute then get the meta and add it to the attributes array
                    $convertedAttribute = $this->attributeMetadataConverter->createMetadataAttribute($attribute);
                    if ($convertedAttribute) {
                        $attributes[$additionalAddressAttribute] = $convertedAttribute;
                    }
                }

            }
        }
        return $attributes;
    }
}
