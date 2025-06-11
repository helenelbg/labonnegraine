# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.2] - 2023-05-16
### Added
- Dematerialization of documents for new destinations if DDP is activated
- New UX of the "backoffice configuration" tab
- Configuration of the shipping category for CN23 at postage

## [2.0.1] - 2023-03-06
### Added
- Optional phone fields for Pickup Points
- Configurable door code fields
- Tariff weighting
- Addition of countries eligible for RDPs
- Return label available on mobile

### Fixed
- Fix download customs documents
- Fix apostrophe issue in widget address

## [1.6.7] - 2022-08-04
### Fixed
- Fix tracking url

## [1.6.6] - 2022-07-04
### Added
- Restriction on phone number for mobile only on pickup point
- Extend HS code field to 10 digits
- PrestaShop datas

## [1.6.5] - 2022-05-18
### Added
- Download the printing kit from the module BO
- Adding a cron task : Adding a CTA to update all the trackings from module board
- Adding a videos tab to help users to configure and use the module
- Generate a label with insurance when the amount is above 1000 euros

### Fixed
- Pickup point widget : Fix icons for V1.6 PrestaShop

### Changed
- Pickup point widget : Wording evolution

## [1.6.4] - 2022-03-31
### Fixed
- Fix closing widget bug
- Fix truncated map issue on widget

## [1.6.3] - 2022-03-08
### Added
- Add option to use or not the default Colissimo widget

### Fixed
- Fix tracking link bug
- Fix lag of address validation button on widget

## [1.6.2] - 2022-02-09
### Added
- Widget V2

### Fixed
- Fix bug create colissimo address table

## [1.6.1] - 2022-01-18
### Added
- Add the door code 1 and door code 2 fields in the delivery step
- Attach orders to the plugin in bulk
- ADD Delivery duty paid offer
- International offer: Choice of the Postal partner

### Fixed
- Fix click list PDR
- Search blocked by tracking number field in order list

## [1.6.0] - 2021-12-10
### Added
- International: Add the default origin country to the whole site
- Customs 2021
- Eco Overseas
- Replace the order reference with the order number

### Fixed
- Colissimo form inputs

## [1.5.1] - 2021-07-28
### Added
- UK EORI number can be configured

### Changed
- Removal of auto-postage modal
- Rollback Brexit (no configuration needed anymore)

### Fixed
- Missing tokens in Postage interface

## [1.5.0] - 2021-06-15
### Added
- Multi package: possibility to send the products through several packages. create several labels and several CN23
- Possibility to add a defaut tare weight to the shippings 
- Status.colissimo.fr integrated into the set up module header
- Display tracking number on the order page 

### Fixed
- Tracking number wich contains 8 characters instead of 9 have been authorised 

### Changed
- Product return through mailbox have been modified and not authorised if the labels are not generated into the customer account 

## [1.4.1] - 2020-09-30
### Added
- Additional countries in Intra-Dom list

### Fixed
- Fix FTD behaviour when changing service
- Fix customer name when pickup-point delivery
- Fix thermal printing in Ethernet mode
- Fix country list in module configuration

## [1.4.0] - 2020-07-31
### Added
- Compatibility with PrestaShop 1.7.7 for Admin Orders
- Allow direct thermal printing (ZPL or DPL)

### Changed
- Remove PrestUI

### Fixed
- Fix conditions for documents purge
- Fix conditions of display for older parcels in Deposit Slip page
- Fix bug when the cart includes customized products & the delivery type is "Pickup Point"

## [1.3.1] - 2020-05-28
### Fixed
- Fix table field

## [1.3.0] - 2020-05-20
### Added
- Pre-fill mobile number in widget area
- Allow pickup delivery in Hungary
- Add TagUsers in Coliship exports
- Add JS file to allow placeholder modification for mobile
- Allow merchants to disable PNA mail after label generation
- Allow customers to generate return label
- Implement ACE
- Allow merchants to hide shipments in Postage page (step 1)

### Changed
- Changed postage post-process logic
- Add confirmation message after tracking updates in Dashboard
- Add Logs controller to secure logfile download
- Lighten log files
- Brighten placeholder for mobile phone in widget area
- Add "Parcels of the day" selection in Deposit slips page

### Fixed
- Fix SQL query in Dashboard
- Fix Dashboard tracking updates
- Fix product name filled in CN23
- Fix credentials in single store mode
- Fix service changes & insurance in Postage page (step 2)
- Fix address & commercial name in labels
- Rename Overseas services
- Fix jQuery 3 compatibility

## [1.2.1] - 2019-11-05
### Added
- Add CN23 exceptions with some EU countries

### Fixed
- Fix default country for sender address in multistore All Shops context
- Fix table names
- Use pickup points address instead of invoice address in Coliship export
- Fix JS bug in widget
- Fix paginations and filters in backend lists (1.6)
- Fix PHP compatibility

## [1.2.0] - 2019-10-09
### Added
- Add Brexit mode to anticipate the withdrawal of the UK from EU
- Add CSV shipping numbers import in "postage in BO" mode
- Allow merchants to send return labels by mail to customers
- Allow merchants to ship from overseas departments
- Allow merchants to configure a return address different from the sender address
- Allow configuration of HS code + origin country + short description in a product and category level
- Add product codes for shipments inside overseas departments
- Allow merchants to print PDF documents without downloading them
- Allow merchants to download and print PDF documents in a single file

