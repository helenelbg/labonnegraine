{if $page['page_name'] == "category"}
    <script>
        dataLayer.push({$datalayer nofilter});
    </script>

    {* Gestion du Add_to_cart *}

    {literal}
        <script>
            $(".js-listing-add-to-cart").on("click", function(){
                var price = $(this).parents(".thumbnail-container").find(".product-price-and-shipping .price").text().trim();

                if(price.indexOf("à") > 0){
                    price = price.split("à ")[1];
                }

                price = price.split(" €")[0];
                price = price.replace(",", ".");
                price = parseFloat(price);

                console.log(price);

                dataLayer.push({
                    event: "add_to_cart",
                    ecommerce: {
                        currency: "EUR",
                        value: price,
                        item: [{
                            item_id: $(this).parents(".product-miniature").attr("data-id-product"),
                            item_name: $(this).parents(".product-miniature").find(".product-description .h3.product-title a").text(),
                            quantity: 1
                        }]
                    }
                });
            });
        </script>
    {/literal}

{elseif $page['page_name'] == "product"}
    <script>
        var dtl = {$datalayer nofilter};
        dtl.ecommerce.value = parseFloat($(".current-price-value").attr("content"));
        dataLayer.push(dtl);
    </script>

    {* Gestion du Add_to_cart *}

    {literal}
        <script>
            $(".add-to-cart").on("click", function(){
                dataLayer.push({
                    event: "add_to_cart",
                    ecommerce: {
                        currency: "EUR",
                        value: parseFloat($(".current-price-value").attr("content")) * parseInt($("#quantity_wanted").val()),
                        item: [{
                            item_id: $("#product_page_product_id").val(),
                            item_name: $("section h1").text(),
                            quantity: $("#quantity_wanted").val()
                        }]
                    }
                });
            });
        </script>
    {/literal}
{elseif $page['page_name'] == "cart"}
    <script>
        dataLayer.push({$datalayer nofilter});
    </script>
{elseif $page['page_name'] == "checkout"}
    <script>
        dataLayer.push({$datalayer nofilter});
    </script>
{/if}