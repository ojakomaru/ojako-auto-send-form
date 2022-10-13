<?php



//オブジェクトを生成し実行する
new OjakoSendMail;

//クラス定義
class OjakoSendMail{
    public function __construct(){
      add_action('admin_menu', array($this, 'adminAddMenu'));
      add_action('transition_post_status', array($this,'publish_state'), 10, 3);
	}

	//管理メニューの設定
	public function adminAddMenu() {
    //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page('投稿更新メール','更新メール設定',  'level_8', __FILE__, array($this,'ojako_postMail_page'), 'dashicons-smiley', 6);
	}

	//管理ページの更新
	public function ojako_postMail_page() {
    //入力値があった場合の処理
    if ($_POST && check_admin_referer('oja_postMail')) {
      //値の更新
      $mail_address = $this->mailAddressCheck($_POST['mail_address']);
      $mail_setting = [
        'mail_subject' => esc_html($_POST['mail_subject']),
        'p_type'       => $_POST['p_type'] ? $_POST['p_type'] : array(),
        'mail_address' => $mail_address,
        'mail_send'    => $_POST['mail_send'] ? $_POST['mail_send'] : array()
      ];
      $is_update = update_option('ojako_mail_options', $mail_setting);
      // 更新メッセージの表示
      if ($is_update) {
        echo '<div class="updated fade"><p><strong>';
        _e('お知らせ設定を更新しました.');
        echo "</strong></p></div>";
      } else {
        echo '<div class="updated fade"><p><strong>';
        _e('メールアドレスが正しくないので、保存されませんでした。');
        echo "</strong></p></div>";
      }
    }

    //HTMLとして表示
    include_once( dirname(__FILE__) . '../../views/setting_form.php' );
	} //public function ojako_postMail_page()

	//メールアドレスの確認
	public function mailAddressCheck($mail_address){
    //mb_split()改行で区切って配列へ変換する
    $data =  mb_split("\r\n|\r|\n", $mail_address);
		$val = array();
		foreach ($data as $mail) {
      // is_email - 文字列がメールアドレス形式か調べる
      if(!is_email($mail)){
        $val = false;
        break;
      }else{
        array_push($val,$mail);
      }
		}
		return $val;
	}

	//メールの条件処理
	public function publish_state($new_status, $old_status, $post){
    //管理画面で設定した条件分岐に基づく
    $mail_setting =  get_option('ojako_mail_options');
    // array_search(検索値, 検索対象) ※返り値が0では困るので「!==false」とする
    if ($new_status == 'publish' && array_search($post->post_type, $mail_setting['p_type']) !== false ) {
      switch ($old_status) {
        case 'new':   //新規作成
        case 'draft': //下書き
        case 'future'://投稿予約
          $str = 'ブログ記事が更新されました';
        break;
        case 'publish':
          $str = 'ブログ記事が更新されました';
        break;
        case 'pending': //レビュー待ち
          $str = 'レビュー待ちのブログ記事が公開されました';
        break;
        case "private":
          $str = "ブログ記事が非公開->公開されました";
        break;
        case "trash":
          $str = "ブログ記事がゴミ箱->公開されました";
        break;
      }

      if (array_search($old_status, $mail_setting['mail_send']) !== false ) {
        $this->ojako_sendMail($mail_setting, $post, $str);
      }
    }
	} //public function publish_state

	//###############################
	//公開時のメール送信
	//###############################
	public function ojako_sendMail($mail_setting, $post, $str){
    $title        = $post->post_title;
    $expt         = $post->post_excerpt;
    $link         = get_permalink($post->ID);
    $mail_address = $mail_setting['mail_address'];
    $subject      = $mail_setting['mail_subject'];
    $oja_address  = get_option(['admin_email']);
    $message = <<<EOS
      おジャコ丸です。
      {$oja_address}
      {$str}
      ※今回のタイトル
      「{$title}」
      {$link}
      {$expt}
    EOS;
    $a = wp_mail($mail_address, $subject, $message);
    if ($a) wp_mail($oja_address, 'お知らせメールを送信しました。',$message);
	}
}