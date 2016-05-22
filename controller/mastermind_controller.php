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
						"code_length" => config::$total_colors_game,
						"colors" => $colors_short,
						"game_key" => $game->game_key,
						"guess" => array(),
						"num_guesses" => 0,
						"past_results" => array(),
						"result" => array(),
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

	    public function guess()
	    {
	    	try
			{
				$cod_http_status = 200;
				$data_return = array();
				$vars = $this->validate_guess();

				if (count($this->error_messages) == 0)
				{
					$mastermind = new mastermind();
					$game = $mastermind->consult_game_key($vars["game_key"]);

					if ($game->id > 0)
					{
						if (!$game->solved)
						{
							$exact = $this->verify_color_exatc($vars["colors"], $game->colors);
							$near = $this->verify_color_near($vars["colors"], $game->colors, $exact);

							$solved = (count($exact) == config::$total_colors_game ? true : false);

							$mastermind->save_guess($game->id, $vars["user_name"], $vars["colors"], count($exact), count($near), $solved);
						}

						$data_return = $this->print_game_info($game->id, $vars["colors"]);
					}
					else
					{
						$cod_http_status = 200;
						$data_return = array("Game_key not found");
					}
				}
				else
				{
					$cod_http_status = 200;
					$data_return = $this->error_messages;
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				print_r($e);
				die();
			}

			$this->print_return_api($cod_http_status, $data_return);
	    }

	    private function verify_color_exatc($colors_user, $object_colors_game)
	    {
	    	$exact = array();
	    	foreach ($colors_user as $index_color => $color_shor_name) 
			{
				foreach ($object_colors_game as $color) 
				{
					$color1 = trim(strtoupper($color->short_name));
					$color2 = trim(strtoupper($color_shor_name));

					if ($color1 == $color2 && $color->order == ($index_color + 1))
					{
						$exact[] = ($index_color + 1);
						break;
					}
				}
			}
			return $exact;
	    }

	    private function verify_color_near($colors_user, $object_colors_game, $exact)
	    {
	    	$near = array();
	    	foreach ($colors_user as $index_color => $color_shor_name) 
			{
				foreach ($object_colors_game as $color) 
				{
					$color1 = trim(strtoupper($color->short_name));
					$color2 = trim(strtoupper($color_shor_name));

					if ($color1 == $color2)
					{
						if (!in_array($color->order, $exact) && !in_array($color->order, $near))
						{
							$near[] = $color->order;
							break;
						}
					}
				}
			}
			return $near;
	    }

	    /** 
		 * Validate variables that use in new_game method
		 * @access public
		 * @return array
		 */
		private function validate_guess()
		{
			$return = array("game_key" => "", "user_name" => "", "colors" => array());

			if (isset($this->post_vars["game_key"]) && !empty(trim($this->post_vars["game_key"])))
				$return["game_key"] = trim($this->post_vars["game_key"]);
			else
				$this->error_messages[] = "Variable 'game_key' is empty";

			if (isset($this->post_vars["user_name"]) && !empty(trim($this->post_vars["user_name"])))
				$return["user_name"] = trim($this->post_vars["user_name"]);
			else
				$this->error_messages[] = "Variable 'user_name' is empty";

			if (isset($this->post_vars["colors"]) && is_array($this->post_vars["colors"]))
			{ 
				if (count($this->post_vars["colors"]) == config::$total_colors_game)
					$return["colors"] = $this->post_vars["colors"];
				else
					$this->error_messages[] = "The number of records in variable 'colors' is invalid. Must be ".config::$total_colors_game;
			}
			else
			{
				$this->error_messages[] = "Variable 'colors' is invalid";
			}

			return $return;
		}

		public function multiplayer()
	    {
	    	try
			{
				$cod_http_status = 200;
				$data_return = array();
				$vars = $this->validate_multiplayer();

				if (count($this->error_messages) == 0)
				{
					$mastermind = new mastermind();
					$game = $mastermind->consult_game_key($vars["game_key"]);

					if ($game->id > 0)
					{
						if (!$game->solved)
						{
							$mastermind->add_user_multiplayer($game->id, $vars["user_name"]);
						}

						$data_return = $this->print_game_info($game->id);
					}
					else
					{
						$cod_http_status = 200;
						$data_return = array("Game_key not found");
					}
				}
				else
				{
					$cod_http_status = 200;
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
		 * Validate variables that use in multiplayer method
		 * @access public
		 * @return array
		 */
		private function validate_multiplayer()
		{
			$return = array("user_name" => "", "game_key" => "");

			if (isset($this->post_vars["user_name"]) && !empty(trim($this->post_vars["user_name"])))
				$return = array("user_name" => trim($this->post_vars["user_name"]));
			else
				$this->error_messages[] = "Variable 'user_name' is empty";

			if (isset($this->post_vars["game_key"]) && !empty(trim($this->post_vars["game_key"])))
				$return["game_key"] = trim($this->post_vars["game_key"]);
			else
				$this->error_messages[] = "Variable 'game_key' is empty";

			return $return;
		}

		private function print_game_info($id_game, $vars_colors = array())
		{
			$mastermind = new mastermind();
			$game = $mastermind->consult_game_results($id_game);

			$game_colors = array();
			foreach ($game->colors as $color) 
			{
				$game_colors[] = $color->short_name;
			}

			$game_users = array();
			foreach ($game->users as $user) 
			{
				$game_users[] = $user->name;
			}

			$exact = 0;
			$near = 0;
			$guesses = array();
			$result = array();
			if (is_array($game->guesses) && count($game->guesses) > 0)
			{
				foreach ($game->guesses as $guess) 
				{
					$guess_colors = array();
					foreach ($guess->colors as $color) 
					{
						$guess_colors[] = $color->short_name;
					}

					$guesses[] = array(
							"user_name" => $guess->user->name,
							"colors" => $guess_colors,
							"exact" => $guess->exact,
							"near" => $guess->near,
							"creation_date" => $guess->creation_date
						);
				}

				$last_guess = $game->guesses[count($game->guesses) - 1];
				$result = array(
						"exact" => $last_guess->exact,
						"near" => $last_guess->near
					);
			}

			$data_return = array(
					"code_length" => config::$total_colors_game,
					"colors" => $game_colors,
					"game_key" => $game->game_key,
					"game_users" => $game_users,
					"guess" => $vars_colors,
					"num_guesses" => count($game->users[0]->user_guesses),
					"past_results" => $guesses,
					"result" => $result,
					"solved" => $game->solved
				);

			return $data_return;
		}
	}
?>