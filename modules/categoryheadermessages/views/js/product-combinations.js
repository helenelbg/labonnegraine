// Remplacer le code de recherche par celui-ci :
$(document).ready(function() {
    // Cacher le champ de déclinaison par défaut s'il n'y a pas de produit sélectionné
    if (!$('input[name="id_product"]').val() || $('input[name="id_product"]').val() == 0) {
        $('.form-group.id_product_attribute_block').hide();
    }
    
    // Détecter le changement de produit
    $(document).on('change', 'input[name="id_product"]', function() {
        var productId = $(this).val();
        
        if (!productId || productId == 0) {
            // Si aucun produit n'est sélectionné, masquer le champ de déclinaison
            $('.form-group.id_product_attribute_block').hide();
            $('#id_product_attribute').empty();
            return;
        }
        
        // Charger les déclinaisons du produit via AJAX
        $.ajax({
            url: currentIndex + '&ajax=1&action=getCombinations&id_product=' + productId + '&token=' + token,
            dataType: 'json',
            success: function(data) {
                var $combinationSelect = $('#id_product_attribute');
                $combinationSelect.empty();
                
                if (data && data.length > 0) {
                    // Si le produit a des déclinaisons, afficher le champ
                    $.each(data, function(index, combination) {
                        $combinationSelect.append(
                            $('<option></option>')
                                .attr('value', combination.id)
                                .text(combination.name)
                        );
                    });
                    $('.form-group.id_product_attribute_block').show();
                } else {
                    // Sinon, masquer le champ
                    $('.form-group.id_product_attribute_block').hide();
                }
            },
            error: function() {
                // En cas d'erreur, masquer le champ
                $('.form-group.id_product_attribute_block').hide();
            }
        });
    });
    
    // Recherche de produit via le bouton
    $(document).on('click', '.search-product-button', function(e) {
        e.preventDefault();
        searchProducts();
    });
    
    // Recherche de produit en appuyant sur Entrée dans le champ
    $(document).on('keypress', 'input.product-search-input', function(e) {
        if (e.which == 13) { // Touche Entrée
            e.preventDefault();
            searchProducts();
        }
    });
    
    // Fonction de recherche de produits
    function searchProducts() {
        var query = $('input.product-search-input').val();
        
        if (query.length < 3) {
            showErrorMessage(translations.errorMinLength);
            return;
        }
        
        // Afficher un indicateur de chargement
        $('.product-search-results').html('<div class="text-center"><i class="icon-refresh icon-spin"></i> ' + translations.searching + '...</div>').show();
        
        $.ajax({
            url: currentIndex + '&ajax=1&action=searchProducts&token=' + token,
            data: { q: query },
            dataType: 'json',
            success: function(data) {
                var html = '';
                
                if (data && data.length > 0) {
                    html += '<div class="list-group">';
                    $.each(data, function(index, product) {
                        var productHtml = '<a href="javascript:void(0)" class="list-group-item product-search-item" data-id="' + product.id + '">';
                        
                        if (product.image) {
                            productHtml += '<img src="https://' + product.image + '" class="product-search-image" alt="' + product.name + '" />';
                        }
                        
                        productHtml += '<span class="product-search-name">' + product.name + '</span>';
                        
                        if (product.reference) {
                            productHtml += ' <span class="product-search-reference">Ref: ' + product.reference + '</span>';
                        }
                        
                        productHtml += '</a>';
                        html += productHtml;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="alert alert-warning">' + translations.noResults + '</div>';
                }
                
                $('.product-search-results').html(html).show();
            },
            error: function() {
                $('.product-search-results').html('<div class="alert alert-danger">' + translations.errorOccurred + '</div>').show();
            }
        });
    }
    
    // Sélection d'un produit dans les résultats
    $(document).on('click', '.product-search-item', function() {
        var productId = $(this).data('id');
        var productName = $(this).find('.product-search-name').text();
        var productRef = $(this).find('.product-search-reference').text();
        
        $('input[name="id_product"]').val(productId).trigger('change');
        $('input.product-search-input').val(productName + ' ' + productRef);
        $('.product-search-results').hide();
    });
    
    // Ajout des traductions pour les messages
    if (typeof translations === 'undefined') {
        window.translations = {
            errorMinLength: 'Veuillez saisir au moins 3 caractères',
            searching: 'Recherche en cours',
            noResults: 'Aucun produit trouvé',
            errorOccurred: 'Une erreur est survenue lors de la recherche'
        };
    }
    
    // Masquer les résultats lors d'un clic en dehors
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.product-search-input, .search-product-button, .product-search-results').length) {
            $('.product-search-results').hide();
        }
    });
    
    // Détection du type de message sélectionné pour adaptation de l'interface
    $(document).on('change', 'select[name="type"]', function() {
        var messageType = $(this).val();
        
        // Pour tous les types qui nécessitent un produit
        var productRequiredTypes = ['produit_phare', 'promo_moment', 'reduction_lot', 'offre_eco'];
        var showProduct = productRequiredTypes.includes(messageType);
        
        if (showProduct) {
            $('.form-group.product_search_block').show();
            $('.form-group.product_search_results_block').show();
            // Vérifier si un produit est déjà sélectionné pour afficher le champ de déclinaison
            if ($('input[name="id_product"]').val() && $('input[name="id_product"]').val() != 0) {
                $('.form-group.id_product_attribute_block').show();
            }
        } else {
            $('.form-group.product_search_block').hide();
            $('.form-group.product_search_results_block').hide();
            $('.form-group.id_product_attribute_block').hide();
        }
    });
    
    // Déclenchement initial pour configurer l'interface
    $('select[name="type"]').trigger('change');
});