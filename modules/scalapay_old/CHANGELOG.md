# Changelog scalapay module prestashop 1.6/1.7/8

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.2] - 2023-06-09
### Fixed
Fix the widget on the checkout page when the title and price configuration are set. SP-10860 

## [2.1.1] - 2023-06-08
### Added:
Fixed widget appearance on the checkout page. SP-10830
Fixed issue on in-page hook management.

## [2.1.0] - 2023-05-30
### Added
[SP-9597] Implemented In Page Checkout mode

## [2.0.8] - 2023-04-27
###Added
- SP-9939: Add 'merchantRefundReference' key in refund call.

## [2.0.7] - 2023-03-21
###Added
- SP-9478: Add void call and set the order as cancelled if impossible to capture the amount.
- SP-9470: Added pageUrl and imageUrl to scalapay request 

###Fixed
- minor fixes

## [2.0.6] - 2023-03-15
### Fixed

- SP-9475: Positioning the widget after the selector and as the last element in the selector.
- Minor Fixes

## [2.0.5] - 2023-03-07
### Fixed
- Installation on multistore

## [2.0.4] - 2023-02-14
### Fixed
- Default configuration for PS 1.6.
- Multistore compatibility.

## [2.0.3] - 2023-02-07
### Added
- Compatibility with PS 1.6 1.7 and 8.
- Compatibility with old version of plugin.
- New Configurations to permit more granularity settings.
- Checkout widget.
- Validate configurations on save.
### Removed
- Unused code.

## [1.2.45] - 2022-10-10
### Added
- Loading javascript CDN runtime
- Default css for paylater widget to adjust layout
- Added a option to enable/disable scalapay support for digital/vitual products
- Added a fallback div on page, holding prices in case price selectors does not work

### Fixed
- Staging endpoint updated
- Widget position after page loads on product page

## [1.2.44] - 2022-08-05
### Fixed
- Removed extra fields which are not useable for widget selectors

## [1.2.43] - 2022-07-05
### Added
- Added Pay in 3 , four and PayLater features

## [1.2.42] - 2022-03-16
### Fixed
- Javascript smarty protects.
- Added htaccess file for module files protection.

## [1.2.41] - 2022-03-04
### Fixed
- Spanish language text updates for checkout page.

## [1.2.40] - 2022-02-18
### Fixed
- Spanish language text updates for checkout page.

## [1.2.39] - 2022-02-01
### Added
- Facilitate user to set scalapay for pay in 4 payments as well.

## [1.2.38] - 2022-02-01
### Fixed
-  Fix Refund Javascript error for prestashop latest version 1.7.8

## [1.2.37] - 2022-02-01
### Added
-  Customized value in plugin details

## [1.2.36] - 2022-01-28
### Added
-  Cleaned some code

## [1.2.35] - 2022-01-07
### Added
-  Changed staging endpoint

## [1.2.34] - 2021-12-30
### Added
-  Support for spanish language

## [1.2.32] - 2021-09-27
### Added
-  Allow the merchant to add a free text in the Scalapay settings of the module and that will display bellow the widget.

## [1.2.31] - 2021-07-07
### Changed
-  Fixed refund feature for prestashop 1.7.7 releases.

## [1.2.31] - 2021-05-10
### Changed
-  Fixed French text on checkout page.

## [1.2.28] - 2021-04-21
### Fixed
-  Added support for Search Engine Friendly urls after success payment on scalapay server.
-  count warning for php 7.2 fix

## [1.2.27] - 2020-12-16
### Fixed
-  Fix redirectConfirmUrl and redirectCancelUrl  and use HTTPS if website support it

## [1.2.26] - 2020-11-27
### Added
-  Added Hook for JS load for widget 

## [1.2.25] - 2020-11-16
### Added
-  Add Scalapay plugin and platform version in order payload data

## [1.2.24] - 2020-11-03
### Added
-  Prestashop module versioning with in script

## [1.2.23] - 2020-11-03
### Added
-  Updated version in the Bitbucket pipeline with each merge to master

## [1.2.22] - 2020-09-16
### Removed
- Remove checkout translation text from plugin UI

### Added
-  Add checkout translation text in language file 

## [1.2.22] - 2020-09-16
### Removed
- Remove checkout translation text from plugin UI

### Added
-  Add checkout translation text in language file 

## [1.2.22] - 2020-09-16
### Removed
- Remove checkout translation text from plugin UI

### Added
-  Add checkout translation text in language file 


## [1.2.21] - 2020-08-28
### Added
- Order Refund checkbox at backend admin order edit page


## [1.2.20] - 2020-08-26
### Fixed
-  Updated Live API key issue for production

## [1.2.19] - 2020-08-25
### Fixed
- Apply checks not to install on prestashop version 1.6

## [1.2.18] - 2020-08-20
### Fixed
- Updated backend field titles with the correct one's

### Added
- Add translation for text on checkout in the language translation file

## [1.2.17] - 2020-08-20
### Removed
- Remove language translation from Plugin configuration settings/labels

### Added
- Separate test and live API in the plugin configuration

## [1.2.16] - 2020-07-30
### Fixed
- Update the italian Scalapay logo at checkout to use the standard logo and not the logo with 'finanziamento' anymore.

## Removed
- Remove empty spaces from price for product detail page


## [1.2.15] - 2020-07-20

### Removed
- Remove cart ID as merchant reference from the payload data

## [1.2.14] - 2020-07-14
### Added
- Order status set to payment accepted by default at module configurations

## [1.2.13] - 2020-07-13
### Added
- Maintain order status history at backend order edit page


## [1.2.12] - 2020-07-13
### Added
- Add option to show/hide price for widget at module configurations


## [1.2.11] - 2020-06-04
### Fixed
- Fixed warnings at module configurations for empty array


## [1.2.10] - 2020-06-04
### Added
- Do capture call after order creation (when return from scalapay after payment)
- Update merchant reference in capture call , no need to do a separate call


## [1.2.9] - 2020-05-12
### Added
- Change plugin config UI to host category restriction feature and widget number of payment and max amount


## [1.2.8] - 2020-05-04
### Added
- Restricted our payment method for selected product categories. Also added max/min limit on widget to show/hide 


## [1.2.7] - 2020-04-08
### Fixed
- Use standard Scalapay logo for EN, FR and GE without text above the logo

## [0.1.6] - 2020-04-07
### Removed
- Clean Up Errors for Code Sniffer

## [0.1.4] - 2020-04-07
### Added
-  Integrate Widget for product and cart pages
-  cart page widget positioning by selectors

### Removed
-  Remove v1/Configuration Call

## [0.1.3] - 2020-03-11
### Added
- Added Scalapay logo for France

## [0.0.1] - 2020-03-05
### Added
- added controller for multilanguage support