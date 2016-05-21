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
			try
			{
				$cod_http_status = 200;
				$data_return = array();
				$vars = $this->validate_new_game();

				if (count($this->error_messages) == 0)
				{
					$mastermind = new mastermind();
					$game = $mastermind->new_game($this->post_vars["user_name"], config::$total_colors_game);

					$colors_short = array();
					if (is_array($game->colors) && count($game->colors) > 0)
					{
						foreach ($game->colors as $color) 
						{
							$colors_short[] = $color->short_name;
						}
					}

					$data_return = array(
						"colors" => $colors_short,
						"game_key" => $game->game_key,
						"code_length" => config::$total_colors_game,
						"num_guesses" => 0,
						"past_results" => array(),
						"solved" => false	
					);
				}
				else
				{
					$cod_http_status = 400;
					$data_return = $this->error_messages;
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
			}

			$this->print_return_api($cod_http_status, $data_return);
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

		private function print_return_api($cod_http_status, $data_return)
		{
			$view = array(
				"http_status_code" => $cod_http_status,
				"http_status_msg" => $this->status_http($cod_http_status),
				"data" => $data_return
			);

			require("view/return_api_view.php");
		}

		private function status_http($cod_http_status) 
	    {
	        $status_http = array(  
	            200 => 'OK',             
	            400 => 'An unhandled user exception occurred', 
	            403 => 'You don\'t have access', 
	            404 => 'Not Found', 
	            405 => 'Method Not Allowed', 
	            500 => 'Internal Server Error', 
	        ); 
	        return (array_key_exists($cod_http_status, $status_http) ? $status_http[$cod_http_status] : $status_http[500]); 
	    } 
	}
?>