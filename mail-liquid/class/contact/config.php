<?php
/*
Package : Pegion-liquid
Coder : M.Hoshi
Version : 1.0.3

■mail_toについて
メールの送信先です。
複数のアドレスを設定するにはクォーテーション内にカンマ区切りで入力してください。

■mail_subject
メールの件名です。

■reply_subject
リプライメールの件名です。

■mail_header
メールヘッダです。
From: xxx@xxx.com のフォーマットで入力してください。

■thanks_dir
送信完了ページ。

■error_dir
エラーページ。

■ チェックタイプについて
5種類のチェックタイプがあり、trueにすると自動でチェックルールが実行されます。
メールアドレス・電話場号・郵便番号チェックを実行するには、
それぞれフィールド名がemail・tel・postcodeに設定されている必要があります。
req     必須チェック
mail    メールアドレスチェック
tel     電話番号チェック
address 郵便番号チェック
retype  確認用入力チェック

■disabled
チェックボックスとラジオボタンで、特定の選択肢を選んだ場合のみ有効になるテキストフィールドです。
optionで設定した値のうち、条件として設定したいものを入力してください。

■example
placeholder内に表示する文字列です。
*/
class config{
	public $mail_to = 'adm4mysites@gmail.com';
	public $mail_subject = 'メールが届きました';
	public $reply_subject = '返信メール';
	public $mail_header = 'From: noreply@example.com';
	public $thanks_dir = '/mail-liquid/contact/thanks.php';
	public $error_dir = '/mail-liquid/contact/error.php';

	public $form_detail = array(
		'contact_type'=>array(
			'label' =>  '商品の種類',
			'type'  =>  'radio',
			'name'  =>  'contact_type',
			'id'    =>  'contact_type',
			'check_type' => array(
				'req'     => false,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'その他',
			'example'=>'',
			'option'=>  array(
				'商品A',
				'商品B',
				'その他'
				)
		),
		'id'=>array(
			'label' =>  'ID',
			'type'  =>  'text',
			'name'  =>  'id',
			'id'    =>  'id',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => true,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  false
		),
		'email'=>array(
			'label' =>  'メール',
			'type'  =>  'text',
			'name'  =>  'email',
			'id'    =>  'email',
			'check_type' => array(
				'req'     => true,
				'mail'    => true,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'例）info@xxx.com',
			'option'=>  false
		),
		'tel'=>array(
			'label' =>  '電話番号',
			'type'  =>  'text',
			'name'  =>  'tel',
			'id'    =>  'tel',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => true,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  false
		),
		'postcode'=>array(
			'label' =>  '郵便番号',
			'type'  =>  'text',
			'name'  =>  'postcode',
			'id'    =>  'postcode',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => false,
				'address' => true,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  false
		),
		'user'=>array(
			'label' =>  'お名前',
			'type'  =>  'text',
			'name'  =>  'user',
			'id'    =>  'user',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  false
		),
		'gender'=>array(
			'label' =>  '性別',
			'type'  =>  'radio',
			'name'  =>  'gender',
			'id'    =>  'gender',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  array(
				'男性',
				'女性'
			)
		),
		'pref'=>array(
			'label' =>  '都道府県',
			'type'  =>  'select',
			'name'  =>  'pref',
			'id'    =>  'pref',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  array(
				'北海道',
				'青森県',
				'秋田県',
				'岩手県',
				'山形県',
				'宮城県',
				'福島県',
				'新潟県',
				'富山県',
				'石川県',
				'福井県',
				'群馬県',
				'栃木県',
				'茨城県',
				'埼玉県',
				'千葉県',
				'東京都',
				'神奈川県',
				'山梨県',
				'長野県',
				'岐阜県',
				'静岡県',
				'愛知県',
				'三重県',
				'京都府',
				'滋賀県',
				'兵庫県',
				'奈良県',
				'大阪府',
				'和歌山県',
				'鳥取県',
				'岡山県',
				'島根県',
				'広島県',
				'山口県',
				'香川県',
				'徳島県',
				'愛媛県',
				'高知県',
				'福岡県',
				'大分県',
				'宮崎県',
				'佐賀県',
				'熊本県',
				'鹿児島県',
				'長崎県',
				'沖縄県'
			)
		),
		'transport'=>array(
			'label' =>  '交通機関',
			'type'  =>  'checkbox',
			'name'  =>  'transport',
			'id'    =>  'transport',
			'check_type' => array(
				'req'     => false,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'その他',
			'example'=>'',
			'option'=>  array(
				'バス',
				'地下鉄',
				'JR',
				'タクシー',
				'その他'
			)
		),
		'contact'=>array(
			'label' =>  'お問い合せ内容',
			'type'  =>  'textarea',
			'name'  =>  'contact',
			'id'    =>  'contact',
			'check_type' => array(
				'req'     => true,
				'mail'    => false,
				'tel'     => false,
				'address' => false,
				'retype'  => false,
			),
			'disabled'=>'',
			'example'=>'',
			'option'=>  false
		)
	);
}
?>
