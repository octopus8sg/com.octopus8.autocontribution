<?php

require_once 'autocontribution.civix.php';
require_once 'autocontribution.utils.php';
require_once 'autocontribution.array.php';
// phpcs:disable
use CRM_Autocontribution_ExtensionUtil as E;

global $_autocontribution_initarray;
$_autocontribution_initarray = $autocontribution_initarray;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function autocontribution_civicrm_config(&$config): void {
  _autocontribution_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function autocontribution_civicrm_install(): void {
  _autocontribution_civix_civicrm_install();
}

function autocontribution_civicrm_enable(): void {
  try{
    civicrm_api4('OptionGroup', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['name', '=', 'payment_instrument'],
      ],
      'checkPermissions' => TRUE,
    ]);
    civicrm_api4('OptionValue', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['option_group_id:name', '=', 'payment_instrument'],
      ],
      'checkPermissions' => TRUE,
    ]);
  }catch (CRM_Core_Exception $err){

  }
  _autocontribution_civix_civicrm_enable();
}

function autocontribution_civicrm_postCommit(string $op, string $objectName, int $objectId, &$objectRef): void{
  // civi::log()->error('Hello :)');
  // civi::log()->error($op);
  // civi::log()->error($objectId);
  // civi::log()->error(print_r($objectName, true));
  // civi::log()->error(print_r($objectRef, true));
  if ($objectName == 'Activity' && $op == 'edit') {    
    $activityID = civicrm_api4('OptionValue', 'get', [
      'where' => [ 
        ['name', '=', 'pencon_activitytype'],
      ],
      'checkPermissions' => TRUE,
    ], 0)['value'];

    $activity = civicrm_api4('Activity', 'get', [
      'select' => [
        'activity_type_id',
      ],
      'where' => [
        ['id', '=', $objectId],
      ],
      'checkPermissions' => FALSE,
    ], 0);

    $activitytypeID = $activity['activity_type_id'];

    if ($activitytypeID == $activityID) {
      if ($objectRef->status_id == 2) { // Completed status
        $getContact = civicrm_api4('ActivityContact', 'get', [
          'where' => [
          ['activity_id', '=', $objectId],
          ['record_type_id:name', '=', 'Activity Targets'],
          ],
        ],0);
        $contactID = $getContact['contact_id'];

        $activity = civicrm_api4('Activity', 'get', [
          'select' => [
            'pencon_customgroup.*',
            'activity_date_time'
          ],
          'where' => [
            ['activity_type_id:name', '=', 'pencon_activitytype'],
            ['id', '=', $objectId],
          ],
          'checkPermissions' => TRUE,
        ], 0);
        
        //new contribution array
        $newContArrays = array(
          'contact_id' => $contactID,
          'total_amount' => $activity['pencon_customgroup.pencon_cf_ammount'],
          'currency:name' => 'SGD', 
          'payment_instrument_id' => $activity['pencon_customgroup.pencon_cf_paymeth'],
          'receive_date' => $activity['activity_date_time'],
          'source' =>  $activity['pencon_customgroup.pencon_cf_source'],
          'contribution_status_id' => 1,
          'financial_type_id' => $activity['pencon_customgroup.pencon_cf_fintype'],
        );
        foreach ($activity as $key => $value) {
          if (strpos($key, 'pencon_customgroup.autocon_cloned_') === 0){
            $clonedFieldID = _autocontribution_utils_extractDigits($key);
            $customfieldDetails = civicrm_api4('CustomField', 'get', [
              'where' => [
                ['id', '=', $clonedFieldID],
              ],
              'checkPermissions' => TRUE,
            ], 0);
            $customGroupDetails = civicrm_api4('CustomGroup', 'get', [
              'where' => [
                ['id', '=', $customfieldDetails['custom_group_id']],
              ],
              'checkPermissions' => TRUE,
            ], 0);
            $newContArrays[$customGroupDetails['name'] . "." . $customfieldDetails['name']] = $value;
          } else {
            
          }
        }
        $results = civicrm_api4('Contribution', 'create', [
          'values' => $newContArrays,
          'checkPermissions' => TRUE,
        ]);
      } else {
      }
      
    }
  }
  if ($objectName == 'FinancialType'){
    //if financial type was created
    if ($op == 'create'){
      $results = civicrm_api4('OptionValue', 'create', [
        'values' => [
        'option_group_id.name' => 'pencon_select_fintype',
        'label' => $objectRef->name,
        'value' => $objectId,
        'is_active' => $objectRef->is_active,
        'name' => 'finType :: ' . $objectRef->name,
        ],
      ]);
    }
    //if financial type was edited
    if ($op == 'edit'){
      $results = civicrm_api4('OptionValue', 'update', [
        'values' => [
          'label' => $objectRef->name,
          'is_active' => $objectRef->is_active,
        ],
        'where' => [
          ['option_group_id:name', '=', 'pencon_select_fintype'],
          ['value', '=', $objectId],
        ],
      ]);
    }
    //if financial type was deleted
    if ($op == 'delete'){
      try{
        $results = civicrm_api4('OptionValue', 'delete', [
          'where' => [
          ['option_group_id:name', '=', 'pencon_select_fintype'],
          ['value', '=', $objectId],
          ],
        ]);
      } catch (CRM_Core_Exception $err){
      }
    }
  }
  //FOR: CHECKING IF ORIGINAL FIELD WAS EDITED/DELETED
  if ($objectName == 'CustomField' && $op == 'edit'){
    //CHECKS IF FIELD EDITED WAS EXTENDED TO CONTRIBUTION
    try{
      $originalField = civicrm_api4('CustomField', 'get', [
            'where' => [
              ['custom_group_id.extends', '=', 'Contribution'],
              ['id', '=', $objectId],
            ],'checkPermissions' => TRUE,
          ], 0);
          if (empty($originalField)){

          } else {
            //GET ALL CLONED FIELDS IN PENCON_CUSTOMGROUP
            $clonedFields = civicrm_api4('CustomField', 'get', [
              'where' => [
                ['custom_group_id:name', '=', 'pencon_customgroup'],
                ['name', 'CONTAINS', 'autocon_cloned_'],
              ],
              'checkPermissions' => TRUE,
            ]);
            //COMPARES EDITED FIELD TO CLONED FIELDS TO SEE IF IT EXISTS IN THE CUSTOM GROUP
            foreach($clonedFields as $clonedField){
              $clonedFieldID = _autocontribution_utils_extractDigits($clonedField['name']);
              //IF IT DOES EXIST:
              if ($clonedFieldID == $objectId){
                $updatedVals = [];
                //STORES EDITED PARAMETERS INTO AN ARRAY
                foreach ($originalField as $key => $value) {
                  $updatedVals[$key] = $value;
                }
                //REMOVES PARAMETERS THAT WILL CONFLICT WITH THE UPDATE API
                $keysToUnset = ['id', 'custom_group_id', 'column_name', 'weight', 'name', 'custom_group_id'];
                foreach ($keysToUnset as $key){
                  unset($updatedVals[$key]);
                };
                //UPDATES THE CLONED FIELD
                $results = civicrm_api4('CustomField', 'update', [
                  'values' => $updatedVals,
                  'where' => [
                    ['name', '=', $clonedField['name']],
                  ],
                  'checkPermissions' => TRUE,
                ]);
              } else {
              }
            }
          }
    } catch (CRM_Core_Exception $err) {

    }
    
  }
}


