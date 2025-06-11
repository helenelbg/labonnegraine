/**
 * JavaScript pour l'administration du module CategoryHeaderMessages
 * Version améliorée pour résoudre le problème de chargement des déclinaisons
 */
$(document).ready(function() {
    // Variables globales
    var $productSearchInput = $('.product-search-input');
    var $productSearchInput2 = $('input.product-search-input');
    var $idProductInput = $('input[name="id_product"]');
    var $combinationsSelect = $('select[name="id_product_attribute"]');
    var $searchResults = null;
    
    // Déboguer les éléments du DOM
    console.log('Debug DOM Elements:', {
        'productSearchInput': $productSearchInput.length ? 'Found' : 'Not found',
        'idProductInput': $idProductInput.length ? 'Found' : 'Not found', 
        'idProductValue': $idProductInput.val(),
        'combinationsSelect': $combinationsSelect.length ? 'Found' : 'Not found'
    });
    
    // Initialiser l'interface utilisateur
    function initUI() {
        // Créer le conteneur de résultats de recherche
        if ($productSearchInput.length > 0) {
            $('.product-search-results').remove();
            $('.product-search-results-wrapper').remove();
            
            $productSearchInput.closest('.form-group').append(
                '<div class="product-search-results-wrapper mt-2">' +
                '<div class="product-search-results" style="display:none;"></div>' +
                '</div>'
            );
            
            $searchResults = $('.product-search-results');
        }
        
        // Ajouter le bouton de réinitialisation
        if ($productSearchInput.length > 0 && $('.clear-product-button').length === 0) {
            $productSearchInput2.after(
                '<button type="button" class="btn btn-default clear-product-button ml-2">' +
                '<i class="icon-trash"></i> Effacer' +
                '</button>'
            );
        }
        
        // Ajouter un indicateur de débogage visible
        $('body').append(
            '<div id="debug-indicator" style="position: fixed; bottom: 10px; right: 10px; ' +
            'background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 4px; ' +
            'z-index: 9999; font-size: 12px;">Debug: initialisation</div>'
        );
    }
    
    // Mettre à jour l'indicateur de débogage
    function updateDebug(message) {
        $('#debug-indicator').text('Debug: ' + message);
        console.log('Debug:', message);
    }
    
    // Fonction qui charge les déclinaisons pour un produit
    function loadCombinations(productId) {
        updateDebug('Chargement des déclinaisons pour #' + productId);
        
        // Vérifier si productId est valide
        if (!productId || productId <= 0) {
            $combinationsSelect.empty().prop('disabled', true)
                .append('<option value="0">Aucune déclinaison disponible</option>');
            return;
        }
        
        // Afficher l'indicateur de chargement
        $combinationsSelect.empty().prop('disabled', true)
            .append('<option value="">Chargement des déclinaisons...</option>');
        
        // Construire l'URL AJAX
        var ajaxUrl = '';
        if (typeof categoryHeaderMessages !== 'undefined' && categoryHeaderMessages.combinationsUrl) {
            ajaxUrl = categoryHeaderMessages.combinationsUrl;
        } else if (typeof currentIndex !== 'undefined' && typeof token !== 'undefined') {
            ajaxUrl = currentIndex + '&ajax=1&action=GetCombinations&token=' + token;
        } else {
            // Fallback à l'URL de la page actuelle
            ajaxUrl = window.location.href.split('?')[0] + '?controller=AdminCategoryHeaderMessages&ajax=1&action=GetCombinations';
        }
        
        // Ajouter un timestamp pour éviter la mise en cache
        ajaxUrl += '&_=' + new Date().getTime();
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST', // Utiliser POST au lieu de GET
            dataType: 'json',
            data: {
                id_product: productId,
                ajax: 1,
                action: 'GetCombinations'
            },
            success: function(response) {
                updateDebug('Déclinaisons reçues: ' + (response ? response.length : 0));
                
                $combinationsSelect.empty();
                
                if (response && response.length > 0) {
                    $combinationsSelect.prop('disabled', false);
                    
                    // Ajouter l'option par défaut
                    $combinationsSelect.append(
                        $('<option>').val(0).text('Sélectionnez une déclinaison')
                    );
                    
                    // Ajouter toutes les déclinaisons
                    $.each(response, function(index, combination) {
                        $combinationsSelect.append(
                            $('<option>').val(combination.id).text(combination.name)
                        );
                    });
                } else {
                    $combinationsSelect.prop('disabled', true)
                        .append('<option value="0">Produit sans déclinaisons</option>');
                }
            },
            error: function(xhr, status, error) {
                updateDebug('Erreur: ' + status + ' - ' + error);
                console.error('Ajax error:', xhr.responseText);
                
                $combinationsSelect.empty().prop('disabled', true)
                    .append('<option value="0">Erreur: impossible de charger les déclinaisons</option>');
                
                // Tentative de récupération par un appel direct à la méthode alternative
                forceLoadCombinations(productId);
            }
        });
    }
    
    // Méthode de secours pour charger les déclinaisons
    function forceLoadCombinations(productId) {
        updateDebug('Tentative de récupération forcée pour #' + productId);
        
        // Envoyer une requête à une URL modifiée pour maximiser les chances
        var fallbackUrl = window.location.pathname + '?controller=AdminCategoryHeaderMessages&ajax=1&action=GetCombinations&id_product=' + productId + '&t=' + new Date().getTime();
        
        $.ajax({
            url: fallbackUrl,
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function(response) {
                updateDebug('Récupération forcée réussie: ' + (response ? response.length : 0));
                
                if (response && response.length > 0) {
                    $combinationsSelect.empty().prop('disabled', false);
                    
                    // Ajouter l'option par défaut
                    $combinationsSelect.append(
                        $('<option>').val(0).text('Sélectionnez une déclinaison')
                    );
                    
                    // Ajouter toutes les déclinaisons
                    $.each(response, function(index, combination) {
                        $combinationsSelect.append(
                            $('<option>').val(combination.id).text(combination.name)
                        );
                    });
                }
            },
            error: function() {
                updateDebug('Échec de la récupération forcée');
            }
        });
    }
    
    // Fonction pour rechercher des produits
    function searchProducts(query) {
        updateDebug('Recherche de produits: ' + query);
        
        if (!query || query.length < 3) {
            if ($searchResults) {
                $searchResults.html('<div class="alert alert-info">Veuillez saisir au moins 3 caractères</div>').show();
            }
            return;
        }
        
        // Construire l'URL AJAX
        var ajaxUrl = '';
        if (typeof categoryHeaderMessages !== 'undefined' && categoryHeaderMessages.searchUrl) {
            ajaxUrl = categoryHeaderMessages.searchUrl;
        } else if (typeof currentIndex !== 'undefined' && typeof token !== 'undefined') {
            ajaxUrl = currentIndex + '&ajax=1&action=SearchProducts&token=' + token;
        } else {
            // Fallback à l'URL de la page actuelle
            ajaxUrl = window.location.href.split('?')[0] + '?controller=AdminCategoryHeaderMessages&ajax=1&action=SearchProducts';
        }
        
        // Ajouter un timestamp pour éviter la mise en cache
        ajaxUrl += '&_=' + new Date().getTime();
        
        if ($searchResults) {
            $searchResults.html('<div class="text-center py-2"><i class="icon-refresh icon-spin"></i> Recherche en cours...</div>').show();
        }
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                q: query,
                ajax: 1,
                action: 'SearchProducts'
            },
            success: function(data) {
                updateDebug('Résultats trouvés: ' + (data ? data.length : 0));
                
                if (!$searchResults) return;
                
                if (data && data.length > 0) {
                    var html = '<div class="list-group">';
                    
                    $.each(data, function(i, product) {
                        html += '<a href="#" class="list-group-item product-item" data-id="' + product.id + '">';
                        
                        if (product.image) {
                            html += '<img src="https://' + product.image + '" alt="' + product.name + '" style="width:40px;height:40px;margin-right:10px;">';
                        } else {
                            html += '<div style="width:40px;height:40px;margin-right:10px;display:inline-block;background:#f5f5f5;text-align:center;line-height:40px;"><i class="icon-picture"></i></div>';
                        }
                        
                        html += '<span class="product-name">' + product.name + '</span>';
                        
                        if (product.reference) {
                            html += ' <small class="text-muted">(Ref: ' + product.reference + ')</small>';
                        }
                        
                        html += '</a>';
                    });
                    
                    html += '</div>';
                    $searchResults.html(html).show();
                } else {
                    $searchResults.html('<div class="alert alert-warning">Aucun produit trouvé</div>').show();
                }
            },
            error: function(xhr, status, error) {
                updateDebug('Erreur de recherche: ' + status);
                console.error('Ajax error:', xhr.responseText);
                
                if ($searchResults) {
                    $searchResults.html('<div class="alert alert-danger">Erreur lors de la recherche</div>').show();
                }
            }
        });
    }
    
    // Initialisation de l'interface
    initUI();
    
    // Gestion des événements
    
    // Clic sur le bouton de recherche
    $(document).on('click', '.search-product-button', function(e) {
        e.preventDefault();
        searchProducts($productSearchInput.val().trim());
    });
    
    // Touche Entrée dans le champ de recherche
    $productSearchInput.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchProducts($(this).val().trim());
        }
    });
    
    // Sélection d'un produit dans les résultats
    $(document).on('click', '.product-item', function(e) {
        e.preventDefault();
        
        var productId = $(this).data('id');
        var productName = $(this).find('.product-name').text();
        var productRef = $(this).find('small').text() || '';
        
        updateDebug('Produit sélectionné: #' + productId);
        
        // Mettre à jour les champs
        $productSearchInput.val(productName + ' ' + productRef);
        
        // Important: D'abord vider le champ de déclinaisons pour éviter des problèmes
        $combinationsSelect.empty().prop('disabled', true);
        
        // Ensuite définir la valeur du produit
        $idProductInput.val(productId);
        
        // Masquer les résultats
        $searchResults.hide();
        
        // Charger les déclinaisons avec un petit délai pour s'assurer que la valeur est bien mise à jour
        setTimeout(function() {
            loadCombinations(productId);
        }, 100);
    });
    
    // Effacer le produit sélectionné
    $(document).on('click', '.clear-product-button', function(e) {
        e.preventDefault();
        
        updateDebug('Réinitialisation des champs');
        
        $productSearchInput.val('');
        $idProductInput.val('');
        $combinationsSelect.empty().prop('disabled', true)
            .append('<option value="0">Aucune déclinaison disponible</option>');
        
        if ($searchResults) {
            $searchResults.hide();
        }
    });
    
    // Masquer les résultats quand on clique ailleurs
    $(document).on('click', function(e) {
        if ($searchResults && !$(e.target).closest('.product-search-input, .search-product-button, .product-search-results').length) {
            $searchResults.hide();
        }
    });
    
    // Si un produit est déjà sélectionné, charger ses déclinaisons
    if ($idProductInput.length > 0 && $idProductInput.val() > 0) {
        updateDebug('Produit initial: #' + $idProductInput.val());
        
        // Charger les déclinaisons avec un petit délai pour s'assurer que tout est bien initialisé
        setTimeout(function() {
            loadCombinations($idProductInput.val());
        }, 300);
    }
    
    // Ajouter un bouton manuel pour forcer le rechargement des déclinaisons
    if ($combinationsSelect.length > 0) {
        $combinationsSelect.after(
            '<button type="button" class="btn btn-outline-secondary btn-sm mt-2 reload-combinations">' +
            '<i class="icon-refresh"></i> Recharger les déclinaisons' +
            '</button>'
        );
    }
    
    // Événement pour forcer le rechargement des déclinaisons
    $(document).on('click', '.reload-combinations', function(e) {
        e.preventDefault();
        
        var productId = $idProductInput.val();
        if (productId > 0) {
            updateDebug('Rechargement forcé pour #' + productId);
            loadCombinations(productId);
        } else {
            updateDebug('Aucun produit sélectionné');
            alert('Veuillez d\'abord sélectionner un produit');
        }
    });
});