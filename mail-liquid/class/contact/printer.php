<?php
/*
Package : Pegion-liquid
Coder : M.Hoshi
Version : 1.0.3
*/
//メール文字化け防止
mb_language('ja');
mb_internal_encoding('UTF-8');
require dirname(__FILE__).'/checker.php';
require dirname(__FILE__).'/config.php';
//出力系
$config = new config();
$html = $page_tpl;
$mail_to = $config->mail_to;
$mail_subject = $config->mail_subject;
$reply_subject = $config->reply_subject;
$mail_header = $config->mail_header;
$return_path = $config->return_path;
$thanks_dir = $config->thanks_dir;
$error_dir = $config->error_dir;
$form_detail = $config->form_detail;

//ポストデータ取得
$post_data = array();
//ポストデータ初期化
foreach($form_detail as $key => $value){
	$post_data[$key] = '';
	if($value['check_type']['retype'] == true){
		$post_data[$key.'-re'] = '';
	}
}
//エスケープ処理
$raw_post_data = $_POST;
foreach($raw_post_data as $key => $value){
	if(is_array($value)){
		//配列の場合
		foreach($value as $v){
			$post_data[$key][] = htmlspecialchars($v);
		}
	}else{
		//通常のデータ
		$post_data[$key] = htmlspecialchars($value);
	}
}

//チェッカー読み込み
$checker = new checker();
$errors = array();
$is_error = false;
//エラーメッセージ初期化
foreach($form_detail as $key => $value){
	$errors[$key] = '';
	if($value['check_type']['retype'] == true){
		$errors[$key.'-re'] = '';
	}
}
//ラジオボタンとセレクトボタンの値を維持
function form_checked($post_data,$ops){
	//配列の場合
	if(is_array($post_data)){
		foreach($post_data as $key=>$value){
			if($value == $ops){
				return ' checked';
			}
		}
	}
	//通常のデータ
	if($post_data == $ops){
		return ' checked';
	}
}
//セレクトタグのオプション値を維持
function form_selected($post_data,$ops){
	if($post_data == $ops){
		return ' selected';
	}
}
//disabledの値を取得
function get_disabled_data($post_data){
	if(is_array($post_data)){
		return end($post_data);
	}
}

