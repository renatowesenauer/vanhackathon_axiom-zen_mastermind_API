<?php
	/**
	 * Struct of guess object
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-20
	 * @access public
	 */
	class guess
	{
		/** 
		 * Stores id game
		 * @access public
		 * @var int 
		 */
		public $id;

		/** 
		 * Stores a count of exact colors
		 * @access public
		 * @var int 
		 */
		public $exact;

		/** 
		 * Stores a count of near colors
		 * @access public
		 * @var int 
		 */
		public $near;

		/** 
		 * Stores creation date
		 * @access public
		 * @var string 
		 */
		public $creation_date;
	}
?>