<?php
/*
Package : Pegion
Coder : M.Hoshi
Version : 1.3.2
*/
//メール文字化け防止
mb_language('ja');
mb_internal_encoding('UTF-8');
require dirname(__FILE__).'/checker.php';
require dirname(__FILE__).'/config.php';
//出力系
$config = new config();
$html = '<table>';
$mail_to = $config->mail_to;
$mail_subject = $config->mail_subject;
$reply_subject = $config->reply_subject;
$mail_header = $config->mail_header;
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
		if($key == 'mode'){
			continue;
		}
		if(strpos($key, '-re') !== false){
			continue;
		}
		//必須
		$req = '';
		$req_txt = '';
		if($form_detail[$key]["check_type"]["req"] == true){
			$req = '<span class="caution">必須</span>';
			$req_txt = 'require';
		}else{
			$req = '<span class="uncaution">任意</span>';
		}
		$merge_value = '';
		$html .= '<tr>';
		$html .= '<th><label>'.$form_detail[$key]["label"].$req.'</label></th>';
		if(is_array($value)){
			//配列の場合
			foreach($value as $v){
				$merge_value .= $v."<br>";
			}
			$html .= '<td>'.$merge_value.'<input type="hidden" name="'.$key.'" value="'.$merge_value.'"></td>';
		}else{
			//通常のデータ
			$html .= '<td>'.$value.'<input type="hidden" name="'.$key.'" value="'.$value.'"></td>';
		}
		$html .= '</tr>';
	}
	$html .= '</table>';
	$html .= '<input type="hidden" name="mode" value="send">
	<a id="back" href="javascript:history.back();">戻る</a>
	<button id="send" type="submit">送信</button>';
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
		$form_detail[$key]["label"] = preg_replace("/\<.+?\>/", "", $form_detail[$key]["label"]);
		$contents .= '['.$form_detail[$key]["label"].']　'.str_replace("<br>", "\n", $value)."\r\n";
	}

	//テンプレート置き換え
	$mail_str = str_replace('%CONTENTS%', $contents, $mail_str);
	$reply_str = str_replace('%CONTENTS%', $contents, $reply_str);
	//メール送信処理
	$send_mail = mb_send_mail($mail_to, $mail_subject, $mail_str, $mail_header);
	$send_reply = mb_send_mail($post_data['mail'], $reply_subject, $reply_str, $mail_header);
	//結果
	if($send_mail && $send_reply){
		//送信成功
		header('location: /mail/contact/thanks.php');
	}else{
		//送信失敗
		header('location: /mail/contact/error.php');
	}
}
if(!isset($post_data['mode']) || !empty($is_error)){
	/*
	--------------------------
	デフォルトまたはエラー
	--------------------------
	*/
	foreach($form_detail as $value){
		//必須
		$req = '';
		$req_txt = '';
		if($value["check_type"]["req"] == true){
			$req = '<span class="caution">必須</span>';
			$req_txt = 'require';
		}else{
			$req = '<span class="uncaution">任意</span>';
		}
		//テキストボックス
		if($value['type'] == 'text'){
			$html .= '<tr>
			<th><label>'.$value["label"].$req.'</label></th>
			<td><input type="'.$value["type"].'" id="'.$value["id"].'" name="'.$value["name"].'" value="'.$post_data[$value["name"]].'" placeholder="'.$value["example"].'" class="'.$req_txt.'">'.$errors[$value["name"]].'</td>
			</tr>';
			//リタイプ
			if($value['check_type']['retype'] == true){
				$html .= '<tr>
				<th><label>'.$value["label"].'(確認)'.$req.'</label></th>
				<td><input type="'.$value["type"].'" id="'.$value["id"].'-re" name="'.$value["name"].'-re" value="'.$post_data[$value["name"].'-re'].'">'.$errors[$value["name"].'-re'].'</td>
				</tr>';
			}
		}
		//ラジオボタン
		if($value['type'] == 'radio'){
			$html .= '<tr>
			<th><label>'.$value["label"].$req.'</label></th>
			<td class="'.$value["name"].'"><input type="hidden" id="'.$value["id"].'" name="'.$value["name"].'" value="">';
			foreach($value['option'] as $ops){
				$html .= '<input type="'.$value["type"].'" id="'.$value["id"].'" name="'.$value["name"].'[]" value="'.$ops.'"'.form_checked($post_data[$value["name"]],$ops).'>'.$ops.'<br>';
			}
			//disabled 入力項目
			if($value['disabled'] != ''){
				$html .= '<input type="text" class="disabled_'.$value["name"].'" id="'.$value["id"].'" name="'.$value["name"].'[]" value="'.get_disabled_data($post_data[$value["name"]]).'" disabled>';
				$html .= "<script>
				$(function(){
					if($('td.".$value["name"]." input[value=\"".$value['disabled']."\"]:checked').length == 1){
						if($('input.disabled_".$value['name']."').is(':disabled')){
							$('input.disabled_".$value['name']."').prop('disabled', false);
						}
					}else{
						$('input.disabled_".$value['name']."').val('');
					}
					$('td.".$value["name"]." input').change(function(){
						if($('td.".$value["name"]." input[value=\"".$value['disabled']."\"]:checked').length == 1){
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
			$html .= $errors[$value["name"]].'</td></tr>';
		}
		//セレクト
		if($value['type'] == 'select'){
			$html .= '<tr>
			<th><label>'.$value["label"].$req.'</label></th>
			<td><select name="'.$value["name"].'" id="'.$value["id"].'">';
			$html .= '<option value="">選択されていません</option>';
			foreach($value['option'] as $ops){
				$html .= '<option value="'.$ops.'"'.form_selected($post_data[$value["name"]],$ops).'>'.$ops.'</option>';
			}
			$html .= '</select>'.$errors[$value["name"]].'</td></tr>';
		}
		//チェックボックス
		if($value['type'] == 'checkbox'){
			$html .= '<tr>
			<th><label>'.$value["label"].$req.'</label></th>
			<td class="'.$value["name"].'"><input type="hidden" id="'.$value["id"].'" name="'.$value["name"].'" value="">';
			foreach($value['option'] as $ops){
				$html .= '<input type="'.$value["type"].'" id="'.$value["id"].'" name="'.$value["name"].'[]" value="'.$ops.'"'.form_checked($post_data[$value["name"]],$ops).'>'.$ops.'<br>';
			}
			//disabled 入力項目
			if($value['disabled'] != ''){
				$html .= '<input type="text" class="disabled_'.$value["name"].'" id="'.$value["id"].'" name="'.$value["name"].'[]" value="'.get_disabled_data($post_data[$value["name"]]).'" disabled>';
				$html .= "<script>
				$(function(){
					if($('td.".$value["name"]." input[value=\"".$value['disabled']."\"]:checked').length == 1){
						if($('input.disabled_".$value['name']."').is(':disabled')){
							$('input.disabled_".$value['name']."').prop('disabled', false);
						}
					}else{
						$('input.disabled_".$value['name']."').val('');
					}
					$('td.".$value["name"]." input').change(function(){
						if($('td.".$value["name"]." input[value=\"".$value['disabled']."\"]:checked').length == 1){
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
			$html .= $errors[$value["name"]].'</td></tr>';
		}
		//テキストエリア
		if($value['type'] == 'textarea'){
			$html .= '<tr>
			<th><label>'.$value["label"].$req.'</label></th>
			<td><textarea name="'.$value["name"].'" id="'.$value["id"].'" class="'.$req_txt.'">'.$post_data[$value["name"]].'</textarea>'.$errors[$value["name"]].'</td>';
		}
	}
	$html .= '</table>';
	$html .= '<input type="hidden" name="mode" value="confirm">
	<button id="confirm" type="submit">確認画面へ</button>';
}
?>
