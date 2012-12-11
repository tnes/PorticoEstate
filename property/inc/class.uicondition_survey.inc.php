<?php

	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.jquery');

	class property_uicondition_survey extends phpgwapi_uicommon
	{

		private $bo;
		private $receipt = array();
		public $public_functions = array
		(
			'query'				=> true,
			'index'				=> true,
			'view'				=> true,
			'add'				=> true,
			'edit'				=> true,
			'save'				=> true,
			'get_vendors'		=> true,
			'get_users'			=> true,
			'edit_survey_title'	=> true,
			'get_files'			=> true,
			'get_related'		=> true,
			'get_summation'		=> true,
			'view_file'			=> true,
			'import'			=> true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo 					= CreateObject('property.bocondition_survey');
			$this->bocommon				= & $this->bo->bocommon;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::project::condition_survey";
	//			$GLOBALS['phpgw']->css->add_external_file('logistic/templates/base/css/base.css');
		}


		public function index()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$categories = $this->_get_categories();


			$data = array(
				'datatable_name'	=> lang('condition survey'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'cat_id',
								'text' => lang('category') . ':',
								'list' => $categories,
							),
							array('type' => 'text',
								'text' => lang('search'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => lang('new'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.add')),
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => lang('download'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'export' => true, 'allrows' => true)),
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['allrows'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'allrows' => true))
							),

						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'phpgw_return_as' => 'json')),
					'editor_action' => 'property.uicondition_survey.edit_survey_title',
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'title',
							'label' => lang('title'),
							'sortable' => true,
							'editor' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:false})'
						),
						array(
							'key' => 'descr',
							'label' => lang('description'),
							'sortable' => false,
						),
						array(
							'key' => 'address',
							'label' => lang('address'),
							'sortable' => true
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'edit_survey',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'import_survey',
						'text' 			=> lang('import'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicondition_survey.import'
						)),
						'parameters'	=> json_encode($parameters)
					);

			self::render_template_xsl('datatable_common', $data);
		}


		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', 0),
				'query' => phpgw::get_var('query'),
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'cat_id' => phpgw::get_var('dir', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('allrows', 'bool')
			);

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			$export = phpgw::get_var('export');

			$values = $this->bo->read($params);

			// ... add result data
			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];


			if (!$export)
			{
				//Add action column to each row in result table
				array_walk(	$result_data['results'], array($this, '_add_links'), "property.uicondition_survey.view" );
			}
			return $this->yui_results($result_data);
		}


		public function view()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit(null, $mode = 'view');
		}

		public function add()
		{
			$this->edit();
		}

		/**
		* Prepare data for view and edit - depending on mode
		*
		* @param array  $values  populated object in case of retry
		* @param string $mode    edit or view
		* @param int    $id      entity id - no id means 'new'
		*
		* @return void
		*/

		public function edit($values = array(), $mode = 'edit')
		{
			$id 	= (int)phpgw::get_var('id');

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uicondition_survey.view', 'id'=> $id));
			}

			if($mode == 'view')
			{
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			phpgwapi_yui::tabview_setup('survey_edit_tabview');
			$tabs = array();
			$tabs['generic']	= array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';
			$tabs['documents']	= array('label' => lang('documents'), 'link' => null);
			$tabs['related']	= array('label' => lang('related'), 'link' => null);
			$tabs['summation']	= array('label' => lang('summation'), 'link' => null);
			$tabs['import']		= array('label' => lang('import'), 'link' => null);

			if ($id)
			{
				if($mode == 'edit')
				{
					$tabs['import']['link'] = '#import';
				}
				$tabs['documents']['link'] = '#documents';
				$tabs['related']['link'] = '#related';
				$tabs['summation']['link'] = '#summation';

				if (!$values)
				{
					$values = $this->bo->read_single( array('id' => $id,  'view' => $mode == 'view') );
				}
			}

			if(isset($values['location_code']) && $values['location_code'])
			{
				$values['location_data'] = execMethod('property.solocation.read_single', $values['location_code']);
			}

			$categories = $this->_get_categories($values['cat_id']);

			$bolocation	= CreateObject('property.bolocation');
			$location_data = $bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> 2,
					'required_level' => 1,
					'no_link'	=> $_no_link, // disable lookup links for location type less than type_id
					'lookup_type'	=> $mode == 'edit' ? 'form2' : 'view2',
					'tenant'	=> false,
					'lookup_entity'	=> array(),
					'entity_data'	=> isset($values['p'])?$values['p']:''
				));

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$file_def = array
			(
				array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
				array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter'),
			);


			$datatable_def = array();
			$datatable_def[] = array
			(
				'container'		=> 'datatable-container_0',
				'requestUrl'	=> json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_files', 'id' => $id,'phpgw_return_as'=>'json'))),
				'ColumnDefs'	=> json_encode($file_def),
			
			);

			$related_def = array
			(
				array('key' => 'url','label'=>lang('id'),'sortable'=>false,'resizeable'=>true),
				array('key' => 'type','label'=>lang('type'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'title','label'=>lang('title'),'sortable'=>false,'resizeable'=>true),
				array('key' => 'status','label'=>lang('status'),'sortable'=>false,'resizeable'=>true),
				array('key' => 'user','label'=>lang('user'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'entry_date','label'=>lang('entry date'),'sortable'=>false,'resizeable'=>true),
			);

			$datatable_def[] = array
			(
				'container'		=> 'datatable-container_1',
				'requestUrl'	=> json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_related', 'id' => $id,'phpgw_return_as'=>'json'))),
				'ColumnDefs'	=> json_encode($related_def)
			);

			$summation_def = array
			(
				array('key' => 'building_part','label'=>lang('building part'),'sortable'=>false,'resizeable'=>true),
				array('key' => 'category','label'=>lang('category'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'period_1','label'=>lang('year') . ':: < 1' ,'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.formatLink'),
				array('key' => 'period_2','label'=>lang('year') . ':: 1 - 5' ,'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.FormatterAmount0'),
				array('key' => 'period_3','label'=>lang('year') . ':: 6 - 10' ,'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.FormatterAmount0'),
				array('key' => 'period_4','label'=>lang('year') . ':: 11 - 15' ,'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.FormatterAmount0'),
				array('key' => 'period_5','label'=>lang('year') . ':: 16 - 20' ,'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.FormatterAmount0'),
				array('key' => 'period_6','label'=>lang('year') . ':: 21 +' ,'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.FormatterAmount0'),
				array('key' => 'sum','label'=>lang('sum'),'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.portico.FormatterAmount0'),
			);

			$datatable_def[] = array
			(
				'container'		=> 'datatable-container_2',
				'requestUrl'	=> json_encode(self::link(array('menuaction' => 'property.uicondition_survey.get_summation', 'id' => $id,'phpgw_return_as'=>'json'))),
				'ColumnDefs'	=> json_encode($summation_def)
			);

			$data = array
			(
				'datatable_def'					=> $datatable_def,
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'survey'						=> $values,
				'location_data2'					=> $location_data,
				'categories'					=> array('options' => $categories),
				'status_list'					=> array('options' => execMethod('property.bogeneric.get_list',array('type' => 'condition_survey_status', 'selected' => $values['status_id'], 'add_empty' => true))),
				'editable' 						=> $mode == 'edit',
				'tabs'							=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
				'multiple_uploader'				=> $mode == 'edit' ? true : '',
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('condition survey');

			if($mode == 'edit')
			{
				$GLOBALS['phpgw']->jqcal->add_listener('report_date');
				phpgwapi_jquery::load_widget('core');
				self::add_javascript('property', 'portico', 'condition_survey_edit.js');
				self::add_javascript('phpgwapi', 'yui3', 'yui/yui-min.js');
				self::add_javascript('phpgwapi', 'yui3', 'gallery-formvalidator/gallery-formvalidator-min.js');
				$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yui3/gallery-formvalidator/validatorCss.css');
			}

			self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

//			$GLOBALS['phpgw_info']['server']['no_jscombine'] = true;

			self::render_template_xsl(array('condition_survey'), $data);
		}


		/**
		* Saves an entry to the database for new/edit - redirects to view
		*
		* @param int  $id  entity id - no id means 'new'
		*
		* @return void
		*/

		public function save()
		{
			$id = (int)phpgw::get_var('id');

			if ($id )
			{
				$values = $this->bo->read_single( array('id' => $id,  'view' => true) );
			}
			else
			{
				$values = array();
			}

			/*
			* Overrides with incoming data from POST
			*/
			$values = $this->_populate($values);

			if( $this->receipt['error'] )
			{
				$this->edit( $values );
			}
			else
			{

				try
				{
					$id = $this->bo->save($values);
				}

				catch(Exception $e)
				{
					if ( $e )
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error'); 
						$this->edit( $values );
						return;
					}
				}

				$this->_handle_files($id);
				if($_FILES['import_file']['tmp_name'])
				{
					$this->_handle_import($id);
				}
				else
				{
					phpgwapi_cache::message_set('ok!', 'message'); 
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uicondition_survey.view', 'id' => $id));
				}
			}
		}

		/**
		* Fetch a list of files to be displayed in view/edit
		*
		* @param int  $id  entity id
		*
		* @return array $ResultSet json resultset
		*/

		public function get_files()
		{
			$id 	= phpgw::get_var('id', 'int', 'REQUEST');

			if( !$this->acl_read)
			{
				return;
			}

			$link_file_data = array
			(
				'menuaction'	=> 'property.uicondition_survey.view_file',
				'id'			=> $id
			);


			$link_view_file = self::link($link_file_data);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$files = $vfs->ls(array(
				'string' => "/property/condition_survey/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;


//------ Start pagination

			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);
			$total_records = count($files);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;			

			if($allrows)
			{
				$out = $files;
			}
			else
			{
			//	$page = ceil( ( $start / $total_records ) * ($total_records/ $num_rows) );
				$page = ceil( ( $start / $num_rows) );
				$files_part = array_chunk($files, $num_rows);
				$out = $files_part[$page];
			}

//------ End pagination


			$lang_view = lang('click to view file');
			$lang_delete = lang('click to delete file');

			$values = array();
			foreach($out as $_entry )
			{
				$values[] = array
				(
					'file_name' => "<a href='{$link_view_file}&amp;file_name={$_entry['name']}' target='_blank' title='{$lang_view}'>{$_entry['name']}</a>",
					'delete_file' => "<input type='checkbox' name='file_action[]' value='{$_entry['name']}' title='$lang_delete'>",
				);
			}							

			$data = array(
				 'ResultSet' => array(
					'totalResultsAvailable' => $total_records,
					'startIndex' => $start,
					'sortKey' => 'type', 
					'sortDir' => "ASC", 
					'Result' => $values,
					'pageSize' => $num_rows,
					'activePage' => floor($start / $num_rows) + 1
				)
			);
			return $data;

		}

		function get_summation()
		{
			$id 	= phpgw::get_var('id', 'int', 'REQUEST');

			if( !$this->acl_read)
			{
				return;
			}

			$values = $this->bo->get_summation($id);
			
			$total_records = count($values);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;			
			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);

			if($allrows)
			{
				$out = $values;
			}
			else
			{
				$page = ceil( ( $start / $total_records ) * ($total_records/ $num_rows) );
				$values_part = array_chunk($values, $num_rows);
				$out = $values_part[$page];
			}


			$data = array(
				 'ResultSet' => array(
					'totalResultsAvailable' => $total_records,
					'startIndex' => $start,
					'sortKey' => 'type', 
					'sortDir' => "ASC", 
					'Result' => $out,
					'pageSize' => $num_rows,
					'activePage' => floor($start / $num_rows) + 1
				)
			);
			return $data;
		}


		function get_related()
		{
			$id 	= phpgw::get_var('id', 'int', 'REQUEST');

			if( !$this->acl_read)
			{
				return;
			}
		
			$interlink 	= CreateObject('property.interlink');
			$target = $interlink->get_relation('property', $this->acl_location, $id, 'target');


			$values = array();
			if($target)
			{
				foreach($target as $_target_section)
				{
					foreach ($_target_section['data'] as $_target_entry)
					{
						$values[] = array
						(
							'url'		=> "<a href=\"{$_target_entry['link']}\" > {$_target_entry['id']}</a>",
							'type'		=> $_target_section['descr'],
							'title'		=> $_target_entry['title'],
							'status'	=> $_target_entry['statustext'],
							'user'		=> $GLOBALS['phpgw']->accounts->get($_target_entry['account_id'])->__toString(),
							'entry_date'=> $GLOBALS['phpgw']->common->show_date($_target_entry['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						);
					}
				}
			}

//------ Start pagination

			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);

			$total_records = count($values);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;			

			if($allrows)
			{
				$out = $values;
			}
			else
			{
				$page = ceil( ( $start / $total_records ) * ($total_records/ $num_rows) );
				$values_part = array_chunk($values, $num_rows);
				$out = $values_part[$page];
			}

//------ End pagination


			$data = array(
				 'ResultSet' => array(
					'totalResultsAvailable' => $total_records,
					'startIndex' => $start,
					'sortKey' => 'type', 
					'sortDir' => "ASC", 
					'Result' => $out,
					'pageSize' => $num_rows,
					'activePage' => floor($start / $num_rows) + 1
				)
			);
			return $data;
		}




		/**
		* Dowloads a single file to the browser
		*
		* @param int  $id  entity id
		*
		* @return file
		*/

		function view_file()
		{
			if(!$this->acl_read)
			{
				return lang('no access');
			}

			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('condition_survey');
		}


		/**
		* Store and / or delete files related to an entity
		*
		* @param int  $id  entity id
		*
		* @return void
		*/
		private function _handle_files($id)
		{
			$id = (int)$id;
			if(!$id)
			{
				throw new Exception('uicondition_survey::_handle_files() - missing id');
			}
			$bofiles	= CreateObject('property.bofiles');

			if(isset($_POST['file_action']) && is_array($_POST['file_action']))
			{
				$bofiles->delete_file("/condition_survey/{$id}/", array('file_action' => $_POST['file_action']));
			}
			$file_name=str_replace(' ','_',$_FILES['file']['name']);

			if($file_name)
			{
				$to_file = $bofiles->fakebase . '/condition_survey/' . $id . '/' . $file_name;
				if($bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => Array(RELATIVE_NONE)
				)))
				{
					phpgwapi_cache::message_set(lang('This file already exists !'), 'error'); 
				}
				else
				{
					$bofiles->create_document_dir("condition_survey/{$id}");
					$bofiles->vfs->override_acl = 1;

					if(!$bofiles->vfs->cp (array (
						'from'	=> $_FILES['file']['tmp_name'],
						'to'	=> $to_file,
						'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
					{
						phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error'); 
					}
					$bofiles->vfs->override_acl = 0;
				}
			}
		}



		public function import()
		{
			$id = phpgw::get_var('id', 'int', 'REQUEST');
			$this->_handle_import($id);
		}

		/**
		* Import deviations found in the survey to the database from a spreadsheet
		*
		* @param int  $id  entity id
		*
		* @return void
		*/
		private function _handle_import($id)
		{
			$id = (int)$id;
			if(!$id)
			{
				throw new Exception('uicondition_survey::_handle_import() - missing id');
			}

			$step			= phpgw::get_var('step', 'int', 'REQUEST');
			$sheet_id		= phpgw::get_var('sheet_id', 'int', 'REQUEST');
			
			$sheet_id = $sheet_id ? $sheet_id : phpgw::get_var('selected_sheet_id', 'int', 'REQUEST');

			if($start_line	= phpgw::get_var('start_line', 'int', 'REQUEST'))
			{
				phpgwapi_cache::system_set('property', 'import_sheet_start_line', $start_line);
			}
			else
			{
				$start_line = phpgwapi_cache::system_get('property', 'import_sheet_start_line');
				$start_line = $start_line  ? $start_line : 1;
			}


			if($columns = phpgw::get_var('columns'))
			{
				phpgwapi_cache::system_set('property', 'import_sheet_columns', $columns);
			}
			else
			{
				$columns = phpgwapi_cache::system_get('property', 'import_sheet_columns');
				$columns = $columns && is_array($columns) ? $columns : array();
			}


			if(!$cached_file = phpgwapi_cache::session_get('property', 'condition_survey_import_file'))
			{
				$file = $_FILES['import_file']['tmp_name'];
				$cached_file ="{$file}_temporary_import_file";
				// save a copy to survive multiple steps
				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'condition_survey_import_file',$cached_file);
				$step = 1;
			}

			$tabs = array();
			
			switch ($step)
			{
				case 0:
					$active_tab = 'step_1';
					$lang_submit = lang('continue');
					$tabs['step_1']	= array('label' => lang('choose file'), 'link' => '#step_1');
					$tabs['step_2']	= array('label' => lang('choose sheet'), 'link' => null);
					$tabs['step_3']	= array('label' => lang('choose start line'), 'link' => null);
					$tabs['step_4']	= array('label' => lang('choose columns'), 'link' => null);
					break;
				case 1:
					$active_tab = 'step_2';
					$lang_submit = lang('continue');
					$tabs['step_1']	= array('label' => lang('choose file'), 'link' => self::link(array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 0, 'sheet_id' => $sheet_id, 'start_line' => $start_line )));
					$tabs['step_2']	= array('label' => lang('choose sheet'), 'link' =>  '#step_2');
					$tabs['step_3']	= array('label' => lang('choose start line'), 'link' => null);
					$tabs['step_4']	= array('label' => lang('choose columns'), 'link' => null);
					break;
				case 2:
					$active_tab = 'step_3';
					$lang_submit = lang('continue');
					$tabs['step_1']	= array('label' => lang('choose file'), 'link' => self::link(array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 0, 'sheet_id' => $sheet_id, 'start_line' => $start_line )));
					$tabs['step_2']	= array('label' => lang('choose sheet'), 'link' => self::link(array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 1, 'sheet_id' => $sheet_id, 'start_line' => $start_line )));
					$tabs['step_3']	= array('label' => lang('choose start line'), 'link' => '#step_3');
					$tabs['step_4']	= array('label' => lang('choose columns'), 'link' => null);
					break;
				case 3:
					$active_tab = 'step_4';
					$lang_submit = lang('import');
					$tabs['step_1']	= array('label' => lang('choose file'), 'link' => self::link(array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 0, 'sheet_id' => $sheet_id, 'start_line' => $start_line )));
					$tabs['step_2']	= array('label' => lang('choose sheet'), 'link' => self::link(array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 1, 'sheet_id' => $sheet_id, 'start_line' => $start_line )));
					$tabs['step_3']	= array('label' => lang('choose start line'), 'link' => self::link(array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 2, 'sheet_id' => $sheet_id, 'start_line' => $start_line )));
					$tabs['step_4']	= array('label' => lang('choose columns'), 'link' =>  '#step_4');
					break;
