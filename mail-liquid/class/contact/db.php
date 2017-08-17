<?php
/*
Package : Pegion-liquid
Coder : M.Hoshi
Version : 1.1.1
*/
class DB_CONTROL{
	/*DB許可*/
	public $db_mode = true;
	/*DBのホスト*/
	public $db_host = 'localhost';
	/*DBのユーザ*/
	public $db_user = 'root';
	/*DBのパスワード*/
	public $db_pswd = '0510';

	/*DB名*/
	public $db_name = 'form_log';
	/*テーブル名*/
	public $tb_name = 'log';

	/*テーブル有無チェック*/
	public function table_scan($db_name, $tb_name, $connection){
		$sql = "SHOW TABLES FROM ".$db_name." LIKE '".$tb_name."';";
		$result = mysql_query($sql, $connection);
		if(mysql_num_rows($result) == 0){
			return false;
		}else{
			return true;
		}
	}

	/*テーブル作成*/
	public function build_table($tb_name, $post_data, $connection){
		$sql = "";
		$sql .= "CREATE TABLE ".$tb_name." (";
		$sql .= "`_id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,";
		foreach($post_data as $sql_key=>$sql_value){
			if(strpos($sql_key, '-re') !== false){
				continue;
			}elseif(strpos($sql_key, 'mode') !== false){
				continue;
			}else{
				$sql .= "`".$sql_key."` VARCHAR(255),";
			}
		}
		$sql .= "`time` VARCHAR(255)) DEFAULT CHARSET=utf8;";
		mysql_query($sql, $connection);
	}

	/*レコード挿入*/
	public function insert_record($tb_name, $post_data, $form_detail, $connection){
		date_default_timezone_set('Asia/Tokyo');
		$today = date('Y/m/d G:i:s');
		$record = "";
		$record .= "INSERT INTO ".$tb_name."(";
			foreach($post_data as $sql_key=>$sql_value){
				if(strpos($sql_key, '-re') !== false){
					continue;
				}elseif(strpos($sql_key, 'mode') !== false){
					continue;
				}else{
					$record .= $sql_key.",";
				}
			}
		$record .= "time";
		$record .= ") VALUES (";
			foreach($post_data as $sql_key=>$sql_value){
				if(strpos($sql_key, '-re') !== false){
					continue;
				}elseif(strpos($sql_key, 'mode') !== false){
					continue;
				}else{
					$sql_value_array = array($form_detail[$sql_key]['label'], $sql_value);
					$serial = serialize($sql_value_array);
					$record .= "'".$serial."',";
				}
			}
		$record .= "'".$today."'";
		$record .= ");";
		mysql_query($record, $connection);
	}
}
?>
