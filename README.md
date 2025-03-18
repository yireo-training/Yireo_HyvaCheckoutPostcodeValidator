# Yireo HyvaCheckoutPostcodeValidator

This Magento 2 module adds postcode validation based upon the Magento core `zip_codes.xml` mechanism to the Hyva Checkout (aka MageWire Checkout)

**This module is abandoned and no longer maintained. We have moved to our new [LokiCheckout](https://loki-checkout.com/) instead.**


### Installation
```bash
composer require yireo/magento2-hyva-checkout-postcode-validator
bin/magento module:enable Yireo HyvaCheckoutPostcodeValidator
```

### Usage
This module implements the Magento `\Magento\Directory\Model\Country\Postcode\ValidatorInterface` in 3 different ways:

- Client-side (JS) validation directly on postcode input (with a spinner);
- Lazy client-side (Hyva Checkout validation) based on MageWire validator;
- The Hyva Checkout form hook `form:build:magewire` is used to allow for the final form submission to be validated;

### Todos
- Check with Yireo ExtensionChecker properly
- Setup GitHub actions
