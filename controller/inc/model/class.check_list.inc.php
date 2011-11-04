<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_list extends controller_model
	{
		public static $so;

		protected $id;
		protected $control_id;
		protected $status;
		protected $comment;
		protected $deadline;
		protected $check_item_array = array();
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }

		public function set_control_id($control_id)
		{
			$this->control_id = $control_id;
		}
		
		public function get_control_id() { return $this->control_id; }
		
		public function set_status($status)
		{
			$this->status = $status;
		}
		
		public function get_status() { return $this->status; }
		
		public function set_comment($comment)
		{
			$this->comment = $comment;
		}
		
		public function get_comment() { return $this->comment; }
		
		public function set_deadline($deadline)
		{
			$this->deadline = $deadline;
		}
		
		public function get_deadline() { return $this->deadline; }
		
		public function set_check_item_array($check_item_array)
		{
			$this->check_item_array = $check_item_array;
		}
		
		public function get_check_item_array() { return $this->check_item_array; }
	}
?>