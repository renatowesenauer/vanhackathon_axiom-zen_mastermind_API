<?php
	require_once("user.php");
	require_once("color.php");

	/**
	 * Struct of Game object
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-20
	 * @access public
	 */
	class game
	{
		/** 
		 * Stores id game
		 * @access public
		 * @var int 
		 */
		public $id;

		/** 
		 * Stores game key
		 * @access public
		 * @var string 
		 */
		public $game_key;

		/** 
		 * Stores creation date
		 * @access public
		 * @var string 
		 */
		public $creation_date;

		/** 
		 * Stores the combination of colors
		 * @access public
		 * @var array 
		 */
		public $colors;

		/** 
		 * Stores users of game
		 * @access public
		 * @var array 
		 */
		public $users;

		function __construct()
		{
			$this->colors = array();
			$this->users = array();
		}
	}
?>