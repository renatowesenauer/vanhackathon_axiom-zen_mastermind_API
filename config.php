<?php
	/**
	 * Class of API configuration settings 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-21
	 * @access public
	 */
	class config 
	{
		/** 
		 * Stores the default time zone of API
		 * @access public
		 * @var string 
		 */
		public static $default_time_zone = "America/Sao_Paulo";

		/** 
		 * Stores the total of colors use in game
		 * @access public
		 * @var int 
		 */
		public static $total_colors_game = 8;
		
		/** 
		 * Stores the data base connection variables
		 * @access public
		 * @var array 
		 */
		public static $connection_db = array(
				"host" => "localhost",
				"user" => "root",
				"password" => "",
				"database_name" => "mastermind_db"
			);
	}
?>