if(isset($post_data['mode']) && $post_data['mode'] == 'confirm'){
	/*
	--------------------------
	確認画面
	--------------------------
	*/
	//入力チェック実行
	$errors = $checker->rule($post_data, $form_detail);
	foreach($errors as $value){
		if($value != ''){
			$is_error = true;
		}
	}
	//エラーなし
	if(empty($is_error)){
	foreach($post_data as $key => $value){
		//フィールド初期化
		$field = '';
		if($key == 'mode'){
			continue;
		}
		if(strpos($key, '-re') !== false){
			continue;
		}
		//必須
		$req_txt = '';
		if($form_detail[$key]["check_type"]["req"] == true){
			$req_txt = 'require';
		}
		$merge_value = '';
		if(is_array($value)){
			//配列の場合
			$loop_count = 1;
			$value_count = count($value);
			foreach($value as $v){
				if($loop_count < $value_count){
					$merge_value .= $v."<br>";
				}else{
					$merge_value .= $v;
				}
				$loop_count++;
			}
			$field .= $merge_value.'<input type="hidden" name="'.$key.'" value="'.$merge_value.'">';
		}else{
			//通常のデータ
			$field .= $value.'<input type="hidden" name="'.$key.'" value="'.$value.'">';
		}
		$html = str_replace('[:::'.$key.':::]', $field, $html);
	}
	$html .= '<div class="form_btn_wrap cl">';
	$html .= '<input type="hidden" name="mode" value="send">';
	$html .= '<a id="back" href="javascript:history.back();">戻る</a>';
	$html .= '<button id="send" type="submit">送信</button>';
	$html .= '</div>';
	}
}
if(isset($post_data['mode']) && $post_data['mode'] == 'send'){
	/*
	--------------------------
	送信
	--------------------------
	*/
	//送信内容初期化
	$contents = '';
	//メールテンプレート読み込み
	$mail_str = file_get_contents(dirname(__FILE__).'/tpl/mail.txt');
	$reply_str = file_get_contents(dirname(__FILE__).'/tpl/reply.txt');
	//ポストデータ分解
	foreach($post_data as $key=>$value){
		if($key == 'mode'){
			continue;
		}
		if(strpos($key, '-re') !== false){
			continue;
		}
		//改行タグ変換
		$value = str_replace("&lt;br&gt;", "\n", $value);

		//テンプレート置き換え
		$mail_str = str_replace('[:::'.$key.':::]', $value, $mail_str);
		$reply_str = str_replace('[:::'.$key.':::]', $value, $reply_str);
	}
	//メール送信処理
	$send_mail = mb_send_mail($mail_to, $mail_subject, $mail_str, "From: ".$post_data['email']);
	$send_reply = mb_send_mail($post_data['email'], $reply_subject, $reply_str, $mail_header, $return_path);

	//結果
	if($send_mail && $send_reply){
		//送信成功
		header('location: '.$thanks_dir);
	}else{
		//送信失敗
		header('location: '.$error_dir);
	}
}
if(!isset($post_data['mode']) || !empty($is_error)){
	/*
	--------------------------
	デフォルトまたはエラー
	--------------------------
	*/
	foreach($form_detail as $value){
		//フィールド初期化
		$field = '';
		//必須
		$req_txt = '';
		if($value["check_type"]["req"] == true){
			$req_txt = 'require';
		}
		//テキストボックス
		if($value['type'] == 'text'){
			$field .= '<input type="'.$value["type"].'" id="'.$value["id"].'" name="'.$value["name"].'" value="'.$post_data[$value["name"]].'" placeholder="'.$value["example"].'" class="'.$req_txt.'">'.$errors[$value["name"]];

			//リタイプ
			if($value['check_type']['retype'] == true){
				$field .= '<input type="'.$value["type"].'" id="'.$value["id"].'-re" name="'.$value["name"].'-re" value="'.$post_data[$value["name"].'-re'].'">（確認用）'.$errors[$value["name"].'-re'];
			}
		}
		//ラジオボタン
		if($value['type'] == 'radio'){
			$field .= '<input type="hidden" id="'.$value["id"].'" name="'.$value["name"].'" value="">';
			foreach($value['option'] as $ops){
				$field .= '<input type="'.$value["type"].'" class="'.$value["id"].'" name="'.$value["name"].'[]" value="'.$ops.'"'.form_checked($post_data[$value["name"]],$ops).'>'.$ops.'<br>';
			}
			//disabled 入力項目
			if($value['disabled'] != ''){
				$field .= '<input type="text" class="disabled_'.$value["name"].'" id="'.$value["id"].'" name="'.$value["name"].'[]" value="'.get_disabled_data($post_data[$value["name"]]).'" disabled>';
				$field .= "<script>
				$(function(){
					if($('input[value=\"".$value['disabled']."\"]:checked').length == 1){
						if($('input.disabled_".$value['name']."').is(':disabled')){
							$('input.disabled_".$value['name']."').prop('disabled', false);
						}
					}else{
						$('input.disabled_".$value['name']."').val('');
					}
					$('input.".$value['id']."').change(function(){
						if($('input[value=\"".$value['disabled']."\"]:checked').length == 1){
							if($('input.disabled_".$value['name']."').is(':disabled')){
								$('input.disabled_".$value['name']."').prop('disabled', false);
							}
						}else{
							$('input.disabled_".$value['name']."').val('');
							$('input.disabled_".$value['name']."').prop('disabled', true);
						}
					});
				});</script>";
			}
			$field .= $errors[$value["name"]];
		}
		//セレクト
		if($value['type'] == 'select'){
			$field .= '<select name="'.$value["name"].'" id="'.$value["id"].'">';
			$field .= '<option value="">選択されていません</option>';
			foreach($value['option'] as $ops){
				$field .= '<option value="'.$ops.'"'.form_selected($post_data[$value["name"]],$ops).'>'.$ops.'</option>';
			}
			$field .= '</select>'.$errors[$value["name"]];
			$field .= '</select>';
		}
		//チェックボックス
		if($value['type'] == 'checkbox'){
			$field .= '<input type="hidden" id="'.$value["id"].'" name="'.$value["name"].'" value="">';
			foreach($value['option'] as $ops){
				$field .= '<input type="'.$value["type"].'" class="'.$value["id"].'" name="'.$value["name"].'[]" value="'.$ops.'"'.form_checked($post_data[$value["name"]],$ops).'>'.$ops.'<br>';
			}
			//disabled 入力項目
			if($value['disabled'] != ''){
				$field .= '<input type="text" class="disabled_'.$value["name"].'" id="'.$value["id"].'" name="'.$value["name"].'[]" value="'.get_disabled_data($post_data[$value["name"]]).'" disabled>';
				$field .= "<script>
				$(function(){
					if($('input[value=\"".$value['disabled']."\"]:checked').length == 1){
						if($('input.disabled_".$value['name']."').is(':disabled')){
							$('input.disabled_".$value['name']."').prop('disabled', false);
						}
					}else{
						$('input.disabled_".$value['name']."').val('');
					}
					$('input.".$value["id"]."').change(function(){
						if($('input[value=\"".$value['disabled']."\"]:checked').length == 1){
							if($('input.disabled_".$value['name']."').is(':disabled')){
								$('input.disabled_".$value['name']."').prop('disabled', false);
							}
						}else{
							$('input.disabled_".$value['name']."').val('');
							$('input.disabled_".$value['name']."').prop('disabled', true);
						}
					});
				});</script>";
			}
			$field .= $errors[$value["name"]];
		}
		//テキストエリア
		if($value['type'] == 'textarea'){
			$field .= '<textarea name="'.$value["name"].'" id="'.$value["id"].'" class="'.$req_txt.'">'.$post_data[$value["name"]].'</textarea>'.$errors[$value["name"]];
		}
		$html = str_replace('[:::'.$value["name"].':::]', $field, $html);
	}
	$html .= '<input type="hidden" name="mode" value="confirm">';
	$html .= '<button id="confirm" type="submit">確認画面へ</button>';
}
?>
