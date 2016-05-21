<?php
	require_once("connect_db.php");
	require_once("struct/game.php");

	/**
	* 
	*/
	class mastermind 
	{
		public function new_game($user_name)
		{
			$return = false;

			$connect = new connect_db();
			$connect->open();

			$date_insert = date("Y-m-d H:i:s");

			$id_user = $this->new_game_user($connect, $user_name, $date_insert);
			$game_key = md5($id_user.$date_insert);

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
				$stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
				$stmt->bindValue(':date_insert', $date_insert, PDO::PARAM_STR); 
				$stmt->execute();

				$strSQL = "SELECT id FROM tb_color;";
				$stmt = $connect->getConnect()->prepare($strSQL);
				$stmt->execute();
				$db_color = $stmt->fetchAll(PDO::FETCH_ASSOC);

				for ($i=1; $i<=8; $i++)
				{
					$strSQL = "INSERT INTO tb_game_color(id_game, count, id_color) VALUES(:id_game, :count, :id_color);";
					$stmt = $connect->getConnect()->prepare($strSQL);
					$stmt->bindValue(':id_game', $id_game, PDO::PARAM_INT);
					$stmt->bindValue(':count', $i, PDO::PARAM_INT);
					$stmt->bindValue(':id_color', $db_color[rand(0, 7)]["id"], PDO::PARAM_INT);
					$stmt->execute();
				}

				$return = true;

				$connect->getConnect()->commit();
			} 
			catch (Exception $e) 
			{
				$connect->getConnect()->rollBack(); 
			}

			$connect->close();
			return $return;
		}

		private function new_game_user(&$connect, $user_name, $date_insert)
		{
			$strSQL = "SELECT id FROM tb_user WHERE name = :user_name;";
			$stmt = $connect->getConnect()->prepare($strSQL);
			$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
			$stmt->execute();
			$db_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (is_array($db_user) && count($db_user) > 0)
			{
				$id_user = $db_user[0]["id"];
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
				$id_user = $connect->getConnect()->lastInsertId();
			}

			return $id_user;
		}

		private function new_game_colors()
		{

		} 
	}
?>