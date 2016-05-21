<?php
	/**
	 * Class tha contains the start of actions 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-20
	 * @access public
	 */
	class mastermind_controller
	{
		/**
		 * Stores error messages
		 * @access private
		 * @var array
		 */
		private $error_messages;

		/**
		 * Stores HTML POST
		 * @access private
		 * @var array 
		 */
		private $post_vars;

		/** 
		 * Construct - initialize error_messages and post_vars variables 
		 * @access public
		 */
		function __construct()
		{
			$this->error_messages = array();
			$this->post_vars = json_decode(file_get_contents("php://input"), true);
		}

		/** 
		 * Start a new game
		 * @access public
		 */
		public function new_game()
		{
			$vars = $this->validate_new_game();

			if (count($this->error_messages) == 0)
			{
				$mastermind = new mastermind();
				$mastermind->new_game($this->post_vars["user_name"]);
			}

			echo("<pre>");
			print_r($vars);
			echo("</pre>");

			echo("<pre>");
			print_r($this->error_messages);
			echo("</pre>");
		}

		/** 
		 * Validate variables that use in new_game method
		 * @access public
		 * @return array
		 */
		private function validate_new_game()
		{
			$return = array("user_name" => "");

			if (isset($this->post_vars["user_name"]) && !empty(trim($this->post_vars["user_name"])))
				$return = array("user_name" => trim($this->post_vars["user_name"]));
			else
				$this->error_messages[] = "Variable 'user_name' is empty";

			return $return;
		}
	}
?>