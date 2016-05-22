<?php
	/**
	 * Class of API returns 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-22
	 * @access public
	 */
	class return_api
	{
		/**
		 * Default function to print results of API
		 * @access public
		 * @var $cod_http_status int
		 * @var $data_return array
		 * @var $status string
		 */
		public static function print_return_api($cod_http_status, $data_return, $status = "success")
		{
			try
			{
				if ($status == "") $status = "success";

				$view = array(
					"http_status_code" => $cod_http_status,
					"http_status_msg" => self::status_http($cod_http_status),
					"data" => array("status" => $status, "data" => $data_return)
				);

				require("view/return_api_view.php");
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/**
		 * Format return HTTP 
		 * @access private
		 * @var $cod_http_status int
		 * @return string 
		 */
		public static function status_http($cod_http_status) 
	    {
	    	try
	    	{
		        $status_http = array(  
		            200 => 'OK',             
		            400 => 'An unhandled user exception occurred', 
		            403 => 'You don\'t have access', 
		            404 => 'Not Found', 
		            405 => 'Method Not Allowed', 
		            500 => 'Internal Server Error'
		        ); 
		        return (array_key_exists($cod_http_status, $status_http) ? $status_http[$cod_http_status] : $status_http[500]); 
	        }
			catch(Exception $e)
			{
				throw new Exception($e);
			}
	    } 

	    /**
		 * Default function to print game results
		 * @access public
		 * @var $id_game int
		 * @var $vars_colors array
		 * @return array
		 */
		public static function print_game_info($id_game, $vars_colors = array())
		{
			try
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
						"num_guesses" => count($game->guesses),
						"past_results" => $guesses,
						"result" => $result,
						"status" => array(
								"solved" => $game->solved,
								"solved_by_user" => $game->user_solved->name,
								"user_started_in_game" => $game->user_solved->game_entry_date,
								"solution_date" => $game->solution_date,
								"time" => general::format_date_interval_info($game->user_solved->game_entry_date, $game->solution_date)
							)
					);

				return $data_return;
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}
	}
?>