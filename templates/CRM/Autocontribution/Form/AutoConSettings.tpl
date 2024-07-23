{* HEADER *}

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}
<div id="boda">
  <h1>Manage Fields</h1>
  <p>Add/Remove extra field details you want mapped</p>
  <p>*NOTE table might take a few seconds to load depending on size of Pending Contributions.</p>
  <p>*NOTE if it doesn't load, there may be an invalid field in Pending Contributions that does not correlate to an existing field, please delete the invalid cloned field</p>
  <table class="dataTable">
    <tbody id="fieldTable">
      <tr>
        <th>Field Name</th>
        <th>Cloned from Custom Field Group</th>
        <th width="10%"> </th>
      </tr>
    </tbody>
  </table>
  <div id="formInputs">
    <div>
      <div>{$form.select_group.label}</div>
      <div>{$form.select_group.html}</div>
    </div>
    <div id="fieldInputDiv" style="display:none">
      <div>{$form.select_field.label}</div>
      <div style="display: flex">
        <div style="width: 70%">{$form.select_field.html}</div>
        <div id="addButton">
          <a style="text-align: center; padding-top:5px; padding-left: 25px;">Add <i class="crm-i fa-plus" aria-hidden="true"></i></a>
        </div>
        <div id="cancelButton">
          <a style="text-align: center; padding-top:5px; padding-left: 25px;">Cancel <i class="crm-i fa-times" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
  </div>
  
</div>
{crmStyle ext="com.octopus8.autocontribution" file="res/styles.css"}
{crmScript ext="com.octopus8.autocontribution" file="res/autoconsettingspage.js"}
