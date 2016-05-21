<?php
	/**
	 * Class of connection with data base 
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-20
	 * @access public
	 */
	class connect_db 
	{
		protected $host;
		protected $user;
		protected $password;
		protected $db;
		protected $connect;

		public function getConnect() { return $this->connect; }

		/**
		 * Construct - Start variables of connection
		 * @access public
		 * @return void
		 */
		function __construct() 
		{
			$this->host = "localhost";
			$this->user = "root";
			$this->password = "";
			$this->db = "mastermind_db";
		}

		/**
		 * Open connection with data base
		 * @access public
		 * @return void
		 */
		public function open() 
		{
			$this->connect = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db, $this->user, $this->password);
		}

		/**
		 * Unset variable connection
		 * @access public
		 * @return void
		 */
		public function close() 
		{
			$this->connect = null;
		}
	}