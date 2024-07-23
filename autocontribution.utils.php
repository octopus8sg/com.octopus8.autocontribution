<?php

function _autocontribution_civicrm_civix_managed(&$entities){
  $mgdFiles = _autocontribution_civix_find_files(__DIR__, '*.mgd.php');
  foreach ($mgdFiles as $file) {
    $es = include $file;
    foreach ($es as $e) {
      if (empty($e['module'])) {
        $e['module'] = 'org.octopus8.autocontribution';
      }
      $entities[] = $e;
    }
  }
}

function _autocontribution_utils_createEntity(String $entityType, array $params){
  return array(
        'module' => 'com.octopus8.autocontribution',
        'name' => $params['name'],
        'entity' => $entityType,
        'params' => array(
          'version' => 4,
          'values' => $params
        )
    );
}

function _autocontribution_utils_extractDigits(String $string){
  preg_match('/(\d+)$/', $string, $matches); // Match one or more digits at the end of the string
    if (isset($matches[1])) {
        return (int)$matches[1]; // Return the matched digits as an integer
    }
  return null; // Return null if no digits found
}