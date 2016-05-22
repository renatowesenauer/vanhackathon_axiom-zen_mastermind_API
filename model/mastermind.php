<?php
	require_once("connect_db.php");
	require_once("struct/game.php");

	/**
	 * Class of mastermind model
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-20
	 * @access public
	 */
	class mastermind 
	{
		/**
		 * Starts a new game
		 * @access public
		 * @var $user_name string
		 * @var $total_colors_game
		 * @return game
		 */ 
		public function new_game($user_name, $total_colors_game)
		{
			$game = new game();
			try
			{
				$connect = new connect_db();
				$connect->open();

				$date_insert = date("Y-m-d H:i:s");

				$user = $this->refer_game_user($connect, $user_name, $date_insert);
				$game_key = md5($user->id).md5($date_insert);

				try 
				{  
					$connect->getConnect()->beginTransaction();

					$strSQL = "INSERT INTO tb_game(game_key, solved, dt_created) VALUES(:game_key, 'N', :date_insert);";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':game_key', $game_key, PDO::PARAM_STR);
					$stmt->bindValue(':date_insert', $date_insert, PDO::PARAM_STR); 
					$stmt->execute();
					$id_game = $connect->getConnect()->lastInsertId();

					$strSQL = "INSERT INTO tb_game_user(id_game, id_user, dt_created) VALUES(:id_game, :id_user, :date_insert);";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
					$stmt->bindValue(':id_user', $user->id, PDO::PARAM_INT);
					$stmt->bindValue(':date_insert', $date_insert, PDO::PARAM_STR); 
					$stmt->execute();

					$colors = $this->refer_new_game_colors($connect, $id_game, $total_colors_game);

					$connect->getConnect()->commit();

					$game->id = $id_game;
					$game->game_key = $game_key;
					$game->users = array($user);
					$game->colors = $colors;
					$game->creation_date = $date_insert;
				} 
				catch (Exception $eDb) 
				{
					$connect->getConnect()->rollBack(); 
					throw new Exception($eDb);
				}
				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
			return $game;
		}

		/**
		 * Add an user in a game
		 * @access public
		 * @var $id_game int
		 * @var $user_name string
		 * @return user
		 */ 
		public function add_user_multiplayer($id_game, $user_name)
		{
			$user = new game();
			try
			{
				$connect = new connect_db();
				$connect->open();

				$date_insert = date("Y-m-d H:i:s");

				$user = $this->refer_game_user($connect, $user_name, $date_insert);

				$strSQL = "INSERT INTO tb_game_user(id_game, id_user, dt_created) VALUES(:id_game, :id_user, :date_insert);";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->bindValue(':id_user', $user->id, PDO::PARAM_INT);
				$stmt->bindValue(':date_insert', $date_insert, PDO::PARAM_STR); 
				$stmt->execute();

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
			return $user;
		}

		/**
		 * Add an user or update date of last game of user
		 * @access private
		 * @var $connect connect_db
		 * @var $user_name string
		 * @var $date_insert string
		 * @return $user user
		 */ 
		private function refer_game_user(&$connect, $user_name, $date_insert)
		{
			$user = new user();
			try
			{
				$strSQL = "SELECT id FROM tb_user WHERE name = :user_name;";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
				$stmt->execute();

				$db_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($db_user) && count($db_user) > 0)
				{
					$user->id = $db_user[0]["id"];
					$strSQL = "UPDATE tb_user SET dt_last_game = :date_insert WHERE id = :id_user;";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':id_user', $user->id, PDO::PARAM_INT);
					$stmt->bindValue(':date_insert', $date_insert, PDO::PARAM_STR); 
					$stmt->execute();
				}
				else
				{
					$strSQL = "INSERT INTO tb_user(name, dt_created, dt_last_game) VALUES(:user_name, :date_insert, :date_insert);";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
					$stmt->bindValue(':date_insert', $date_insert, PDO::PARAM_STR); 
					$stmt->execute();
					$user->id = $connect->getConnect()->lastInsertId();
				}
				$user->name = $user_name;
			}
			catch (Exception $e)
			{
				throw new Exception($e);
			}
			return $user;
		}

		/**
		 * Select and insert random colors to the game
		 * @access public
		 * @var $connect connect_db
		 * @var $id_game int
		 * @var $total_colors_game int
		 * @return array
		 */ 
		private function refer_new_game_colors(&$connect, $id_game, $total_colors_game)
		{
			$colors = array();
			try
			{
				$strSQL = "SELECT id, name, short_name FROM tb_color;";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->execute();
				$db_color = $stmt->fetchAll(PDO::FETCH_ASSOC);

				for ($i=1; $i<=$total_colors_game; $i++)
				{
					$index_color = rand(0, ($total_colors_game - 1));

					$strSQL = "INSERT INTO tb_game_color(id_game, nb_order, id_color) VALUES(:id_game, :nb_order, :id_color);";

					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
					$stmt->bindValue(':nb_order', $i, PDO::PARAM_INT);
					$stmt->bindValue(':id_color', $db_color[$index_color]["id"], PDO::PARAM_INT);
					$stmt->execute();

					$color = new color();
					$color->id = $db_color[$index_color]["id"];
					$color->name = $db_color[$index_color]["name"];
					$color->short_name = $db_color[$index_color]["short_name"];
					$colors[$i - 1] = $color;
				}
			}
			catch (Exception $e)
			{
				throw new Exception($e);
			}
			return $colors;
		} 

		/**
		 * Consult a game_key and returns the game information
		 * @access public
		 * @var $game_key string
		 * @return game
		 */ 
		public function consult_game_key($game_key)
		{
			$game = new game();

			try
			{
				$strSQL = "SELECT
								g.id,
								g.solved,
								gc.id_color,
								gc.nb_order,
								c.short_name
							FROM
								tb_game g
								INNER JOIN tb_game_color gc ON gc.id_game = g.id
								INNER JOIN tb_color c ON c.id = gc.id_color 
							WHERE
								g.game_key = :game_key
							ORDER BY
								gc.nb_order;";

				$connect = new connect_db();
				$connect->open();
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':game_key', $game_key, PDO::PARAM_STR);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (count($rows) > 0)
				{
					foreach ($rows as $row) 
					{
						$game->id = $row["id"];
						$game->game_key = $game_key;
						$game->solved = ($row["solved"] == "Y" ? true : false);

						$color = new color();
						$color->id = $row["id_color"];
						$color->order = $row["nb_order"];
						$color->short_name = $row["short_name"];

						$game->colors[] = $color;
					}
				}

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $game;
		}

		/**
		 * Save a new user guess of a game
		 * @access public
		 * @var $id_game int
		 * @var $user_name string
		 * @var $short_name_colors string
		 * @var $exact_total int
		 * @var $near_total int
		 * @var $solved boolean
		 * @return game
		 */ 
		public function save_guess($id_game, $user_name, $short_name_colors, $exact_total, $near_total, $solved)
		{			
			$connect = new connect_db();
			$connect->open();

			$date_insert = date("Y-m-d H:i:s");

			try 
			{  
				$connect->getConnect()->beginTransaction();

				$user = $this->refer_game_user($connect, $user_name, $date_insert);

				$strSQL = "INSERT INTO tb_guess(id_game, id_user, exact, near, dt_created) VALUES(:id_game, :id_user, :exact, :near, :dt_created);";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->bindValue(':id_user', $user->id, PDO::PARAM_INT);
				$stmt->bindValue(':exact', $exact_total, PDO::PARAM_INT);
				$stmt->bindValue(':near', $near_total, PDO::PARAM_INT);
				$stmt->bindValue(':dt_created', $date_insert, PDO::PARAM_STR);
				$stmt->execute();
				$id_guess = $connect->getConnect()->lastInsertId();

				if ($id_guess > 0)
				{
					$strSQL = "SELECT id, short_name FROM tb_color;";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->execute();
					$db_color = $stmt->fetchAll(PDO::FETCH_ASSOC);

					foreach($short_name_colors as $index_color => $short_name_color)
					{
						foreach ($db_color as $color) 
						{
							if (trim(strtoupper($short_name_color)) == trim(strtoupper($color["short_name"])))
							{
								$strSQL = "INSERT INTO tb_guess_color(id_guess, id_color, nb_order) VALUES(:id_guess, :id_color, :nb_order);";
								$stmt = $connect->getConnect()->prepare($strSQL);
								$stmt->bindValue(':id_guess', $id_guess, PDO::PARAM_INT);
								$stmt->bindValue(':id_color', $color["id"], PDO::PARAM_INT);
								$stmt->bindValue(':nb_order', ($index_color + 1), PDO::PARAM_INT);
								$stmt->execute();
								break;
							}
						}
					}

					if ($solved)
					{
						$strSQL = "UPDATE tb_game SET solved = 'Y', id_user_solved = :id_user, dt_solved = :dt_solved WHERE id = :id_game;";
						$stmt = $connect->getConnect()->prepare($strSQL);
						$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
						$stmt->bindValue(':id_user', $user->id, PDO::PARAM_INT);
						$stmt->bindValue(':dt_solved', $date_insert, PDO::PARAM_STR);
						$stmt->execute();
					}
				}
				else
				{
					$connect->getConnect()->rollBack();
					throw new Exception("Error on insert guess", 1);
				}

				$connect->getConnect()->commit();
			} 
			catch (Exception $eDb) 
			{
				$connect->getConnect()->rollBack(); 
				throw new Exception($eDb);
			}

			$connect->close();
		}

		/**
		 * Consult the results of a game
		 * @access public
		 * @var $id_game int
		 * @return game
		 */ 
		public function consult_game_results($id_game)
		{
			$game = new game();

			try
			{
				$strSQL = "SELECT
								g.id, g.game_key, g.solved, g.id_user_solved, u.name, g.dt_solved, gu.dt_created  
							FROM
								tb_game g
								LEFT JOIN tb_user u ON u.id = g.id_user_solved 
								LEFT JOIN tb_game_user gu ON gu.id_game = g.id AND gu.id_user = u.id
							WHERE
								g.id = :id_game ;";

				$connect = new connect_db();
				$connect->open();
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$guesses = array();
				$game_colors = array();

				if (count($rows) > 0)
				{
					$game->id = $rows[0]["id"];
					$game->game_key = $rows[0]["game_key"];
					$game->solved = ($rows[0]["solved"] == 'Y' ? true : false);
					$game->user_solved->id = $rows[0]["id_user_solved"];
					$game->user_solved->name = $rows[0]["name"];
					$game->user_solved->game_entry_date = $rows[0]["dt_created"];
					$game->solution_date = $rows[0]["dt_solved"];

					$game->colors = $this->refer_consult_game_results_colors($connect, $id_game);
					$game->guesses = $this->refer_consult_game_results_guesses($connect, $id_game);
					$game->users = $this->refer_consult_game_results_users($connect, $id_game);
				}

				$connect->close();

			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $game;
		}

		/**
		 * Consult the users of a game
		 * @access private
		 * @var $connect connect_db 
		 * @var $id_game int
		 * @return array
		 */ 
		private function refer_consult_game_results_users(&$connect, $id_game)
		{
			$users = array();

			try
			{
				$strSQL = "SELECT
								u.name,
								gu.dt_created 
							FROM
								tb_game_user gu 
								INNER JOIN tb_user u ON u.id = gu.id_user
							WHERE
								gu.id_game = :id_game
							ORDER BY
								u.name;";

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (count($rows) > 0)
				{
					foreach ($rows as $row) 
					{
						$user = new user();
						$user->name = $row["name"];
						$user->game_entry_date = $row["dt_created"];
						$users[] = $user;
					}
				}
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $users;
		}

		/**
		 * Consult the colors of a game
		 * @access private
		 * @var $connect connect_db 
		 * @var $id_game int
		 * @return array
		 */ 
		private function refer_consult_game_results_colors(&$connect, $id_game)
		{
			$colors = array();

			try
			{
				$strSQL = "SELECT
								c.id, gc.nb_order, c.short_name
							FROM
								tb_game_color gc
								INNER JOIN tb_color c ON c.id = gc.id_color
							WHERE
								gc.id_game = :id_game
							ORDER BY 
								gc.nb_order;";

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (count($rows) > 0)
				{
					foreach ($rows as $row) 
					{
						$color = new color();
						$color->id = $row["id"];
						$color->order = $row["nb_order"];
						$color->short_name = $row["short_name"];
						$colors[] = $color;
					}
				}
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $colors;
		}

		/**
		 * Consult the guesses of a game
		 * @access private
		 * @var $connect connect_db 
		 * @var $id_game int
		 * @return array
		 */
		private function refer_consult_game_results_guesses(&$connect, $id_game)
		{
			$guesses = array();

			try
			{
				$strSQL = "SELECT
								g.id,
								g.exact,
								g.near,
								c.short_name,
								gc.nb_order,
								u.name as user_name, 
								g.dt_created
							FROM
								tb_guess g 
								INNER JOIN tb_guess_color gc ON gc.id_guess = g.id
								INNER JOIN tb_color c ON c.id = gc.id_color
								INNER JOIN tb_user u ON u.id = g.id_user 
							WHERE
								g.id_game = :id_game
							ORDER BY
								g.id,
								gc.nb_order; ";

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$guess = new guess();
				if (count($rows) > 0)
				{
					foreach ($rows as $row) 
					{
						if ($guess->id != $row["id"])
						{
							if ($guess->id > 0)
							{
								$guesses[] = $guess; 
							}

							$guess = new guess();
						    $guess->id = $row["id"];	
						    $guess->exact = $row["exact"];
						    $guess->near = $row["near"];
						    $guess->user->name = $row["user_name"];
						    $guess->creation_date = $row["dt_created"];
						}
						$color = new color();
						$color->order = $row["nb_order"];
						$color->short_name = $row["short_name"];
						$guess->colors[] = $color;
					}

					if ($guess->id > 0)
						$guesses[] = $guess; 
				}
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $guesses;
		}

		/**
		 * Validate if a user belongs to a game
		 * @access private
		 * @var $id_game int
		 * @var $user_name string
		 * @return array
		 */
		public function validate_user_game($id_game, $user_name)
		{
			$return = false;

			try
			{
				$connect = new connect_db();
				$connect->open();

				$strSQL = "SELECT
								gu.id_game
							FROM
								tb_game_user gu 
								INNER JOIN tb_user u ON u.id = gu.id_user
							WHERE
								gu.id_game = :id_game AND 
								u.name = :user_name;";

				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
				$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if (count($rows) > 0)
					$return = true;
				else
					$return = false;

				$connect->close();
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}

			return $return;
		}
	}
?>