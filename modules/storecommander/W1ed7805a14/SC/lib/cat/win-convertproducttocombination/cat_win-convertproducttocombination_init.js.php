<?php
if (!defined('STORE_COMMANDER')) {
    exit;
}



echo '<script>'; ?>
const winConvertProductToCombinationLayout = wConvertProductToCombination.attachLayout("2U");
const attribute_col_prefix = 'attr_';
let errors = [];
// prepare json object for convert_data
let convertData = {};
cat_grid.getSelectedRowId().split(',').forEach(function(element) {
    convertData[element] = {};
})




// -----------------------------
// ATTRIBUTE GROUPS
// -----------------------------
const winConvertProductToCombinationAttributesSelection_cell = winConvertProductToCombinationLayout.cells('a');
winConvertProductToCombinationAttributesSelection_cell.setText('Attribute and groups');
winConvertProductToCombinationAttributesSelection_cell.setWidth(450);
const groupAttributesGridUrl = "index.php?ajax=1&act=cat_win-convertproducttocombination_attributegroup_get&id_lang=" + SC_ID_LANG + "&" + new Date().getTime();
winConvertProductToCombinationAttributesSelection_cell._grid = winConvertProductToCombinationAttributesSelection_cell.attachGrid({xml: groupAttributesGridUrl});
winConvertProductToCombinationAttributesSelection_cell.cell.classList.add('service', 'attribute_groups_selection');
winConvertProductToCombinationAttributesSelection_cell._grid._name = 'ConvertProductToCombinationAttributeGroupsGrid';
winConvertProductToCombinationAttributesSelection_cell._grid.setImagePath("lib/js/imgs/");
winConvertProductToCombinationAttributesSelection_cell._grid.enableDragAndDrop(false);
winConvertProductToCombinationAttributesSelection_cell._grid.enableMultiselect(false);
// disable row selection
winConvertProductToCombinationAttributesSelection_cell._grid.attachEvent("onBeforeSelect", function () {
    return false;
});
// adding buttons for each row
winConvertProductToCombinationAttributesSelection_cell._grid.attachEvent("onRowCreated", function (id) {
    let idxName = winConvertProductToCombinationAttributesSelection_cell._grid.getColIndexById('name');
    let idxSelect = winConvertProductToCombinationAttributesSelection_cell._grid.getColIndexById('select');
    let name = winConvertProductToCombinationAttributesSelection_cell._grid.cells(id, idxName).getValue();
    let button = '<input id="add_group_' + id + '" type="button" class="button add_attribute_group" data-groupid="' + id + '" data-grouplabel="' + name + '" value="<?php echo ucfirst(_l('add to selection'));?> >">'
    winConvertProductToCombinationAttributesSelection_cell._grid.cells(id, idxSelect).setValue(button);
    return true;
});

// gestion de la selection des groupes d'attributs dans cellule des groupes d'attributs
winConvertProductToCombinationAttributesSelection_cell._grid.attachEvent("onXLE", function () {
    winConvertProductToCombinationAttributesSelection_cell.cell.querySelectorAll('.add_attribute_group').forEach(function (element) {
        element.addEventListener('click', function () {
            // ajout de la colonne dans la grid preview
            addGroupToSelection(element);
            this.style.display = 'none';
        });
    })
})


// -----------------------------
// FORMULAIRE
// -----------------------------
const winConvertProductToCombinationsPreview_cell = winConvertProductToCombinationLayout.cells('b');
const convertProductToCombinationFormStructure = [
    {type: "settings", position: "label-left", margin: "0"},
    {
        type: "input",
        name: "new_product_name",
        width: '450',
        note: {text: "<?php echo _l('New parent product for these combinations', 1); ?>"},
        label: "<?php echo _l("Product name", 1) ?>",
        checked: false
    },
    {
        type: "hidden",
        name: "convert_data",
        value: JSON.stringify(convertData)
    },
    {
        type: "hidden",
        name: "default_product_id",
        value: Object.keys(convertData)[0]
    }
];


winConvertProductToCombinationsPreview_cell.setText('Configuration');
winConvertProductToCombinationsPreview_cell.cell.classList.add('service');
winConvertProductToCombinationsPreview_cell._name = 'ConvertProductToCombinationPreview';
winConvertProductToCombinationsPreview_cell.attachURL('index.php?act=cat_win-convertproducttocombination_preview&id_lang=' + SC_ID_LANG + '&ajax=1', true, {'product_ids': cat_grid.getSelectedRowId()});

winConvertProductToCombinationLayout.attachEvent('onContentLoaded', function(id){
    if(id === 'b'){
        // FORM
        const convertProductToCombinationForm = new dhtmlXForm("convert_products_to_combination_form_container", convertProductToCombinationFormStructure);
        const convertDataField = winConvertProductToCombinationsPreview_cell.cell.querySelector('input[name="convert_data"]');
        const defaultDataField = winConvertProductToCombinationsPreview_cell.cell.querySelector('input[name="default_product_id"]');
        // GRID
        winConvertProductToCombinationsPreview_cell._grid = dhtmlXGridFromTable("combinations_list");
        winConvertProductToCombinationsPreview_cell._grid.enableAutoWidth(true);
        winConvertProductToCombinationsPreview_cell._grid.enableAutoHeight(true);
        winConvertProductToCombinationsPreview_cell._grid.setSizes();

        // modification d'un valeur d'attribut dans la grille
        winConvertProductToCombinationsPreview_cell._grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){

            let current_col_idx = winConvertProductToCombinationsPreview_cell._grid.getColumnId(cInd);
            // MAJ du champ caché contenant le row id de la declinaison par defaut
            if(stage === 0) {
                defaultDataField.value =rId;
            }
            if(stage === 2){
                if(current_col_idx !== undefined && current_col_idx.indexOf(attribute_col_prefix) === 0){
                    // enregistrement group_id -> attribute value
                    let convertData = JSON.parse(convertDataField.value);
                    convertData[rId][current_col_idx.replace(attribute_col_prefix,'')] = nValue;
                    convertDataField.value =JSON.stringify(convertData);
                    winConvertProductToCombinationsPreview_cell._grid.cells(rId,cInd).cell.classList.remove('sc_cell_error');
                }
            }
            return true;
        })

        // envoi formulaire
        winConvertProductToCombinationsPreview_cell.cell.querySelector('#create_combinations').addEventListener('click', function() {
            errors = [];
            validateProductName();
            validateAttributeValues();
            if(errors.length > 0){
                dhtmlx.message({
                    text: errors,
                    type: 'error'
                });
            } else {
                convertProductToCombinationForm.send("index.php?ajax=1&act=cat_win-convertproducttocombination_update&idc=<?php echo Tools::getValue('idc','');?>", convertProductToCombinationsAfterSaved(this));
            }
        });
    }
})


