<?
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/

	$test[]='0.9.16.000';
	$test[]='0.9.16.001';
	$test[]='0.9.18.001';
	$test[]='0.9.18.002';
	$test[]='0.9.18.003';
	$test[]='0.9.18.004';
	$test[]='0.9.18.005';
	$test[]='0.9.18.006';
					
	function ged_upgrade0_9_16_000()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('ged_elements','validity_period',array('type'=>'int', 'precision'=>4, 'nullable'=>True, 'default'=>NULL));		
		
		$old_table_def=array(
			'fd'=>array(
				'url'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False),
				'size'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'status'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False),
				'creator_id'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'validation_date'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'validity_period'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'creation_date'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'minor'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'version_id'=>array('type'=>'auto','nullable'=>False),
				'element_id'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'description'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False),
				'file_extension'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False),
				'file_name'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False,'default'=>'0'),
				'major'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'stored_name'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False)
			),
			'pk'=>array('version_id'),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
		);
		
		$GLOBALS['phpgw_setup']->oProc->DropColumn('ged_versions', $old_table_def, 'validity_period');
		
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('ged_versions','validation_date', array('type'=>'int', 'precision'=>4, 'nullable'=>True, 'default'=>NULL));
		
		/*
		'spcontrol_lifetimes'=>array(
			'fd'=>array(
				'lifetime'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'description'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False)
			)
			*/
			
		$GLOBALS['phpgw_setup']->oProc->CreateTable('ged_periods',
			array(
			'fd'=>array(
				'period'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'description'=>array('type'=>'varchar', 'precision'=>100,'nullable'=>False)
				),
			'pk'=>array('period'),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
			)
			);
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 0, 'aeternel')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 3600, '1 hour')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 86400, '24 hours')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 2592000, '30 days')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 7776000, '90 days')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 15552000, '6 monthes')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 31104000, '1 year')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 93312000, '3 years')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO ged_periods ( period, description) VALUES ( 155520000, '5 years')" );

		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.16.001';
		return $GLOBALS['setup_info']['ged']['currentver'];
	}

	function ged_upgrade0_9_16_001()
	{
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable( 'ged_relations' ,array(
			'fd' => array(
				'relation_id' => array('type' => 'auto','nullable' => False),
				'linked_version_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'linking_version_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'relation_type' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('relation_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		) );
		
		$GLOBALS['phpgw_setup']->oProc->DropTable('ged_history');
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('ged_history' , array(
			'fd' => array(
				'history_id' => array('type' => 'auto','nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'element_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'version_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'status' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'action' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'ip' => array('type' => 'varchar', 'precision' => 16,'nullable' => True),
				'agent' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'logdate' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'comment' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('history_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)	);
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.001';
		return $GLOBALS['setup_info']['ged']['currentver'];
		
	}

	function ged_upgrade0_9_18_001()
	{	
		$GLOBALS['phpgw_setup']->oProc->AddColumn('ged_elements','project_name',
		array('type' => 'varchar', 'precision' => 255,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('ged_elements','project_root', 
		array('type' => 'int', 'precision' => 4,'nullable' => True));		
		
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('ged_versions','url', 
		array('type' => 'varchar', 'precision' => 100,'nullable' => True));
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.002';
		return $GLOBALS['setup_info']['ged']['currentver'];
	
	}

	function ged_upgrade0_9_18_002()
	{	
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_versions SET status='refused' WHERE status='rejected'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET status='refused', action='refused', comment='refused WHERE status='rejected'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET action='approved', comment='approved' WHERE action='accepted'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET action='accepted', comment='accepted' WHERE status='current'" );
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.003';
		return $GLOBALS['setup_info']['ged']['currentver'];
		
	}

	function ged_upgrade0_9_18_003()
	{	
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_history SET status='pending_for_acceptation' WHERE status='pending_for_approval'" );
		$GLOBALS['phpgw_setup']->oProc->query ("UPDATE ged_versions SET status='pending_for_acceptation' WHERE status='pending_for_approval'" );
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.004';
		return $GLOBALS['setup_info']['ged']['currentver'];
		
	}
	
	function ged_upgrade0_9_18_004()
	{
		$old_ged_doc_types_table_def=array(
			'fd'=>array(
				'type_ref'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False),
				'type_desc'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>True),
				'type_chrono'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0'),
				'type_smq_ref'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>True),
				'ged_parent_id'=>array('type'=>'int', 'precision'=>4,'nullable'=>False,'default'=>'0')
			),
			'pk'=>array(),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('ged_doc_types', $old_ged_doc_types_table_def, 'ged_parent_id');		

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('ged_doc_types', 'type_ref', 'type_id');
		
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('ged_doc_types', 'type_smq_ref', 'type_ref');
		
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.005';
		return $GLOBALS['setup_info']['ged']['currentver'];			

	}	

	function ged_upgrade0_9_18_005()
	{

		$GLOBALS['phpgw_setup']->oProc->CreateTable('ged_types_places',
			array(
			'fd'=>array(
				'type_id'=>array('type'=>'varchar', 'precision'=>255,'nullable'=>False),
				'project_root' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'element_id' => array('type' => 'int','precision' => 4,'nullable' => False)
				),
			'pk'=>array(),
			'fk'=>array(),
			'ix'=>array(),
			'uc'=>array()
			)
			);

		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.006';
		return $GLOBALS['setup_info']['ged']['currentver'];			

	}	

	function ged_upgrade0_9_18_006()
	{

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows', array(
			'fd' => array(
				'flow' => array('type' => 'auto','nullable' => False),
				'app' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'flow_name' => array('type' => 'varchar', 'precision' => 252,'nullable' => False)
			),
			'pk' => array('flow'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows_roles', array(
			'fd' => array(
				'role' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'account_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'context' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array('role'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows_statuses', array(
			'fd' => array(
				'status_id' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'app' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'status_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('status_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows_transitions', array(
			'fd' => array(
				'transition' => array('type' => 'auto','nullable' => False),
				'flow' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'from_status' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'to_status' => array('type' => 'varchar', 'precision' => 100,'nullable' => False),
				'action' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'method' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'set_status')
			),
			'pk' => array('transition'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows_transitions_custom_values', array(
			'fd' => array(
				'custom_value_id' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'field_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'value' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('custom_value_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows_triggers', array(
			'fd' => array(
				'trigger_id' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '11'),
				'app' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'class' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'flow_client'),
				'method' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'context' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('trigger_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_flows_conditions', array(
			'fd' => array(
				'condition_id' => array('type' => 'auto','nullable' => False),
				'transition' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '11'),
				'app' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'class' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'flow_client'),
				'method' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'context' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('condition_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
		);
		
		// ged default flow : flow
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows` VALUES (1,'ged','default');" );
		
		// ged default flow : statuses
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('working','ged','working')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('pending_for_technical_review','ged','pending for technical review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('pending_for_quality_review','ged','pending for quality review')," );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('ready_for_delivery','ged','ready for delivery')," );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('pending_for_acceptation','ged','pending for final acceptation')," );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('current','ged','current')," );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('refused','ged','refused')," );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('obsolete','ged','obsolete')," );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_statuses` VALUES ('alert','ged','alert');" );
		
		// ged default flow : transitions
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (1,1,'working','pending_for_technical_review','submit file','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (2,1,'pending_for_technical_review','pending_for_quality_review','approve file (technical)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (3,1,'pending_for_quality_review','ready_for_delivery','approve file (quality)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (4,1,'ready_for_delivery','pending_for_acceptation','deliver file','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (5,1,'pending_for_acceptation','current','accept file (final)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (6,1,'pending_for_acceptation','refused','refuse file (final)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (7,1,'pending_for_technical_review','working','reject file (technical)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (8,1,'pending_for_quality_review','working','reject file (quality)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (9,1,'pending_for_technical_review','current','accept file (force)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (10,1,'pending_for_quality_review','current','accept file (force)','set_status_with_review')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (11,1,'current','obsolete','obsolete','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (12,1,'working','current','accept file (force)','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (13,1,'current','alert','alert','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (14,1,'alert','current','cancel alert','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (15,1,'alert','obsolete','obsolete','set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (16, 1, 'working', 'working', 'update', 'update')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (17, 1, 'current', 'current', 'update', 'update')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (18, 1, 'refused', 'refused', 'update', 'update')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (19, 1, 'working', 'locked', 'lock', 'set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (20, 1, 'locked', 'working', 'unlock', 'set_status')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions` VALUES (21, 1, 'locked', 'locked', 'update', 'update')" );

		
		// ged default flow : transitions custom values
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions_custom_values` VALUES (1,2,'review_file_type','fiche-relecture-interne')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions_custom_values` VALUES (2,3,'review_file_type','fiche-relecture-interne')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions_custom_values` VALUES (3,5,'review_file_type','fiche-relecture-externe')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions_custom_values` VALUES (4,6,'review_file_type','fiche-relecture-externe')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions_custom_values` VALUES (5,7,'review_file_type','fiche-relecture-interne')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_transitions_custom_values` VALUES (6,8,'review_file_type','fiche-relecture-interne')" );
		
		// ged default flow : triggers
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_triggers` VALUES (1,5,'ged','flow_client','apply_transition_to_previous_versions_matching_status','a:1:{s:10:\"transition\";i:11;}')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_triggers` VALUES (2,11,'ged','flow_client','apply_transition_to_linking_versions_with_link_type','a:2:{s:10:\"transition\";i:13;s:9:\"link_type\";s:10:\"dependancy\";}')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_triggers` VALUES (3,4,'ged','flow_client','apply_transition_to_linked_versions_with_link_type','a:2:{s:10:\"transition\";i:4;s:9:\"link_type\";s:8:\"delivery\";}')" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_triggers` VALUES (4,5,'ged','flow_client','apply_transition_to_previous_versions_matching_status','a:1:{s:10:\"transition\";i:15;}')" );
		
		// ged default flow : conditions

		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_conditions` (`condition_id`, `transition`, `app`, `class`, `method`, `context`) VALUES (1, 17, 'ged', 'flow_client', 'is_last_version', '')");
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_conditions` (`condition_id`, `transition`, `app`, `class`, `method`, `context`) VALUES (2, 18, 'ged', 'flow_client', 'is_last_version', '')");

		// ged default flow : admin roles
		
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (1,1,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (2,2,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (3,3,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (4,4,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (5,5,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (6,6,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (7,7,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (8,8,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (9,9,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (10,10,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (11,11,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (12,12,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (13,13,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (14,14,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (15,15,6,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (16,16,16,NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (17,17,6, NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (18,18,6, NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (19,19,6, NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (20,20,6, NULL)" );
		$GLOBALS['phpgw_setup']->oProc->query ("INSERT INTO `phpgw_flows_roles` VALUES (21,21,6, NULL)" );
		
		$GLOBALS['setup_info']['ged']['currentver']='0.9.18.007';
		return $GLOBALS['setup_info']['ged']['currentver'];			
		
	}	
	
?>
