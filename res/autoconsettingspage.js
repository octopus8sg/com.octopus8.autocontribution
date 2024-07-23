//toggle status button
function pencon_toggleFieldStatus(id, status){
    var updatedIsActive = !status;
    enordis = updatedIsActive ? "Enable" : "Disable"
    CRM.confirm({
      title: enordis,
      message: "Are you sure you want to " + enordis + " this field?",
      buttonLabels: { ok: "Yes", cancel: "No" }
    })
    .on('crmConfirm:yes', function() {
      CRM.api4('CustomField', 'update', {
        values: {"is_active": updatedIsActive},
        where: [["id", "=", id]]
      }).then(function(results) {
        // do something with results array
        location.reload();
      }, function(failure) {
        // handle failure
        location.reload();
      });
    })
    .on('crmConfirm:no', function() {
      // Don't do something
    });
  }
CRM.$(function($){
let groupSelect = document.getElementById('select_group');
let fieldSelect = document.getElementById('select_field');
let fieldInputDiv = document.getElementById('fieldInputDiv');
let fieldTable = document.getElementById('fieldTable');
let currFields;

loadEntities();
populateSelect('CustomGroup', groupSelect);

groupSelect.addEventListener('change', toggleFieldInputView)
//To populate select fields
async function populateSelect(entity, selectToPop){
    isGroup = entity === 'CustomGroup';

    options = [];

    CRM.api4(entity,'get', isGroup ?  {
    where: [["extends", "=", 'Contribution']]
    } : {
    where: [["custom_group_id", "=", groupSelect.value]]
    }).then(function(returnedArr){
    selectToPop.innerHTML = '';
    //default
    const defOption = document.createElement('option');
    defOption.value = 'null';
    defOption.text = `--Select a ${entity}--`;
    selectToPop.append(defOption);
    returnedArr.forEach(arr => {
        options[arr.id] = isGroup ? arr.title : arr.label;
    });
    if (isGroup){
        for (const option in options){
        const optionHTML = document.createElement('option');
        optionHTML.value = option;
        optionHTML.text = options[option];
        selectToPop.append(optionHTML); 
        }
    } else {
        for (const option in options){
        let isExist = false;
        for (const field of currFields){
            if (field.name.match(/\d+$/) != null){
            if (option === field.name.match(/\d+$/)[0]){
                isExist = true;
                break;
            }
            }
        }
        const optionHTML = document.createElement('option');
        optionHTML.value = option;
        optionHTML.text = isExist ? options[option] + " (Added)" : options[option];
        isExist ? optionHTML.disabled = true : null;
        selectToPop.append(optionHTML);
        }
    }
    selectToPop.value = 'null';
    });
}
async function loadEntities(){
    currFields = await CRM.api4('CustomField', 'get', {
        where: [["custom_group_id:name", "=", "pencon_customgroup"]],
    });
    //console.log(currFields);
    populateCurrentFields();
}
async function populateCurrentFields() {
    try {
    const fieldDataPromises = currFields.map(async (field) => {
        if (field.name.startsWith("autocon_cloned_")) {
        const originalID = parseInt(field.name.match(/\d+$/)[0]);
        const fieldData = await CRM.api4('CustomField', 'get', {
            where: [["id", "=", originalID]],
        });
        const groupData = await CRM.api4('CustomGroup', 'get', {
            where: [["id", "=", fieldData[0]['custom_group_id']]],
        });
        field.distitle = groupData[0]['title'];
        }
        return field;
    });

    const fieldsWithData = await Promise.all(fieldDataPromises);

    fieldsWithData.forEach((field) => {
        const distitle = field.distitle || '';
        const fieldRow = $(`<tr id="crmF-${field.id}" ${field.is_active === false ? 'class="disabled"' : ''}></tr>`)
        .append($(`<td>${field.label}</td>`))
        .append($(`<td>${field.name.startsWith("autocon_cloned_") ? distitle : ' '}</td>`))
        .append($(`<td>${!field.name.startsWith("autocon_cloned_")
            ? ' '
            : `<a class="action-item crm-hover-button dis-button" href="javascript:pencon_toggleFieldStatus(${field.id}, ${field.is_active})">${field.is_active === false ? 'Enable' : 'Disable'}</a></td>`}`));
        $(fieldTable).append(fieldRow);
    });
    } catch (error) {
    // handle errors
    }
}
//Toggle the field view whether the group input is something other than default
function toggleFieldInputView(){
    if (groupSelect.value !== 'null'){
    fieldInputDiv.style.display = 'block';
    populateSelect('CustomField', fieldSelect);
    } else {
    fieldInputDiv.style.display = 'none';
    $(fieldSelect).empty();
    }
}
//When cancel button is clicked
$('#cancelButton').on('click', function(){
    groupSelect.value = 'null';
    $(fieldSelect).empty();
    toggleFieldInputView();
});
//add new field button
$('#addButton').on('click',async function(){
    const groupID = fieldSelect.options[fieldSelect.selectedIndex].textContent;
    const fieldID = fieldSelect.value;
    CRM.confirm({
    title: "Add Field",
    message: "Are you sure you want to add " + groupID ,
    buttonLabels: { ok: "Yes", cancel: "No" }
    })
    .on('crmConfirm:yes', async function() {
        let fieldToClone = await CRM.api4('CustomField', 'get', {
        where: [["id", "=", fieldID]]
        }, 0);
        delete fieldToClone['id'], ['custom_group_id'], ["column_name"], ['weight'];
        fieldToClone['name'] = "autocon_cloned_" + fieldID;
        fieldToClone['custom_group_id.name'] = "pencon_customgroup";
        CRM.api4('CustomField', 'create', {
            values : fieldToClone
        }).then(function(results) {
            location.reload();
        }, function(failure) {
        // handle failure
            console.log(`Failure : ${failure}`);
        });        
    })
    .on('crmConfirm:no', function() {
    // Don't do something
    });
    //get groupt to clone

});
})
