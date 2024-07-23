<?php
$autocontribution_initarray = array(
    //ACTIVITY
    'activity' => array(
        'type' => 'OptionValue',
        'params' => array(
            'label' => 'Pending Contribution',
			'name' => 'pencon_activitytype',
			'option_group_id.name' => 'activity_type',
			'is_active' => true,
        ),
    ),
    //CUSTOM GROUP
    'group' => array(
        'type' => 'CustomGroup',
        'params' => array(
			'title' => 'Pending Contribution Fields',
			'name' => 'pencon_customgroup',
			'extends_entity_column_value:name' => 'pencon_activitytype',
			'extends' => 'Activity',
			'style' => 'Inline',
			'is_active' => TRUE,
		),
    ),
    //OPTION GROUP (FINANCIAL TYPE)
    'OPfinancial' => array(
		'type' => "OptionGroup",
		'params' => array(
			'name' => 'pencon_select_fintype',
			'title' => 'Pending Contribution Fields :: Financial Type',
			'option_value_fields' => ['name', 'label', 'description'],
			'is_active' => TRUE,
			'data_type' => 'Integer',
		),
	),
    //CUSTOM FIELD: (FINANCIAL TYPE)
	'CFfinancial' => array(
		'type' => "CustomField",
		'params' => array(
			'custom_group_id:name' => 'pencon_customgroup',
			'option_group_id:name' => 'pencon_select_fintype',
			'name' => 'pencon_cf_fintype',
			'label' => 'Financial Type',
			'html_type' => 'Select',
			'data_type' => 'Int',
			'is_active' => TRUE,
			'is_searchable' => TRUE,
			'is_required' => TRUE,
		),
	),
    //CUSTOM FIELD (AMMOUNT)
	'CFammount' => array(
		'type' => "CustomField",
		'params' => array(
			'custom_group_id:name' => 'pencon_customgroup',
			'name' => 'pencon_cf_ammount',
			'label' => 'Total Amount',
			'html_type' => 'Text',
			'data_type' => 'Money',
			'is_active' => TRUE,
			'is_searchable' => TRUE,
			'is_required' => TRUE,
		),
	),
	//CUSTOM FIELD (SOURCE)
	'CFsource' => array(
		'type' => "CustomField",
		'params' => array(
			'custom_group_id:name' => 'pencon_customgroup',
			'name' => 'pencon_cf_source',
			'label' => 'Source',
			'html_type' => 'Text',
			'data_type' => 'String',
			'is_active' => TRUE,
			'is_searchable' => TRUE,
		),
	),
	
	//CUSTOM FIELD (PAYMENT INSTRUMENT)
	'CFpayment' => array(
		'type' => "CustomField",
		'params' => array( 
			'custom_group_id:name' => 'pencon_customgroup',
			'option_group_id.name' => 'payment_instrument',
			'name' => 'pencon_cf_paymeth',
			'label' => 'Payment Method',
			'html_type' => 'Select',
			'data_type' => 'Int',
			'is_active' => TRUE,
			'is_searchable' => TRUE,
			'is_required' => TRUE,
		),
	),
);