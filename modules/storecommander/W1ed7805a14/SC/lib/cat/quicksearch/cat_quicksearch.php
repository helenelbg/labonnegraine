<?php

?>
<div id="search">
    <form id="searchbox" action="" method="GET" onSubmit="return false;">
        <div id="quicksearch" style="margin-top: 2px;">
            <input id="search_query" class="ac_input" value="" type="search" name="search_query" placeholder="<?php echo _l('Search'); ?>" onClick="this.select();" />
            <i id="quicksearch_loading" class="fas fa-spinner fa-pulse" style="display: none;position:absolute;left:-22px;top:3px;" ></i>
        </div>
        <div id="menuObj" class="align_right" dir="ltr"></div>
        <input type="submit" style="display:none" class="autocomplete"/>
    </form>
</div>
<script type="text/javascript">
id_product_attributeToSelect=0;
$('document').ready(function(){
    sc_qs_filter = string_ini_to_object(getParamUISettings('start_cat_quicksearch_filter'));
    if (sc_qs_filter) {
            myAutoCompleteURL="index.php?ajax=1&act=cat_quicksearch_get"+
                "&id_product="+sc_qs_filter['id_product']+
                "&id_product_attribute="+sc_qs_filter['id_product_attribute']+
                "&name="+sc_qs_filter['name']+
                "&reference="+sc_qs_filter['reference']+
                "&supplier_reference="+sc_qs_filter['supplier_reference']+
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                "&supplier_reference_all="+sc_qs_filter['supplier_reference_all']+
                <?php } ?>

                "&upc="+sc_qs_filter['upc']+
                <?php if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) { ?>
                "&mpn="+sc_qs_filter['mpn']+
                <?php } ?>

                "&ean="+sc_qs_filter['ean']+
                "&short_desc="+sc_qs_filter['short_desc']+
                "&desc="+sc_qs_filter['desc']+
                "&how_equal="+sc_qs_filter['how_equal'];

        } else {
            myAutoCompleteURL="index.php?ajax=1&act=cat_quicksearch_get";
            var sc_qs_filter = {
                'id_product':1,
                'id_product_attribute':1,
                'name':0,
                'reference':1,
                'supplier_reference':0,
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                'supplier_reference_all':0,
                <?php } ?>
                'ean':0,
                'upc':0,
                <?php if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) { ?>
                'mpn':0,
                <?php } ?>
                'short_desc':0,
                'desc':0,
                'how_equal':0
            };
        }
        myAutoCompleteLoading = "quicksearch_loading";
        $("#search_query").autocomplete("index.php?ajax=1&act=cat_quicksearch_get",{
                minChars: 1,
                max: 20,
                width: 500,
                cacheLength:0,
                selectFirst: false,
                scroll: false,
                blockSubmit:true,
                dataType: "json",
                formatItem: function(data, i, max, value, term){
                    return value;
                },
                parse: function(data){
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++){
                            mytab[mytab.length]={
                                data: data[i],
                                value: data[i].cname+' > '+data[i].pname
                            };
                        }
                        return mytab;
                },
                extraParams:{
                    ajaxSearch: 1
                }
        })
        .result(function(event, data, formatted){
                lastProductSelID=0;
                if (typeof data!='undefined')
                {
                    catselection=0;
                    cat_tree.openItem(data.id_category);
                    cat_tree.selectItem(data.id_category,false);
                    catselection=data.id_category;
                    displayProducts('id_product_attributeToSelect='+Number(data.id_product_attribute)+';lastProductSelID=0;idxProductID=cat_grid.getColIndexById("id");oldFilters["id"]="'+data.id_product+'";cat_grid.getFilterElement(idxProductID).value="'+data.id_product+'";cat_grid.filterByAll();cat_grid.selectRowById('+data.id_product+',false,true,true);');
                }
                return false;
        })

    filterQuickSearch = new dhtmlXMenuObject("menuObj");
    qsXMLMenuData=''+
    '<menu>'+
    '<item id="filters" text="<?php echo _l('Filters'); ?>" img="fa fa-filter" imgdis="fa fa-filter">'+
            '<item id="id_product" type="checkbox" '+(sc_qs_filter['id_product']==1?'checked="true"':'')+' text="<?php echo _l('id_product'); ?>"></item>'+
            '<item id="id_product_attribute" type="checkbox" '+(sc_qs_filter['id_product_attribute']==1?'checked="true"':'')+' text="<?php echo _l('id_product_attribute'); ?>"></item>'+
            '<item id="name" type="checkbox" '+(sc_qs_filter['name']==1?'checked="true"':'')+' text="<?php echo _l('Name'); ?>"></item>'+
            '<item id="reference" type="checkbox" '+(sc_qs_filter['reference']==1?'checked="true"':'')+' text="<?php echo _l('Reference'); ?>"></item>'+
            '<item id="supplier_reference" type="checkbox" '+(sc_qs_filter['supplier_reference']==1?'checked="true"':'')+' text="<?php echo _l('Default Supplier Ref.'); ?>"></item>'+
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
        '<item id="supplier_reference_all" type="checkbox" '+(sc_qs_filter['supplier_reference_all']==1?'checked="true"':'')+' text="<?php echo _l('All Supplier Ref.'); ?>"></item>'+
        <?php } ?>
        '<item id="ean" type="checkbox" '+(sc_qs_filter['ean']==1?'checked="true"':'')+' text="<?php echo _l('EAN13'); ?>"></item>'+

        '<item id="upc" type="checkbox" '+(sc_qs_filter['upc']==1?'checked="true"':'')+' text="<?php echo _l('UPC'); ?>"></item>'+
        <?php if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) { ?>
        '<item id="mpn" type="checkbox" '+(sc_qs_filter['mpn']==1?'checked="true"':'')+' text="<?php echo _l('MPN'); ?>"></item>'+
        <?php } ?>
        '<item id="short_desc" type="checkbox" '+(sc_qs_filter['short_desc']==1?'checked="true"':'')+' text="<?php echo _l('Short description'); ?>"></item>'+
        '<item id="desc" type="checkbox" '+(sc_qs_filter['desc']==1?'checked="true"':'')+' text="<?php echo _l('Description'); ?>"></item>'+
        '<item type="separator"></item>'+
        '<item id="all_tick" text="<?php echo _l('Tick all filters', 1); ?>"></item>'+
        '<item id="all_untick" text="<?php echo _l('Untick all filters', 1); ?>"></item>'+
        '<item type="separator"></item>'+
        '<item id="how_equal" type="checkbox" '+(sc_qs_filter['how_equal']==1?'checked="true"':'')+' text="<?php echo _l('Exact search'); ?>"></item>'+
    '</item>'+
    '</menu>';
    filterQuickSearch.setIconset('awesome');
    filterQuickSearch.loadStruct(qsXMLMenuData);
    function onMenuClick(id){
        if(id=='all_tick') {
            filterQuickSearch.forEachItem(function(itemId){
                filterQuickSearch.setCheckboxState(itemId, 1);
            });
        }
        if(id=='all_untick') {
            filterQuickSearch.forEachItem(function(itemId){
                filterQuickSearch.setCheckboxState(itemId, 0);
            });
        }
        sc_qs_filter['id_product'] = Number(filterQuickSearch.getCheckboxState('id_product'));
        sc_qs_filter['id_product_attribute'] = Number(filterQuickSearch.getCheckboxState('id_product_attribute'));
        sc_qs_filter['name'] = Number(filterQuickSearch.getCheckboxState('name'));
        sc_qs_filter['reference'] = Number(filterQuickSearch.getCheckboxState('reference'));
        sc_qs_filter['supplier_reference'] = Number(filterQuickSearch.getCheckboxState('supplier_reference'));
        sc_qs_filter['supplier_reference_all'] = Number(filterQuickSearch.getCheckboxState('supplier_reference_all'));
        sc_qs_filter['ean'] = Number(filterQuickSearch.getCheckboxState('ean'));
        sc_qs_filter['upc'] = Number(filterQuickSearch.getCheckboxState('upc'));
        <?php if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) { ?>
        sc_qs_filter['mpn'] = Number(filterQuickSearch.getCheckboxState('mpn'));
        <?php } ?>
        sc_qs_filter['short_desc'] = Number(filterQuickSearch.getCheckboxState('short_desc'));
        sc_qs_filter['desc'] = Number(filterQuickSearch.getCheckboxState('desc'));
        sc_qs_filter['how_equal'] = Number(filterQuickSearch.getCheckboxState('how_equal'));
        myAutoCompleteURL="index.php?ajax=1&act=cat_quicksearch_get"+
            "&id_product="+sc_qs_filter['id_product']+
            "&id_product_attribute="+sc_qs_filter['id_product_attribute']+
            "&name="+sc_qs_filter['name']+
            "&reference="+sc_qs_filter['reference']+
            "&supplier_reference="+sc_qs_filter['supplier_reference']+
            "&supplier_reference_all="+sc_qs_filter['supplier_reference_all']+
            "&ean="+sc_qs_filter['ean']+
            "&upc="+sc_qs_filter['upc']+
            <?php if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) { ?>
            "&mpn="+sc_qs_filter['mpn']+
            <?php } ?>
            "&short_desc="+sc_qs_filter['short_desc']+
            "&desc="+sc_qs_filter['desc']+
            "&how_equal="+sc_qs_filter['how_equal'];
        saveParamUISettings('start_cat_quicksearch_filter', object_to_string_ini(sc_qs_filter));
        return true;
    }
    filterQuickSearch.attachEvent("onClick",onMenuClick);
});
</script>