//Civi::log()->debug(gettype($clonedFieldID) . ": " . $clonedFieldID);
function autocontribution_civicrm_managed(&$entities):void{
  global $_autocontribution_initarray;
  foreach ($_autocontribution_initarray as $arr){
    $entities[] = _autocontribution_utils_createEntity($arr['type'], $arr['params']);
  }
  $financialTypes = civicrm_api4('FinancialType', 'get', [
    'checkPermissions' => TRUE,
  ]);
  foreach ($financialTypes as $type){
    $disparam = array(
      'option_group_id.name' => 'pencon_select_fintype',
      'label' => $type['name'],
      'value' => $type['id'],
      'is_active' => $type['is_active'],
      'name' => 'finType :: ' . $type['name']
    );
    $entities[] = _autocontribution_utils_createEntity('OptionValue', $disparam);
  }
}

function autocontribution_civicrm_navigationMenu(&$menu) {
  _autocontribution_civix_insert_navigation_menu($menu, 'Contributions', [
    'label' => E::ts('Auto Contribution Settings'),
    'name' => 'autoconsettings',
    'url' => 'civicrm/autoconsettings',
    'permission' => 'Access Civicrm',
    'icon' => 'fa-bars'
  ]);
}

//_autocontribution_civix_civicrm_managed($entities);