/*
				case 4://temporary
					phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
					unlink($cached_file);
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'property.uicondition_survey.import', 'id' =>$id, 'step' => 0));
					break;
*/
			}
			
//-----------

			$data = array();
			if(!$step )
			{
				phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
				unlink($cached_file);
			}
			else if ($cached_file)
			{
				phpgw::import_class('phpgwapi.phpexcel');

				try
				{
					$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
					$AllSheets = $objPHPExcel->getSheetNames();
			
					$sheets = array();
					if($AllSheets)
					{
						foreach ($AllSheets as $key => $sheet)
						$sheets[] = array
						(
							'id'	=> $key,
							'name'	=> $sheet,
							'selected' => $sheet_id == $key
						);
					}

					$objPHPExcel->setActiveSheetIndex((int)$sheet_id);
					$data = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				}
				catch(Exception $e)
				{
					if ( $e )
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error'); 
						phpgwapi_cache::session_clear('property', 'condition_survey_import_file');
						unlink($cached_file);
					}
				}
			}

			$survey = $this->bo->read_single( array('id' => $id,  'view' => $mode == 'view') );

			$html_table = '<table border="1">';
			if($data && $step == 2)
			{
				$i = 0;
				$html_table .= "<tr><th align = 'center'>". lang('start'). "</th><th align='center'>" . implode("</th><th align='center'>", array_keys($data[1])) . '</th></tr>';
				foreach($data as $row_key => $row)
				{
					if($i>20)
					{
						break;
					}

					$_checked = '';
					if($start_line == $row_key)
					{
						$_checked = 'checked="checked"';
					}

					$_radio = "[{$row_key}]<input id=\"start_line\" type =\"radio\" {$_checked} name=\"start_line\" value=\"{$row_key}\">";

					$html_table .= "<tr><td><pre>{$_radio}</pre></td><td>" . implode('</td><td>', array_values($row)) . '</td></tr>';
					$i++;
				}
			}			
			else if($data && $step == 3)
			{
				$_options = array
				(
					'_skip_import_'			=> 'Utelates fra import/implisitt',
					'building_part'			=> 'bygningsdels kode',
					'descr'					=> 'Tilstandbeskrivelse',
					'title'					=> 'Tiltak',
					'hjemmel'				=> 'Hjemmel/ krav',
					'condition_degree'		=> 'Tilstandsgrad',
					'condition_type'		=> 'Konsekvenstype',
					'consequence'			=> 'Konsekvensgrad',
					'probability'			=> 'Sannsynlighet',
					'due_year'				=> 'År (innen)',
					'percentage_investment'	=> 'Andel % aktivering (U)',
					'amount'				=> 'Kalkyle netto totalt (VU)',
				);

				phpgw::import_class('phpgwapi.sbox');

				foreach($data[$start_line] as $_column => $_value)
				{
					$selected = isset($columns[$_column]) && $columns[$_column] ? $columns[$_column] : '';
					
					$_listbox = phpgwapi_sbox::getArrayItem("columns[{$_column}]", $selected, $_options, true );
					$html_table .= "<tr><td>[{$_column}] {$_value}</td><td>{$_listbox}</td><tr>";
				}
			}
			else if($data && $step == 4)
			{
//				_debug_array($columns);

				$rows = count($data)+1;

				$import_data = array();

				for ($i=$start_line; $i<$rows; $i++ )
				{
					$_result = array();

					foreach ($columns as $_row_key => $_value_key)
					{
						if($_value_key != '_skip_import_')
						{
							$_result[$_value_key] =trim($data[$i][$_row_key]);
						}
					
					}
					$import_data[] = $_result;
				}
				if($import_data)
				{
					try
					{
						$this->bo->import($survey, $import_data);
					}
					catch(Exception $e)
					{
						if ( $e )
						{
							phpgwapi_cache::message_set($e->getMessage(), 'error'); 
						}
					}
				}

//				$msg = "'{$cached_file}' contained " . count($import_data) . " lines";
//				phpgwapi_cache::message_set($msg, 'message'); 

//	_debug_array($import_data);die();
			}



			$html_table .= '</table>';




			if(isset($survey['location_code']) && $survey['location_code'])
			{
				$survey['location_data'] = execMethod('property.solocation.read_single', $survey['location_code']);
			}

			$bolocation	= CreateObject('property.bolocation');
			$location_data = $bolocation->initiate_ui_location(array
				(
					'values'	=> $survey['location_data'],
					'type_id'	=> 2,
					'lookup_type'	=> 'view2',
					'tenant'	=> false,
					'lookup_entity'	=> array(),
					'entity_data'	=> isset($survey['p'])?$survey['p']:''
				));

			$data = array
			(
				'lang_submit'					=> $lang_submit,
				'survey'						=> $survey,
				'location_data2'				=> $location_data,
				'step'							=> $step +1,
				'sheet_id'						=> $sheet_id,
				'start_line'					=> $start_line,
				'html_table'					=> $html_table,
				'sheets'						=> array('options' => $sheets),
				'tabs'							=>$GLOBALS['phpgw']->common->create_tabs($tabs, $active_tab),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('condition survey import');

			self::render_template_xsl(array('condition_survey_import'), $data);


		}


		/**
		* Gets user candidates to be used as coordinator - called as ajax from edit form
		*
		* @param string  $query
		*
		* @return array 
		*/

		public function get_users()
		{
			if(!$this->acl_read)
			{
				return;
			}

			$query = phpgw::get_var('query');

			$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts', $start, $sort, $order, $query,$offset);

			$values = array();
			foreach($accounts as $account)
			{
				if ($account->enabled)
				{
					$values[] = array
					(
						'id'	=> $account->id,
						'name'	=> $account->__toString(),
					);
				}
			}
			return array('ResultSet'=> array('Result'=>$values));
		}

		/**
		* Gets vendor canidated to be used as vendor - called as ajax from edit form
		*
		* @param string  $query
		*
		* @return array 
		*/

		public function get_vendors()
		{
			if(!$this->acl_read)
			{
				return;
			}

			$query = phpgw::get_var('query');

			$sogeneric = CreateObject('property.sogeneric', 'vendor');
			$values = $sogeneric->read(array('query' => $query));
			foreach ($values as &$entry)
			{
				$entry['name'] = $entry['org_name'];
			}
			return array('ResultSet'=> array('Result'=>$values));
		}

		/**
		* Edit title fo entity directly from table
		*
		* @param int  $id  id of entity
		* @param string  $value new title of entity
		*
		* @return string text to appear in ui as receipt on action
		*/

		public function edit_survey_title()
		{
			$id = phpgw::get_var('id', 'int', 'GET');

			if(!$this->acl_edit)
			{
				return lang('no access');
			}
			
			if ($id )
			{
				$values = $this->bo->read_single( array('id' => $id,  'view' => true) );
				$values['title'] = phpgw::get_var('value');

				try
				{
					$this->bo->edit_title($values);
				}

				catch(Exception $e)
				{
					if ( $e )
					{
						return $e->getMessage(); 
					}
				}
				return 'OK';
			}
		}

		/*
		* Overrides with incoming data from POST
		*/
		private function _populate($data = array())
		{
			$insert_record = phpgwapi_cache::session_get('property', 'insert_record');

			$values	= phpgw::get_var('values');

			$_fields = array
			(
				array
				(
					'name' => 'title',
					'type'	=> 'string',
					'required'	=> true
				),
				array
				(
					'name' => 'descr',
					'type'	=> 'string',
					'required'	=> true
				),
				array
				(
					'name' => 'cat_id',
					'type'	=> 'integer',
					'required'	=> true
				),
				array
				(
					'name' => 'report_date',
					'type'	=> 'string',
					'required'	=> true
				),
				array
				(
					'name' => 'status_id',
					'type'	=> 'integer',
					'required'	=> true
				),
				array
				(
					'name' => 'vendor_id',
					'type'	=> 'integer',
					'required'	=> false
				),
				array
				(
					'name' => 'vendor_name',
					'type'	=> 'string',
					'required'	=> false
				),
				array
				(
					'name' => 'coordinator_id',
					'type'	=> 'integer',
					'required'	=> false
				),
				array
				(
					'name' => 'coordinator_name',
					'type'	=> 'string',
					'required'	=> false
				),
			);


			foreach ($_fields as $_field)
			{
				if($data[$_field['name']] = $_POST['values'][$_field['name']])
				{
					$data[$_field['name']] =  phpgw::clean_value($data[$_field['name']], $_field['type']);
				}
				if($_field['required'] && !$data[$_field['name']])
				{
					$this->receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $_field['name']));
				}
			}

