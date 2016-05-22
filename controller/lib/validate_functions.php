<?php
	/**
	 * Class to validade post vars of API methods
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-22
	 * @access public
	 */
	class validate 
	{
		/** 
		 * Validate variables that use in new_game method
		 * @access public
		 * @var $error_messages array
		 * @var $post_vars array
		 * @return array
		 */
		public static function validate_new_game(&$error_messages, $post_vars)
		{
			try
			{
				$return = array("user_name" => "");

				if (isset($post_vars["user_name"]) && !empty(trim($post_vars["user_name"])))
					$return = array("user_name" => trim($post_vars["user_name"]));
				else
					$error_messages[] = "Variable 'user_name' is empty";

				return $return;
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/** 
		 * Validate variables that use in validate_guess method
		 * @access public
 		 * @var $error_messages array
		 * @var $post_vars array
		 * @return array
		 */
		public static function validate_guess(&$error_messages, $post_vars)
		{
			try
			{
				$return = array("game_key" => "", "user_name" => "", "colors" => array());

				if (isset($post_vars["game_key"]) && !empty(trim($post_vars["game_key"])))
					$return["game_key"] = trim($post_vars["game_key"]);
				else
					$error_messages[] = "Variable 'game_key' is empty";

				if (isset($post_vars["user_name"]) && !empty(trim($post_vars["user_name"])))
					$return["user_name"] = trim($post_vars["user_name"]);
				else
					$error_messages[] = "Variable 'user_name' is empty";

				if (isset($post_vars["colors"]) && is_array($post_vars["colors"]))
				{ 
					if (count($post_vars["colors"]) == config::$total_colors_game)
						$return["colors"] = $post_vars["colors"];
					else
						$error_messages[] = "The number of records in variable 'colors' is invalid. Must be ".config::$total_colors_game;
				}
				else
				{
					$error_messages[] = "Variable 'colors' is invalid";
				}

				return $return;
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}

		/** 
		 * Validate variables that use in multiplayer method
		 * @access public
 		 * @var $error_messages array
		 * @var $post_vars array		 
		 * @return array
		 */
		public static function validate_multiplayer(&$error_messages, $post_vars)
		{
			try
			{
				$return = array("user_name" => "", "game_key" => "");

				if (isset($post_vars["user_name"]) && !empty(trim($post_vars["user_name"])))
					$return = array("user_name" => trim($post_vars["user_name"]));
				else
					$error_messages[] = "Variable 'user_name' is empty";

				if (isset($post_vars["game_key"]) && !empty(trim($post_vars["game_key"])))
					$return["game_key"] = trim($post_vars["game_key"]);
				else
					$error_messages[] = "Variable 'game_key' is empty";

				return $return;
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}
	}
?>