<?php
	require_once("connect_db.php");
	require_once("struct/game.php");

	/**
	* 
	*/
	class mastermind 
	{
		/**
		 * Starts a new game
		 * @access public
		 * @var $user_name string
		 * @var $total_colors_game
		 */ 
		public function new_game($user_name, $total_colors_game)
		{
			$game = new game();
			try
			{
				$connect = new connect_db();
				$connect->open();

				$date_insert = date("Y-m-d H:i:s");

				$user = $this->new_game_user($connect, $user_name, $date_insert);
				$game_key = md5($id_user).md5($date_insert);

				try 
				{  
					$connect->getConnect()->beginTransaction();

					$strSQL = "INSERT INTO tb_game(game_key, dt_created) VALUES(:game_key, :date_insert);";
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

					$colors = $this->new_game_colors($connect, $id_game, $total_colors_game);

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
		 * Add an user
		 * @access public
		 * @var $connect connect_db
		 * @var $user_name string
		 * @var $date_insert string
		 * @return $user user
		 */ 
		private function new_game_user(&$connect, $user_name, $date_insert)
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
					$stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
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
		 * Select and insert randiom colors to the game
		 * @access public
		 * @var $connect connect_db
		 * @var $id_game int
		 * @var $total_colors_game int
		 * @return array
		 */ 
		private function new_game_colors(&$connect, $id_game, $total_colors_game)
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
	}
?>