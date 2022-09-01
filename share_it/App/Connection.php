<?php

namespace App;

class Connection {

	public static function getDb() {
		try {
            
			$conn = new \PDO(
				"mysql:host=localhost;dbname=id18447020_share_it;charset=utf8;",
				"id18447020_admin",
				"Polosky!2011" 
			);
			return $conn;

		} catch (\PDOException $e) {
			//.. tratar de alguma forma ..//´
		echo'O erro: 	'. $e;
		
		}
	}
}

?>