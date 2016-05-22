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
				$status_return = "";
				$error_messages = array();

				/* validate parameters */
				$vars = validate::validate_new_game($error_messages, $this->post_vars);

				if (count($error_messages) == 0)
				{
					/* start new game */
					$mastermind = new mastermind();
					$game = $mastermind->new_game($vars["user_name"], config::$total_colors_game);

					$colors_short = array();
					if (is_array($game->colors) && count($game->colors) > 0)
					{
						foreach ($game->colors as $color) 
						{
							$colors_short[] = $color->short_name;
						}
					}

					/* return game data */
					$data_return = array(
						"code_length" => config::$total_colors_game,
						"colors" => $colors_short,
						"game_key" => $game->game_key,
						"game_users" => array($vars["user_name"]),
						"guess" => array(),
						"num_guesses" => 0,
						"past_results" => array(),
						"result" => array(),
						"solved" => false	
					);
				}
				else
				{
					$data_return = $error_messages;
					$status_return = "error";
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			return_api::print_return_api($cod_http_status, $data_return, $status_return);
		}

		/**
		 * Verify a new guess of game
		 * @access public
		 */
	    public function guess()
	    {
	    	try
			{
				$cod_http_status = 200;
				$data_return = array();
				$status_return = "";
				$error_messages = array();

				$vars = validate::validate_guess($error_messages, $this->post_vars);

				if (count($error_messages) == 0)
				{
					$mastermind = new mastermind();
					$game = $mastermind->consult_game_key($vars["game_key"]);

					if ($game->id > 0)
					{
						$user_valid = $mastermind->validate_user_game($game->id, $vars["user_name"]);

						if ($user_valid)
						{
							if (!$game->solved)
							{
								$exact = general::verify_color_exatc($vars["colors"], $game->colors);
								$near = general::verify_color_near($vars["colors"], $game->colors, $exact);

								$solved = (count($exact) == config::$total_colors_game ? true : false);

								$mastermind->save_guess($game->id, $vars["user_name"], $vars["colors"], count($exact), count($near), $solved);
							}

							$data_return = return_api::print_game_info($game->id, $vars["colors"]);
						}
						else
						{
							$data_return = array("User invalid to this game");
							$status_return = "error";
						}
					}
					else
					{
						$data_return = array("Game_key not found");
						$status_return = "error";
					}
				}
				else
				{
					$data_return = $error_messages;
					$status_return = "error";
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			return_api::print_return_api($cod_http_status, $data_return, $status_return);
	    }

	    /**
		 * Add a user in multiplayer game
		 * @access public
		 */
		public function multiplayer()
	    {
	    	try
			{
				$cod_http_status = 200;
				$data_return = array();
				$status_return = "";
				$error_messages = array();

				$vars = validate::validate_multiplayer($error_messages, $this->post_vars);

				if (count($error_messages) == 0)
				{
					$mastermind = new mastermind();
					$game = $mastermind->consult_game_key($vars["game_key"]);

					if ($game->id > 0)
					{
						if (!$game->solved)
						{
							$mastermind->add_user_multiplayer($game->id, $vars["user_name"]);
						}

						$data_return = return_api::print_game_info($game->id);
					}
					else
					{
						$data_return = array("Game_key not found");
						$status_return = "error";
					}
				}
				else
				{
					$data_return = $error_messages;
					$status_return = "error";
				}
			}
			catch (Exception $e)
			{
				$cod_http_status = 500;
				$status_return = "error";
			}

			return_api::print_return_api($cod_http_status, $data_return, $status_return);
	    }	    
	}
?>