### Changed
- Update Front-Tracking page with a new Timeline
- Add ID column in dashboard, postage and deposit slip forms
- Add EORI number in CN23
- Move pickup point selection button below delivery option in PS 1.6

### Fixed
- Fix CSV MIME types
- Fix PHP compatibility
- Fix files management configuration save process
- Fix total weight and product weights for CN23

## [1.1.2] - 2019-09-04
### Fixed
- Fix mobile input on OPC mode

## [1.1.1] - 2019-08-28
### Fixed
- Fix Colissimo label table creation for fresh install
- Fix widget and mobile input init process on 1.6

## [1.1.0] - 2019-08-07
### Added
- Allow merchants to tie an order placed with a carrier different than Colissimo to the module
- Allow merchants to change the Colissimo service (home deliveries, pickup point) when creating a label
- Allow merchants to delete labels
- Allow merchants to select/unselect all shipments in one click when generating deposit slips
- Allow merchants to change the state of an order after creating labels
- Allow merchants to change the state of an order after creating deposit slips
- Add two CSS and JS empty files to be overridden
- Display insurance information for each shipments

### Changed
- New mobile input for Front Widget with prefix and country
- Update order shipping number after deleting labels
- Restrict the pickup point country to the customer's country
- Check if SOAP extension is loaded and display a warning in Deposit Slip page if it's not

### Fixed
- Improve compatibility of modal
- Fix display rules of carriers for Monaco & Andorra
- Replaced array_column with array_map for PHP < 5.5
- Remove Material icons for PS 1.7 themes compatibility reasons
- Fix escape of Widget addresses in PS 1.7
- Add missing id_customer in pickup point address
- Remove Content-Length header for compatibility reasons
- Fix translations in Admin Controllers
- Fix help texts in Colissimo Dashboard
- Fix return labels generation with "Franc de Taxe et de Douane" option

## [1.0.6] - 2019-06-06
### Changed
- Remove Content-Length header for ZPL/DPL label download
- Fix display of modal in BO
- Add check if zip PHP extension is enabled
- Prevent customers to validate an order without choosing a pickup point and providing a valid mobile number in OPC mode

### Fixed
- Fix compatibility of modal
- Fix conflict with autocomplete JS plugin
- Add phone number in pickup point address
- Add missing countDown JS function in few PS 1.6 versions
- Fix display of widget for tablets
- Anonymize password field of Colissimo account in BO

## [1.0.5] - 2019-04-09
### Added
- Add new field for EORI number in BO configuration
- Allow merchant to use local files or remote files for Widget
- Add PCS service (EU pickup-points)
- Add "What's new?" modal with latest changes

### Changed
- Replace "other" field with Colissimo Pickup Point ID in address
- Rename carriers name (fresh install only)
- Improve logs in hook newOrder

### Fixed
- Fix product code sent when Pickup Point shipment (handle aliases)

## [1.0.4] - 2019-02-04
### Added
- Improve error management and add more logs in Coliship
- Add CPassID value in Coliship export
- Add logs in case of failed return label download (Front-Office)
- Override of module's front templates is now possible
- Handle gr weight unit abbreviation
- Allow return shipment from Outre-Mer
- Allow "without signature" service for Outre-Mer destination
- Add more country available for pudo service

### Changed
- Update Colissimo webservices version to 2.0
- Handle 9-digits reference in FO tracking URL
- Add mobile phone format example when validating pickup-point
- Convert weight unit to grams in Coliship export
- Convert EOL character to CRLF in Coliship export
- Handle INet encoding in Coliship export
- Replace hard-coded country ISO
- Check HS Code format in module configuration (Back-Office tab)
- Hide cancelled orders in the Colissimo Deposit Slip interface
- Hide and prevent tracking update of orders older than 90 days in Colissimo Dashboard

### Fixed
- Fix mobile validation in PrestaShop 1.6.1.x + One-Page-Checkout mode
- Fix escape for address and city fields inside Front Widget
- Fix valid label formats
- Fix hook registration for carrier process
- Fix phone numbers fields in Coliship export
- Fix error in order history in PrestaShop 1.6.1.x
- Fix UX in module configuration (Back-Office tab)
- Fix Euro conversion
- Fix PHP error on version 5.3 & 5.4
- Fix SQL query cast
- Fix JS variable name
- Fix widget endpoint URL
- Fix Colissimo EU Zone 1 & 3 country list
- Fix menu creation for PrestaShop 1.7.0.x & 1.7.1.x

## [1.0.3] - 2018-12-21
### Changed
- Change documents storage & download process

### Fixed
- Fix file extension of CN23 when downloading

## [1.0.2] - 2018-12-13
### Fixed
- Fix BO field validation function

## [1.0.1] - 2018-12-12
### Added
- Pickup point ID is now displayed on order page after pickup point address inside Colissimo section

### Changed
- Remove check WS credentials

### Fixed
- Fix Front Widget compatibility with OPC
- Fix choice of carrier when customer is not logged in
- Fix URL after validating pickup point in the Front Widget
- Fix minor security issues
- Fix missing translation
- Add fields format checks in module configuration
- Fix missing JS variable

## [1.0.0] - 2018-10-12
### Added
- First stable version
