(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const customerId = params.get('customerId');
        const orderId = params.get('orderId');
        
        if (!customerId) return;
console.log('LA');
        const checkReact = setInterval(() => {
            if (window.order_create && window.order_create.searchCustomerByString) {
                clearInterval(checkReact);
                
                // On fait la recherche
                window.order_create.searchCustomerByString(customerId);

                // On attend que les résultats de recherche apparaissent
                const waitForResults = setInterval(() => {
                    const searchResults = document.querySelectorAll('.js-customer-search-result');
                    
                    for (const result of searchResults) {
                        const idSpan = result.querySelector('.js-customer-id');
                        if (idSpan && idSpan.textContent === customerId) {
                            clearInterval(waitForResults);
                            // On trouve le bouton dans ce résultat spécifique
                            const chooseButton = result.querySelector('.js-choose-customer-btn');
                            if (chooseButton) {
                                chooseButton.click();
                                
                                //1
                                const waitForResults2 = setInterval(() => {
                                    const searchResults2 = document.querySelectorAll('#customer-orders-table tr');

                                    for (const result2 of searchResults2) {
                                        const idSpan2 = result2.querySelector('.js-order-id');
                                        if (idSpan2 && $(idSpan2).text() == orderId) {
                                            clearInterval(waitForResults2);
                                            // On trouve le bouton dans ce résultat spécifique
                                            const chooseButton2 = result2.querySelector('.js-use-order-btn');
                                            if (chooseButton2) {
                                                chooseButton2.click();
                                                $('#customer-checkout-history').hide()

                                                //2
                                                const setPricesTo0 = setInterval(() => {
                                                    const unitPriceInputs = document.querySelectorAll('.js-product-unit-input');
                                                    
                                                    if (unitPriceInputs.length > 0) {
                                                        unitPriceInputs.forEach(input => {
                                                            input.value = '0';
                                                            // Dispatch un événement de changement pour déclencher les mises à jour
                                                            const event = new Event('change', { bubbles: true });
                                                            input.dispatchEvent(event);
                                                        });
                                                        clearInterval(setPricesTo0);
                                                    }
                                                }, 500);                 
                                                //2

                                                // Cocher la livraison gratuite
                                                const setFreeShipping = setInterval(() => {
                                                    const freeShippingSwitch = document.querySelector('.js-free-shipping-switch[value="1"]');
                                                    
                                                    if (freeShippingSwitch) {
                                                        // Coche le bouton radio de livraison gratuite
                                                        freeShippingSwitch.click();
                                                        console.log('OK');
                                                        clearInterval(setFreeShipping);
                                                    }
                                                }, 500);

                                                selectPaymentMethod('savpayment');
                                                selectStatus(44);

                                                break;
                                            }
                                        }
                                    }
                                }, 100);
                                //1

                                break;
                            }
                        }
                    }
                }, 100);
            }
        }, 100);
    });

    function selectPaymentMethod(paymentId) {
        const waitForPaymentMethods = setInterval(() => {
            const paymentSelect = document.querySelector('select#cart_summary_payment_module');
            if (paymentSelect) {
                clearInterval(waitForPaymentMethods);
                paymentSelect.value = paymentId;
                // Déclencher l'événement change pour que PrestaShop mette à jour l'interface
                paymentSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, 100);
    }

    function selectStatus(statusId) {
        const waitForStatus = setInterval(() => {
            const statusSelect = document.querySelector('select#cart_summary_order_state');
            if (statusSelect) {
                clearInterval(waitForStatus);
                statusSelect.value = statusId;
                // Déclencher l'événement change pour que PrestaShop mette à jour l'interface
                statusSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, 100);
    }

})();