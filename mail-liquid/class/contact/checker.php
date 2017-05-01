<?php
/*
Package : Pegion-liquid
Coder : M.Hoshi
Version : 1.0.1
*/
class checker{
	public function rule($post_data, $form_detail){
		$errors = array();
		foreach($post_data as $key => $value){
			if($key == 'mode'){
				continue;
			}
			/*
			-----------------------------
			リタイプ
			-----------------------------
			*/
			if(strpos($key, '-re') !== false){
				//キーからリタイプ識別符号を削除
				$key = str_replace('-re', '', $key);
				//値がない場合は必須かどうか確認
				if(!isset($value) || $value == ''){
					/*
					-----------------------------
					必須チェック
					-----------------------------
					*/
					//チェックタイプのreqがtrueの場合のみ実行
					if($form_detail[$key]["check_type"]["req"] === true){
						//必須項目が空の場合はエラーを返す
						$errors[$form_detail[$key]["name"].'-re'] = '<span class="errorstr">確認用'.$form_detail[$key]["label"].'を入力してください。</span>';
					}else{
						//チェックタイプのreqがfalseの場合はチェックをパス
						$errors[$form_detail[$key]["name"].'-re'] = '';
						//リタイプチェック
						if($post_data[$key] === $post_data[$key.'-re']){
							$errors[$form_detail[$key]["name"].'-re'] = '';
						}else{
							$errors[$form_detail[$key]["name"].'-re'] = '<span class="errorstr">確認用'.$form_detail[$key]["label"].'が一致しません。</span>';
						}
					}
				}else{
					//値があればエラーなし
					$errors[$form_detail[$key]["name"].'-re'] = '';
					//リタイプチェック
					if($post_data[$key] === $post_data[$key.'-re']){
						$errors[$form_detail[$key]["name"].'-re'] = '';
					}else{
						$errors[$form_detail[$key]["name"].'-re'] = '<span class="errorstr">確認用'.$form_detail[$key]["label"].'が一致しません。</span>';
					}
				}
			}
			//値がない場合は必須かどうか確認
			elseif(!isset($value) || $value == ''){
				/*
				-----------------------------
				必須チェック
				-----------------------------
				*/
				//チェックタイプのreqがtrueの場合のみ実行
				if($form_detail[$key]["check_type"]["req"] === true){
					//必須項目が空の場合はエラーを返す
					$errors[$form_detail[$key]["name"]] = '<span class="errorstr">'.$form_detail[$key]["label"].'を入力してください。</span>';
				}else{
					//チェックタイプのreqがfalseの場合はチェックをパス
					$errors[$form_detail[$key]["name"]] = '';
				}
			}else{
				//値があればエラーなし
				$errors[$form_detail[$key]["name"]] = '';

				/*
				-----------------------------
				メールアドレスチェック
				-----------------------------
				*/
				//チェックタイプのemailがtrueの場合のみ実行
				if($key == 'email'){
					if($form_detail[$key]["check_type"]["mail"] === true){
						if(isset($value) || $value != ''){
							if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $value)){
								//正しくない場合はエラーを返す
								$errors[$form_detail[$key]["name"]] = '<span class="errorstr">正しい'.$form_detail[$key]["label"].'を入力してください。</span>';
							}else{
								//正しい場合はエラーなし
								$errors[$form_detail[$key]["name"]] = '';
							}
						}
					}else{
						//チェックタイプのemailがfalseの場合はチェックをパス
						$errors[$form_detail[$key]["name"]] = '';
					}
				}

				/*
				-----------------------------
				電話番号チェック
				-----------------------------
				*/
				//チェックタイプのtelがtrueの場合のみ実行
				if($key == 'tel'){
					if($form_detail[$key]["check_type"]["tel"] === true){
						if(isset($value) || $value != ''){
							if(!preg_match("/^([0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4})?$|^[0-9]{9,11}+$/", $value)){
								//正しくない場合はエラーを返す
								$errors[$form_detail[$key]["name"]] = '<span class="errorstr">正しい'.$form_detail[$key]["label"].'を入力してください。</span>';
							}else{
								//正しい場合はエラーなし
								$errors[$form_detail[$key]["name"]] = '';
							}
						}
					}else{
						//チェックタイプのtelがfalseの場合はチェックをパス
						$errors[$form_detail[$key]["name"]] = '';
					}
				}

				/*
				-----------------------------
				郵便番号チェック
				-----------------------------
				*/
				//チェックタイプのaddressがtrueの場合のみ実行
				if($key == 'postcode'){
					if($form_detail[$key]["check_type"]["address"] === true){
						if(isset($value) || $value != ''){
							if(!preg_match("/^([0-9]{3}-[0-9]{4})?$|^[0-9]{7}+$/", $value)){
								//正しくない場合はエラーを返す
								$errors[$form_detail[$key]["name"]] = '<span class="errorstr">正しい'.$form_detail[$key]["label"].'を入力してください。</span>';
							}else{
								//正しい場合はエラーなし
								$errors[$form_detail[$key]["name"]] = '';
							}
						}
					}else{
						//チェックタイプのaddressがfalseの場合はチェックをパス
						$errors[$form_detail[$key]["name"]] = '';
					}
				}
			}

		}
		return $errors;
	}
}
?>
