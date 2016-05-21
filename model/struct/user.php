<?php
	require_once("guess.php");

	/**
	 * Struct of Game object
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-20
	 * @access public
	 */ 
	class user
	{
		/** 
		 * Stores id game
		 * @access public
		 * @var int 
		 */
		public $id;

		/** 
		 * Stores user name
		 * @access public
		 * @var int 
		 */
		public $name;

		/** 
		 * Stores creation date
		 * @access public
		 * @var string 
		 */
		public $creation_date;

		/** 
		 * Stores guesses of user
		 * @access public
		 * @var array 
		 */
		public $user_guesses;

		function __construct()
		{
			$this->$user_guesses = array();
		}
	}
?>