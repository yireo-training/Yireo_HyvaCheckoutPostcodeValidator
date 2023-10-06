<?php declare(strict_types=1);

namespace Yireo\HyvaCheckoutPostcodeValidator\Form\ShippingAddress;

use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use InvalidArgumentException;
use Magento\Directory\Model\Country\Postcode\ValidatorInterface;

class PostcodeFormModifier implements EntityFormModifierInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'Yireo_HyvaCheckoutPostcodeValidator::validatePostcode',
            'form:build:magewire',
            [$this, 'validatePostcode']
        );

        return $form;
    }

    public function validatePostcode(EntityFormInterface $form, AbstractMagewireAddressForm $component)
    {
        $postcode = $form->getField('postcode');
        if (false === $postcode->getValue()) {
            return;
        }

        $country = $form->getField('country_id');
        if (false === $country->getValue()) {
            return;
        }

        try {
            $isValid = $this->validator->validate($postcode->getValue(), $country->getValue());
            $isInvalidMessage = __('Invalid postcode "'.$postcode->getValue().'" for country "'.$country->getValue().'"');
        } catch (InvalidArgumentException $invalidArgumentException) {
            return;
        }

        if ($isValid) {
            return;
        }

        $postcode->setAttribute('data-magewire-is-valid', '0');
        $postcode->setAttribute('data-msg-magewire', $isInvalidMessage);
    }
}
