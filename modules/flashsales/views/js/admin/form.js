/*
* 2022 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2022 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*/

$(document).ready(function() {
    Card = {
        field: $('#product'),
        fieldAction: $('#search_action'),
        fieldResult: $('#search_result'),
        filedSelectedProducts: $('#selected_products'),
        ajaxUrl: '',
        ajaxTab: "AdminFlashSales",
        defaultReduction: $('#reduction').val(),
        defaultReductionType: $('#reduction_type').val(),
        defaultFrom: $('#from').val(),
        defaultTo: $('#to').val(),
        merge: true,

        init: function() {
            $(Card.field).typeWatch({
                captureLength: 1,
                highlight: true,
                wait: 500,
                callback: Card.search
            });

            Card.ajaxUrl = flashsalesControllerLink;
        },

        search: function() {
            Card.merge = true;

            $.ajax({
                type:"POST",
                url: Card.ajaxUrl,
                dataType: "json",
                data: {
                    ajax: "1",
                    tab: Card.ajaxTab,
                    action: "searchItems",
                    item: Card.fieldAction.val(),
                    ids_item: Card.getSelectedIdsItem(Card.fieldAction.val()),
                    product_search: Card.field.val(),
                },
                success: Card.success
            });
        },

        success: function(content) {
            Card.fieldResult.show();

            if (content != "") {
                Card.fieldResult.html(content); 
            }
        },

        updateSearchAction: function() {
            $(this).closest('.input-group-btn')
                .find('[data-bind="label"]').text($(this).text())
                .end()
                .children('.dropdown-toggle').dropdown('toggle');

            Card.fieldAction.val($(this).attr('data-item'));
            Card.field.val('');
        },

        addCustomPrice: function () {
            var selector = $(this).closest('.panel')
            var item = selector.attr('data-panel');
            var id_item = selector.find('.panel-footer').last().attr('data-product');

            $('#custom_price_form').find('input[name="custom_reduction"]').val(selector.find('.custom_price_content').attr('data-reduction'));
            $('#custom_price_form').find('select[name="custom_reduction_type"]').val(selector.find('.custom_price_content').attr('data-reduction-type'));
            $('#custom_price_form').find('input[name="custom_from"]').val(selector.find('.custom_price_content').attr('data-from'));
            $('#custom_price_form').find('input[name="custom_to"]').val(selector.find('.custom_price_content').attr('data-to'));

            $.fancybox([
                {
                    href : '#custom_price_form',
                    scrolling: 'no',
                    hideOnContentClick: false,
                }
            ]);

            $(".fancybox-inner, #fancybox-content").on('click', '#validate_custom_price', function() {
                var errors = false;
                var reduction = parseFloat($(".fancybox-inner, #fancybox-content").find('input[name="custom_reduction"]').val());
                var reductionType = $(".fancybox-inner, #fancybox-content").find('select[name="custom_reduction_type"]').val();
                var from = $(".fancybox-inner, #fancybox-content").find('input[name="custom_from"]').val();
                var to = $(".fancybox-inner, #fancybox-content").find('input[name="custom_to"]').val();

                if (new Date(from).getTime() > new Date(to).getTime()) {
                    var errors = true;
                    $(".fancybox-inner, #fancybox-content").find('#custom_period_errors').show();
                }

                if (reduction < 0) {
                    var errors = true;
                    $(".fancybox-inner, #fancybox-content").find('#custom_price_errors').show();
                }

                if (!errors) {
                    if (item != "product") {
                        $.each(selectedProducts, function(key, value) {
                            if (value['item'] == item && value['id_item'] == id_item) {
                                selectedProducts[key]['custom_reduction'] = true;
                                selectedProducts[key]['reduction'] = reduction;
                                selectedProducts[key]['reduction_type'] = reductionType;
                                selectedProducts[key]['from'] = from;
                                selectedProducts[key]['to'] = to;
                            }
                        })
                    } else {
                        var idCombination = selector.find('.panel-body select[name="id_combination"]').val();
                        $.each(combinations, function(key, combination) {
                            if (combination['id_product'] == id_item && combination['id_product_attribute'] == idCombination) {
                                combinations[key]['custom_reduction'] = true;
                                combinations[key]['reduction'] = reduction;
                                combinations[key]['reduction_type'] = reductionType;
                                combinations[key]['from'] = from;
                                combinations[key]['to'] = to;
                            }
                        })
                    }

                    $(".fancybox-inner, #fancybox-content").find('.custom_errors').hide();
                    Card.initReductionDisplay();
                    $.fancybox.close();
                }
            });
        },

        deleteReduction: function () {
            var selector = $(this).closest('.panel')
            var item = selector.attr('data-panel');
            var id_item = selector.find('.panel-footer').last().attr('data-product');

            if (item != "product") {
                $.each(selectedProducts, function(key, value) {
                    if (value['item'] == item && value['id_item'] == id_item) {
                        selectedProducts[key]['custom_reduction'] = false;
                    }
                })
            } else {
                var idCombination = selector.find('.panel-body select[name="id_combination"]').val();
                $.each(combinations, function(key, combination) {
                    if (combination['id_product'] == id_item && combination['id_product_attribute'] == idCombination) {
                        combinations[key]['custom_reduction'] = false;
                        delete combinations[key]['reduction'];
                        delete combinations[key]['reduction_type'];
                        delete combinations[key]['from'];
                        delete combinations[key]['to'];
                    }
                })
            }

            Card.initReductionDisplay();
        },

        setDefaultReduction: function() {
            Card.defaultReduction = $('input[name="reduction"]').val();
            Card.defaultReductionType = $('select[name="reduction_type"]').val();
            Card.defaultFrom = $('input[name="from"]').val();
            Card.defaultTo = $('input[name="to"]').val();
        },

        initReductionDisplay: function () {
            $.each(selectedProducts, function(k, item) {
                var selector = $('.item_'+item['item']+'_'+item['id_item']);
                var reduction = Card.defaultReduction;
                var reductionType = Card.defaultReductionType;
                var from = Card.defaultFrom;
                var to = Card.defaultTo;

                selector.find('.add-custom-price').show();
                selector.find('.delete-custom-price').hide();

                if (item['custom_reduction']) {
                    reduction = item['reduction'];
                    reductionType = item['reduction_type'];
                    from = item['from'];
                    to = item['to'];
                    selector.find('.add-custom-price').hide();
                    selector.find('.delete-custom-price').show();
                };

                Card.setFormattedReduction(selector.find('.custom_price_content'), reduction, reductionType, from, to);

                $.each(item['ids_product'], function(l, idProduct) {
                    var selectorChild = $('.item_product_'+idProduct);
                    var idCombination = selectorChild.find('.panel-body select[name="id_combination"]').val();
                    var combination = Card.getCombination(idProduct, idCombination);
                    var defaultCombination = Card.getCombination(idProduct, 0);
                    var reductionChild = reduction;
                    var reductionTypeChild = reductionType;
                    var fromChild = from;
                    var toChild = to;

                    selectorChild.find('.add-custom-price').show();
                    selectorChild.find('.delete-custom-price').hide();

                    if (combination['custom_reduction']) {
                        reductionChild = combination['reduction'];
                        reductionTypeChild = combination['reduction_type'];
                        fromChild = combination['from'];
                        toChild = combination['to'];
                        selectorChild.find('.add-custom-price').hide();
                        selectorChild.find('.delete-custom-price').show();
                    } else if (defaultCombination['custom_reduction']) {
                        reductionChild = defaultCombination['reduction'];
                        reductionTypeChild = defaultCombination['reduction_type'];
                        fromChild = defaultCombination['from'];
                        toChild = defaultCombination['to'];
                    }

                    Card.toggleReductionDisplay(idProduct, idCombination);
                    Card.setFormattedReduction(selectorChild.find('.custom_price_content'), reductionChild, reductionTypeChild, fromChild, toChild);
                })
            });

            Card.displaySummaryProduct();
        },

        editCard: function() {
            var item = $(this).closest('.panel').attr('data-panel');
            var id = $(this).parent().attr('data-product');

            $(this).closest('.productCard').hasClass('selected-product') ? Card.deleteCard($(this), item, id) : Card.addCard($(this), item, id);
        },

        addCard: function(element, item, id) {
            Card.merge = false;

            element.removeClass('setup-product').addClass('remove-product').html('<i class="icon-refresh"></i>&nbsp;'+remove).blur();
            element.closest('.productCard').addClass('selected-product').find('.custom_price_content').removeClass('hide');
            $('.productCard').not('.selected-product').remove();

            Card.getSelectedItemField(item).append(Card.fieldResult.html()).closest('.panel').show();
            Card.addSelectedItem(item, id);

            $('#search_result').empty();
        },

        deleteCard: function(element, item, id) {
            if (element.closest('.row.cards').hasClass('products_detail')) { // delete product link to a category
                item = element.closest('.row.cards').attr('data-parent-panel');
                parentId = Card.getDisplayProductsField(item).attr('data-id-item');
                element.closest('.productCard').remove();

                //update number of products for item
                var nb_products = parseInt($('#'+item+'_products_'+parentId).closest('.productCard').find('.nb_products').html());
                $('#'+item+'_products_'+parentId).closest('.productCard').find('.nb_products').html(nb_products-1);

                if (Card.getDisplayProductsField(item).find('.productCard').length > 0) {
                    $('#'+item+'_products_'+parentId).html(Card.getDisplayProductsField(item).html());
                    Card.deleteSelectedItem(item, parentId, id);
                } else {
                    Card.deleteCard($('#'+item+'_products_'+parentId), item, parentId);
                }
            } else { // delete card
                element.closest('.productCard').remove();
                if (!Card.getSelectedItemField(item).find('.productCard').length) {
                    Card.getSelectedItemField(item).empty().closest('.panel').hide();
                }
                Card.deleteSelectedItem(item, id, -1);
            }
        },

        addSelectedItem: function (item, id) {
            var selectedItem = new Array();
            selectedItem['item'] = item;
            selectedItem['id_item'] = id;
            selectedItem['custom_reduction'] = false;
            selectedItem['ids_product'] = new Array();

            if (item == "product") {
                var selectorChild = $('.item_product_'+id);
                var idCombination = selectorChild.find('.panel-body select[name="id_combination"]').val();

                if (!Card.isProductSelected(id)) {
                    var combination = Card.getCombination(id, idCombination);
                    combination['has_reduction'] = true;
                    Card.updateHasReduction(combination);  
                } 

                selectedItem['ids_product'].push(id);
                Card.updateCombinationDisplay(selectorChild, id, idCombination, item, id);
            } else {
                $('#'+item+'_products_'+id).find('.productCard button.card-action').each(function() {
                    subId = $(this).parent().attr('data-product');

                    if (!Card.isProductSelected(subId)) {
                        $.each(combinations, function(key, combination) {
                            if (combination['id_product'] == subId) {
                                combinations[key]['has_reduction'] = true;
                                Card.updateHasReduction(combinations[key]);
                            }
                            
                        }); 
                    } 

                    selectedItem['ids_product'].push(subId);
                    Card.updateCombinationDisplay($('.item_product_'+subId), subId, Card.getSelectedCombination(subId), item, id);
                });
            }

            selectedProducts.push(selectedItem);
            combinations = combinations.filter(function(combination) {
                return Card.isProductSelected(parseInt(combination['id_product']));
            });

            Card.initReductionDisplay();
        },

        deleteSelectedItem: function(item, id, subId) {
            selectedProducts = selectedProducts.filter(function(selectedProduct) {
                if (selectedProduct['item'] == item && selectedProduct['id_item'] == id) {
                    if (subId == -1) {
                        return false;
                    } else {
                        selectedProduct['ids_product'] = selectedProduct['ids_product'].filter(function(idProduct) {
                            if (subId == idProduct) {
                                return false;
                            } 

                            return true;
                        });
                    }
                }

                return true;
            });
            
            combinations = combinations.filter(function(combination) {
                return Card.isProductSelected(combination['id_product']);
            });

            Card.displaySummaryProduct();
        },

        isProductSelected: function(idProduct) {
            var isSelected = false;

            for (var selectedProduct of selectedProducts) {
                for (var id of selectedProduct['ids_product']) {
                    if (idProduct == id) {
                        isSelected = true;
                        break;
                    }
                }

                if (isSelected) {
                    break;
                }
            }
            
            return isSelected;
        },

        getSelectedCombination: function(idProduct) {
            var idCombination = 0;

            for (var combination of combinations) {
                if (combination['id_product'] == idProduct && combination['selected'] == true) {
                    idCombination = combination['id_product_attribute'];
                    break;
                } 
            }

            return idCombination;
        },

        updateSelectedCombination: function(idProduct, idCombination) {
            combinations = combinations.map(function(combination) {
                if (combination['id_product'] == idProduct) {
                    combination['selected'] = false;

                    if (combination['id_product_attribute'] == idCombination) {
                        combination['selected'] = true;
                    }
                }
                
                return combination;
            });
        },

        expandProducts: function() {
            var item = $(this).closest('.panel').attr('data-panel');
            var id = $(this).parent().attr('data-product');
            var selectedItemField = Card.getSelectedItemField(item);
            var productsField = Card.getDisplayProductsField(item);

            productsField.attr('data-id-item', id).find('h4 span').html($(this).closest('.panel').find('.panel-heading > span.pull-left').html());
            productsField.children('.content').html($('#'+item+'_products_'+id).html());
            selectedItemField.find('.expand-action').removeClass('expanded');
            productsField.hide();
            $(this).children().toggle();

            if ($(this).children('.btn-collapse').is(':visible')) {
                $(this).addClass('expanded');
                selectedItemField.find('.expand-action').not('.expanded').children('.btn-collapse').hide();
                selectedItemField.find('.expand-action').not('.expanded').children('.btn-expand').show();
                productsField.show();
            }
        },

        getSelectedIdsItem: function (item) {
            var ids = new Array();

            selectedProducts.map(function(selectedProduct) {
                if (selectedProduct['item'] == item) {
                    ids.push(selectedProduct['id_item']);
                }
            });

            return ids.join(',');
        },

        getSelectedItemField: function(item) {
            return $('#selected_'+item);
        },

        getDisplayProductsField: function(item) {
            return $('#'+item+'_products_detail');
        },

       hasProductAllCombinationInReduction: function(id_product) {
            var hasProductAllCombinationInReduction = true;

            for (var combination of combinations) {
                if (combination['id_product'] == id_product 
                    && combination['id_product_attribute'] != 0 
                    && (!combination['has_reduction'] || combination['custom_reduction'])
                ) {
                    hasProductAllCombinationInReduction = false;
                    break;
                }
            };

            return hasProductAllCombinationInReduction;
       },

        getSummaryProduct: function() {
            var products = new Object();
            $.each(selectedProducts, function(key, val) {
                $.each(val['ids_product'], function(k, v) {
                    var hasProductAllCombinationInReduction = Card.hasProductAllCombinationInReduction(v);
                    var defaultCombination = Card.getCombination(v, 0);
                    products[v] = new Object();

                    $.each(combinations, function(l, combination) {
                        if (combination['id_product'] == v && combination['has_reduction']) {
                            if ((hasProductAllCombinationInReduction && combination['id_product_attribute'] != 0) 
                                || (!hasProductAllCombinationInReduction && combination['id_product_attribute'] == 0)
                            ) {
                                return;
                            }

                            if (combination['custom_reduction']) {
                                products[v][combination['id_product_attribute']] = new Object();
                                products[v][combination['id_product_attribute']]['attributes'] = combination['attributes'];
                                products[v][combination['id_product_attribute']]['reduction'] = combination['reduction'];
                                products[v][combination['id_product_attribute']]['reduction_type'] = combination['reduction_type'];
                                products[v][combination['id_product_attribute']]['from'] = combination['from'];
                                products[v][combination['id_product_attribute']]['to'] = combination['to'];
                                products[v][combination['id_product_attribute']]['custom_reduction'] = 1;
                            } else {
                                products[v][combination['id_product_attribute']] = new Object();
                                products[v][combination['id_product_attribute']]['attributes'] = combination['attributes'];
                                products[v][combination['id_product_attribute']]['reduction'] = defaultCombination['custom_reduction'] 
                                    ? defaultCombination['reduction'] 
                                    : val['custom_reduction'] 
                                        ? val['reduction'] 
                                        : Card.defaultReduction
                                ;
                                products[v][combination['id_product_attribute']]['reduction_type'] = defaultCombination['custom_reduction'] 
                                    ? defaultCombination['reduction_type'] 
                                    : val['custom_reduction'] 
                                        ? val['reduction_type'] 
                                        : Card.defaultReductionType
                                ;
                                products[v][combination['id_product_attribute']]['from'] = defaultCombination['custom_reduction'] 
                                    ? defaultCombination['from'] 
                                    : val['custom_reduction'] 
                                        ? val['from'] 
                                        :  Card.defaultFrom
                                ;
                                products[v][combination['id_product_attribute']]['to'] = defaultCombination['custom_reduction'] 
                                    ? defaultCombination['to'] 
                                    : val['custom_reduction'] 
                                        ? val['to'] 
                                        : Card.defaultTo
                                ;

                                // products[v][combination['id_product_attribute']]['custom_reduction'] = 0;
                                products[v][combination['id_product_attribute']]['custom_reduction'] = (!hasProductAllCombinationInReduction && defaultCombination['custom_reduction']) ? 1 : 0;
                            }
                        }
                    });
                });
            });

            return products;
        },

        setFormattedReduction: function(element, reduction, reductionType, from, to) {
            var status = Card.getProductStatus(from, to);
            var formattedImpact = reduction + ' ' + (reductionType === 'percentage' ? '%' : reductionType);

            element.find('.formatted_reduction .card-panel').removeClass('info warning danger').addClass(status.class);
            element.find('.formatted_reduction .card-panel .formatted_impact').html(formattedImpact);
            element.find('.formatted_reduction .card-panel .formatted_period_from').html(from);
            element.find('.formatted_reduction .card-panel .formatted_period_to').html(to);
            element
                .attr('data-reduction', reduction)
                .attr('data-reduction-type', reductionType)
                .attr('data-from', from)
                .attr('data-to', to);
        },


        displaySummaryProduct: function() {
            var products = Card.getSummaryProduct();
            var html = '';

            $('#summary_products').empty();
            $('#summary_part').find('table > tbody').empty();

            $.each(selectedProducts, function(key, val) {
                $('#summary_products').append('<input type="hiden" name="'+val["item"]+'['+val["id_item"]+'][ids_product]" value="'+val["ids_product"]+'" />');
                if (val['custom_reduction']) {
                    $('#summary_products').append('<input type="hiden" name="'+val["item"]+'['+val["id_item"]+'][reduction]" value="'+val["reduction"]+'" />');
                    $('#summary_products').append('<input type="hiden" name="'+val["item"]+'['+val["id_item"]+'][reduction_type]" value="'+val["reduction_type"]+'" />');
                    $('#summary_products').append('<input type="hiden" name="'+val["item"]+'['+val["id_item"]+'][from]" value="'+val["from"]+'" />');
                    $('#summary_products').append('<input type="hiden" name="'+val["item"]+'['+val["id_item"]+'][to]" value="'+val["to"]+'" />');
                }
            });

            $.each(products, function(id_product, ids_attribute) {
                var img = $('.item_product_'+id_product).find('.panel-body img');
                $.each(ids_attribute, function(id_attribute, properties) {
                    var reduction_type = properties['reduction_type'] === 'percentage' ? '%' : properties['reduction_type'];
                    var status = Card.getProductStatus(properties['from'], properties['to']);
                    html += '<tr class="'+ status.class +'"><td class="fixed-width-sm">'+id_product+'</td>';
                    html += '<td><img src="'+img.attr('src')+'" title="'+img.attr('title')+'" class="fixed-width-sm img-thumbnail" /></td>';
                    html += '<td>'+img.attr('title')+'</td>';
                    html += '<td>'+properties['attributes']+'</td>';
                    html += '<td>'+properties['reduction']+' '+reduction_type+'</td>';
                    html += '<td>'+properties['from']+'<br/>'+properties['to']+'</td>';
                    html += '<td>'+status.text+'</td></tr>';
                    $('#summary_products').append('<input type="hiden" name="reductions['+id_product+']['+id_attribute+'][reduction]" value="'+properties['reduction']+'" />');
                    $('#summary_products').append('<input type="hiden" name="reductions['+id_product+']['+id_attribute+'][reduction_type]" value="'+properties['reduction_type']+'" />');
                    $('#summary_products').append('<input type="hiden" name="reductions['+id_product+']['+id_attribute+'][from]" value="'+properties['from']+'" />');
                    $('#summary_products').append('<input type="hiden" name="reductions['+id_product+']['+id_attribute+'][to]" value="'+properties['to']+'" />');
                    $('#summary_products').append('<input type="hiden" name="reductions['+id_product+']['+id_attribute+'][custom_reduction]" value="'+properties['custom_reduction']+'" />');
                });
            });

            $('#summary_part').find('table > tbody').append(html);
        },

        getProductStatus: function(from, to) {
            var status;
            var now = new Date().getTime();
            var from = new Date(from).getTime();
            var to = new Date(to).getTime();

            if (from > now && to > now) {
                status = pending;
            } else if (from <= now && to > now) {
                status = active;
            } else {
                status = expired;
            }

            return status;
        },

        displaySummaryField: function(field, val) {
            if (field && val) {
                $('#summary_' + field).html(val);
            }
        },

        getCombination: function(idProduct, idCombination) {
            var combination = new Array();

            $.each(combinations, function(index, value) {
                if (value['id_product'] == idProduct && value['id_product_attribute'] == idCombination) {
                    combination = value;
                }
            });

            return combination;
        },

        mergeCombinations: function(combs) {
            if (Card.merge) {
                $.each(combs, function(idProduct, attributes) {
                    $.each(attributes, function(idAttribute, combination) {
                        if (!Object.keys(Card.getCombination(idProduct, idAttribute)).length) {
                            combinations.push(combination);
                        }
                    })
                });
            }
            
            Card.initReductionDisplay();
        },

        updateHasReduction: function(combination) {
            if (combination['id_product_attribute'] == 0) {
                $.each(combinations, function(index, value) {
                    if (combination['has_reduction']) {
                        if (value['id_product'] == combination['id_product'] && value['id_product_attribute'] != combination['id_product_attribute'] && !value['has_reduction']) {
                            combinations[index]['has_reduction'] = true;
                        }
                    } else {
                        if (value['id_product'] == combination['id_product'] && value['id_product_attribute'] != combination['id_product_attribute'] && value['has_reduction'] && !value['custom_reduction']) {
                            combinations[index]['has_reduction'] = false;
                        }
                    }
                });
            } else  {
                var defaultCombination = Card.getCombination(combination['id_product'], 0);

                if (!combination['has_reduction']) {
                    defaultCombination['has_reduction'] = false
                } else if (Card.hasProductAllCombinationInReduction(combination['id_product'])) {
                    defaultCombination['has_reduction'] = true;
                }   
            }
        },

        toggleHasReduction: function() {
            var $self = $(this);
            var idProduct = $self.closest('.panel').find('.panel-footer').attr('data-product');
            var idCombination = $self.closest('.panel').find('select[name="id_combination"]').val();
            var combination = Card.getCombination(idProduct, idCombination);

            combination['has_reduction'] = !combination['has_reduction'];

            Card.updateHasReduction(combination);
            Card.toggleReductionDisplay(idProduct, idCombination);
            Card.displaySummaryProduct(); 
        },

        toggleReductionDisplay: function(idProduct, idCombination) {
            $(document).find('.item_product_'+idProduct).each(function() {
                var selector = $(this);

                if (selector.find('select[name="id_combination"]').val() == idCombination) {
                    var combination = Card.getCombination(idProduct, idCombination);

                    if (combination['has_reduction']) {
                        selector.find('input[name="has_reduction"]').prop('checked', true);
                        selector.find('.formatted_reduction').show();
                        
                    } else {
                        selector.find('input[name="has_reduction"]').prop('checked', false);
                        selector.find('.formatted_reduction').hide();   
                    }

                    if (combination['custom_reduction']) {
                        selector.find('.add-custom-price').hide();
                        selector.find('.delete-custom-price').show();
                    } else {
                        selector.find('.delete-custom-price').hide();
                        selector.find('.add-custom-price').show();
                    }
                } 
            })         
        },

        updateCombinationDisplay: function(selector, idProduct, idCombination, parentItem, parentItemId) {
            var combination = Card.getCombination(idProduct, idCombination);
            var reduction = Card.defaultReduction;
            var reduction_type = Card.defaultReductionType;
            var from = Card.defaultFrom;
            var to = Card.defaultTo;
            
            selector.find('select[name="id_combination"] option').prop('selected', false);
            selector.find('select[name="id_combination"] option').removeAttr('selected');
            selector.find('select[name="id_combination"] option[value="'+idCombination+'"]').prop('selected', true);
            selector.find('select[name="id_combination"] option[value="'+idCombination+'"]').attr('selected', 'selected');
            selector.find('.panel-body .stock').html(combination['stock']);
            selector.find('.panel-body .price').html(combination['formatted_price']);
            selector.find('.add-custom-price').show();
            selector.find('.delete-custom-price').hide();

            if (combination['custom_reduction']) {
                selector.find('.add-custom-price').hide();
                selector.find('.delete-custom-price').show();

                Card.toggleReductionDisplay(idProduct, idCombination);
                Card.setFormattedReduction(selector.find('.custom_price_content'), combination['reduction'], combination['reduction_type'], combination['from'], combination['to']);
            } else {
                var defaultCombination = Card.getCombination(idProduct, 0);
                if (defaultCombination['custom_reduction']) {
                    reduction = defaultCombination['reduction'];
                    reduction_type = defaultCombination['reduction_type'];
                    from = defaultCombination['from'];
                    to = defaultCombination['to'];
                } else {
                    if (parentItem != "product") {
                        $.each(selectedProducts, function(key, item) {
                            if (item['item'] == parentItem && item['id_item'] == parentItemId && item['custom_reduction']) {
                                reduction = item['reduction'];
                                reduction_type = item['reduction_type'];
                                from = item['from'];
                                to = item['to'];
                            }
                        })
                    }
                }

                Card.toggleReductionDisplay(idProduct, idCombination);
                Card.setFormattedReduction(selector.find('.custom_price_content'), reduction, reduction_type, from, to);
            }
        },

        updateCombination: function() {
            var $self = $(this);
            var idProduct = $self.closest('.panel').find('.panel-footer').attr('data-product');
            var idCombination = $self.val();
            var parentItem = $self.closest('.cards').attr('data-parent-panel');
            var parentItemId = $self.closest('.cards').attr('data-id-item');

            Card.updateSelectedCombination(idProduct, idCombination);
            return Card.updateCombinationDisplay($(document).find('.item_product_'+idProduct), idProduct, idCombination, parentItem, parentItemId);
        }
    };

    Customer = {
        "hiddenField": jQuery('#id_customer'),
        "field": jQuery('#customer'),
        "container": jQuery('#customers'),
        "loader": jQuery('#customerLoader'),
        "init": function() {
            jQuery(Customer.field).typeWatch({
                "captureLength": 1,
                "highlight": true,
                "wait": 500,
                "callback": Customer.search
            }).focus(Customer.placeholderIn).blur(Customer.placeholderOut);
        },
        "placeholderIn": function() {
            if (this.value == allCustomers) {
                this.value = '';
            }
        },
        "placeholderOut": function() {
            if (this.value == '') {
                this.value = allCustomers;
                Customer.hiddenField.val(0);
            }
        },
        "search": function()
        {
            Customer.showLoader();
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: customerControllerLink,
                data: {
                    ajax: "1",
                    tab: "AdminCustomers",
                    action: "searchCustomers",
                    customer_search: Customer.field.val()
                },
                success: Customer.success
            });
        },
        "success": function(result)
        {
            if(result.found) {
                var html = '<ul class="list-unstyled">';
                jQuery.each(result.customers, function() {
                    html += '<li><a class="fancybox" href="'+customerControllerLink+'&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a>'+(this.birthday ? ' - '+this.birthday:'');
                    html += ' - '+this.email;
                    html += '<a onclick="Customer.select('+this.id_customer+', \''+this.firstname+' '+this.lastname+'\'); return false;" href="#" class="btn btn-default">'+choose+'</a></li>';
                });
                html += '</ul>';
            } else {
                html = '<div class="alert alert-warning">'+noCustomersFound+'</div>';
            }

            Customer.hideLoader();
            Customer.container.html(html);
        },
        "select": function(id_customer, fullname)
        {
            Customer.hiddenField.val(id_customer);
            Customer.field.val(fullname);
            Customer.container.empty();
            return false;
        },
        "showLoader": function() {
            Customer.loader.fadeIn();
        },
        "hideLoader": function() {
            Customer.loader.fadeOut();
        }
    };

	Customer.init();
	Card.init();
	initAsset();
	initBind();
});