function convertProductToCombinationsAfterSaved(){
    wConvertProductToCombination.setModal(true);
    wConvertProductToCombination.setDimension(500,300);
    wConvertProductToCombination.setPosition(($(window).width()-500)/2, ($(window).height()-300)/2);
    const wConvertProductToCombinationFinalLayout = wConvertProductToCombination.attachLayout("1C");
    wConvertProductToCombinationFinalCell = wConvertProductToCombinationFinalLayout.cells('a');
    wConvertProductToCombinationFinalCell.hideHeader(true);
    wConvertProductToCombinationFinalCell.attachURL('index.php?act=cat_win-convertproducttocombination_final&ajax=1', true, {'product_id': 56361});
}

function addGroupToSelection(element){
    // add column with combo values
    if(winConvertProductToCombinationsPreview_cell._grid.getAllRowIds() === ''){
        return;
    }
    let colIndex = winConvertProductToCombinationsPreview_cell._grid.getColumnsNum();
    winConvertProductToCombinationsPreview_cell._grid.insertColumn(colIndex);
    winConvertProductToCombinationsPreview_cell._grid.setColumnId(colIndex, attribute_col_prefix+element.dataset.groupid);
    winConvertProductToCombinationsPreview_cell._grid.setColumnExcellType(colIndex,'coro');
    winConvertProductToCombinationsPreview_cell._grid.refreshComboColumn(colIndex);
    winConvertProductToCombinationsPreview_cell._grid.setColLabel(colIndex, element.dataset.grouplabel+'<a class="button secondary remove_column" data-groupid="'+element.dataset.groupid+'" onclick="removeGroupFromSelection(this)"><?php echo ucfirst(_l('remove'));?></a>');

    fetch('index.php?ajax=1&act=cat_win-convertproducttocombination_attributegroup_values.json&attribute_group_id='+element.dataset.groupid, {
        method: "GET",
        headers: {
            "Content-type": "application/json;charset=UTF-8"
        }
    })
    .then(function (response) {
        fetch_status = response.status;
        return response.json();
    })
    .then(function (json) {
        if (fetch_status == 200) {
            json.extra.content.forEach(function(e){
                winConvertProductToCombinationsPreview_cell._grid.getAllRowIds().split(',').forEach(function(rowId){
                    let combobox = winConvertProductToCombinationsPreview_cell._grid.getCustomCombo(rowId,colIndex);
                    combobox.put(e.id_attribute,e.name);
                })
            })
        }
    })
}


function removeGroupFromSelection(e){
    winConvertProductToCombinationAttributesSelection_cell.cell.querySelector('.add_attribute_group[data-groupid="'+e.dataset.groupid+'"]').style.display = 'block';
    let idxColumToRemove = winConvertProductToCombinationsPreview_cell._grid.getColIndexById(attribute_col_prefix+e.dataset.groupid);
    winConvertProductToCombinationsPreview_cell._grid.editStop(); // removing opened combos on remove column
    winConvertProductToCombinationsPreview_cell._grid.deleteColumn(idxColumToRemove);
}


// product name is not set
function validateProductName(){
    let newProductNameField = winConvertProductToCombinationsPreview_cell.cell.querySelector('input[name="new_product_name"]');
    if(newProductNameField.value === ''){
        newProductNameField.classList.add('dhtmlx_validation_error');
        errors.push("<?php echo _l('%s is empty',0,array(_l('Product name'))); ?>");
    } else {
        newProductNameField.classList.remove('dhtmlx_validation_error');
    }
}

// no attribute group added to array
function validateAttributeValues(){
    let flagMissingAttributeGroup = true;
    let flagMissingCellValueError = false;
    winConvertProductToCombinationsPreview_cell._grid.forEachRow(function(id){
        winConvertProductToCombinationsPreview_cell._grid.forEachCell(id,function(cell,ind){
            if (winConvertProductToCombinationsPreview_cell._grid.getColumnId(ind) !== undefined){
                if(winConvertProductToCombinationsPreview_cell._grid.getColumnId(ind).includes(attribute_col_prefix)){
                    flagMissingAttributeGroup = false;
                }
                if (cell.getValue()==""){
                    cell.cell.classList.add('sc_cell_error');
                    flagMissingCellValueError = true;
                } else {
                    cell.cell.classList.remove('sc_cell_error');
                }
            }
        })
    })
    if(flagMissingAttributeGroup){
        errors.push("<?php echo _l('You must add at least one attribute group to create combination(s)',1); ?>");
    }
    if(flagMissingCellValueError){
        errors.push("<?php echo _l('You must fill all combinations values',1); ?>");
    }
}


<?php echo '</script>'; ?>
