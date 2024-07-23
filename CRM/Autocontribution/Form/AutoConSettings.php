<?php

use CRM_Autocontribution_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Autocontribution_Form_AutoConSettings extends CRM_Core_Form {
  /**
   * @throws \CRM_Core_Exception
   */
  public function buildQuickForm(): void {

    // add form elements
    $this->add(
      'select', // field type
      'select_group', // field name
      'Choose Field Group', // field label
      //$this->getGroupOptions(), // list of options
      [],
      TRUE // is required
    );
    $this->add(
      'select', // field type
      'select_field', // field name
      'Choose Field', // field label
      //$this->getSelectedFields(), // list of options
      [],
      TRUE // is required
    );
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);
    $this->assign('curFields', $this->getExistingFields());

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    $values = $this->exportValues();
    CRM_Core_Session::setStatus(E::ts('You picked color "%1"', [
      1 => $options[$values['favorite_color']],
    ]));
    parent::postProcess();
  }

  public function getExistingFields(){
    $customFields = civicrm_api4('CustomField', 'get', [
      'where' => [
        ['custom_group_id:name', '=', 'pencon_customgroup'],
      ],
      'checkPermissions' => TRUE,
    ]);
    
    $custFieldArray=[];
    foreach ($customFields as $fields){
      $customGroupSearch = civicrm_api4('CustomGroup', 'get', [
        'where' => [
          ['id', '=', $fields['custom_group_id']],
        ],
        'checkPermissions' => TRUE,
      ], 0);
      $customGroupName = $customGroupSearch['title'];
      //$custFieldArray[] = $fields['label'];
      $custFieldArray[] = ['fieldData' => $fields, 'fromGroup' => $customGroupName];
    };
    return $custFieldArray;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames(): array {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
