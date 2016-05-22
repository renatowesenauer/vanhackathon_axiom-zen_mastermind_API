<?php
	/**
	 * Class of genereal functions of API
	 * @author Renato Wesenauer <renato.wesenauer@gmail.com>
	 * @since 2016-05-22
	 * @access public
	 */
	class general
	{
		/**
		 * Verify the number of exact colors and returns the array positions
		 * @access public
		 * @var $colors_user array
		 * @var $object_colors_game array
		 * @return array 
		 */
		public static function verify_color_exatc($colors_user, $object_colors_game)
	    {
	    	try
	    	{
		    	$exact = array();
		    	foreach ($colors_user as $index_color => $color_shor_name) 
				{
					foreach ($object_colors_game as $color) 
					{
						$color1 = trim(strtoupper($color->short_name));
						$color2 = trim(strtoupper($color_shor_name));

						if ($color1 == $color2 && $color->order == ($index_color + 1))
						{
							$exact[] = ($index_color + 1);
							break;
						}
					}
				}
				return $exact;
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
	    }

	    /**
		 * Verify the number of near colors and returns the array positions
		 * @access public
		 * @var $colors_user array
		 * @var $object_colors_game array
		 * @var $exact array
		 * @return array 
		 */
	    public static function verify_color_near($colors_user, $object_colors_game, $exact)
	    {
	    	try
	    	{
		    	$near = array();
		    	foreach ($colors_user as $index_color => $color_shor_name) 
				{
					foreach ($object_colors_game as $color) 
					{
						$color1 = trim(strtoupper($color->short_name));
						$color2 = trim(strtoupper($color_shor_name));

						if ($color1 == $color2)
						{
							if (!in_array($color->order, $exact) && !in_array($color->order, $near))
							{
								$near[] = $color->order;
								break;
							}
						}
					}
				}
				return $near;
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
	    }

	    /**
		 * Format a date interval
		 * @access public
		 * @var $date_start string
		 * @var $date_end string
		 * @return string
		 */
		public static function format_date_interval_info($date_start, $date_end)
		{
			try
			{
				if ($date_start != '0000-00-00 00:00:00' && $date_end != '0000-00-00 00:00:00')
				{
					$date_start = new DateTime($date_start);
					$date_end = new DateTime($date_end);
					$date_diff = $date_start->diff($date_end);

					$info = "";
					if ($date_diff->y > 0) 
					{
						if ($date_diff->y == 1)
							$info .= ", 1 year";
						else
							$info .= ", ".$date_diff->y." years";
					}

					if ($date_diff->m > 0) 
					{
						if ($date_diff->m == 1)
							$info .= ", 1 month";
						else
							$info .= ", ".$date_diff->m." months";
					}

					if ($date_diff->d > 0) 
					{
						if ($date_diff->d == 1)
							$info .= ", 1 day";
						else
							$info .= ", ".$date_diff->d." days";
					}

					if ($date_diff->h > 0) 
					{
						if ($date_diff->h == 1)
							$info .= ", 1 hour";
						else
							$info .= ", ".$date_diff->h." hours";
					}

					if ($date_diff->i > 0) 
					{
						if ($date_diff->i == 1)
							$info .= ", 1 minute";
						else
							$info .= ", ".$date_diff->i." minutes";
					}

					if ($info != "") $info = trim(substr($info, 2));

					if ($date_diff->s > 0) 
					{
						if ($info != "") $info .= " and ";

						if ($date_diff->s == 1)
							$info .= "1 second";
						else
							$info .= $date_diff->s." seconds";
					}

					return $info;
				}
				else
				{
					return "";
				}
			}
			catch(Exception $e)
			{
				throw new Exception($e);
			}
		}
	}
?>