function initAsset()
{
	// load datepicker
	$(document).find('.datepicker').datetimepicker({
		prevText: '',
		nextText: '',
		dateFormat: 'yy-mm-dd',
		timeFormat: 'hh:mm:ss tt',
		timeSuffix: '',
	});

    $('.selectpicker').selectpicker({
        tickIcon: 'icon icon-check',
        width: '100%',
        actionsBox: true,
        selectAllText: selectAllText,
        deselectAllText: deselectAllText
    });

	// load tinymce
	tinySetup({
		editor_selector: "autoload_rte"
	});
	hideOtherLanguage(default_language);
}

function initBind()
{
	// load summary
	$('#specific_price_rule').find('select, input[type="text"], input[type="radio"]').on('change', function() {
		var field = $(this).attr('id'), val;

		if (field == "reduction" || field == "reduction_type" || field == "from" || field == "to") {
            Card.setDefaultReduction();
            Card.initReductionDisplay();
		} else {
			if ($(this).is('select')) {
                values = [];
                $(this).find('option:selected').each(function() {
                    values.push($(this).html())
                });

                val = values.length ? values.join(', ') : $(this).attr('title');
            } else if ($(this).is('input[type="radio"]:checked')) {
				field = $(this).attr('name');
				val = $(this).val() == 1 ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>';
			} else {
                val = $(this).val()
            }

			Card.displaySummaryField(field, val);
		}
	}).trigger('change');

	// load cards button
	$(document).on('click', '.cards button.card-action', Card.editCard);
	$(document).on('click', '.cards button.expand-action', Card.expandProducts);
	$(document).on('click', '.cards button.add-custom-price', Card.addCustomPrice);
	$(document).on('click', '.cards button.delete-custom-price', Card.deleteReduction);
    
	// load dropdown menu button
    $(document).on('click', '.dropdown-menu li', Card.updateSearchAction);
    $(document).on('change', '.cards select[name="id_combination"]', Card.updateCombination);
    $(document).on('change', '.cards input[name="has_reduction"]', Card.toggleHasReduction);

    $(document).on('click', 'label[data-toggle="has_reduction"]', function() {
        $(this).closest('.material-switch').find('input').trigger('change');
    });

    $('.selectpicker').each(function () {
        $(this).on('hide.bs.select', function (e) {
            if (!$(this).find('option').not(':selected').length) {
                $(this).selectpicker('deselectAll');
            }
        });
    });

	$(document).on('click', 'a.fancybox', function(event) {
        event.preventDefault();
        $.fancybox([
            {
                href: $(this).attr('href'),
                type : 'iframe',
            }
        ]);
	});
}