//_debug_array($data);die();

			$values = $this->bocommon->collect_locationdata($data,$insert_record);

			if(!isset($values['location_code']) || ! $values['location_code'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please select a location !'));
			}

			/*
			* Extra data from custom fields
			*/
			$values['attributes']	= phpgw::get_var('values_attribute');

			if(is_array($values['attributes']))
			{
				foreach ($values['attributes'] as $attribute )
				{
					if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
					{
						$this->receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
					}
				}
			}

			if(!isset($values['cat_id']) || !$values['cat_id'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please select a category !'));
			}

			if(!isset($values['title']) || !$values['title'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please give a title !'));
			}

			if(!isset($values['report_date']) || !$values['report_date'])
			{
				$this->receipt['error'][]=array('msg'=>lang('Please select a date!'));
			}

			return $values;
		}

		private function _get_categories($selected = 0)
		{
			$cats	= CreateObject('phpgwapi.categories', -1, 'property', $this->acl_location);
			$cats->supress_info	= true;
			$categories = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $selected,'globals' => true,'use_acl' => $this->_category_acl));
			$default_value = array ('cat_id'=>'','name'=> lang('no category'));
			array_unshift ($categories['cat_list'],$default_value);

			foreach ($categories['cat_list'] as & $_category)
			{
				$_category['id'] = $_category['cat_id'];
			}

			return $categories['cat_list'];
		}
	}
