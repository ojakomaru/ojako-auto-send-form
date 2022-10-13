<?php
//値の設定
// implode()カンマ区切りの文字列に戻す
// mb_ereg_replace(置換対象, 置換する文字, 文字列)カンマを改行で置き換える
$mail_setting =  get_option('ojako_mail_options');
$mail_subject = isset($mail_setting['mail_subject']) ? $mail_setting['mail_subject'] : "おジャコ丸からお知らせ";
$post_type    = isset($mail_setting['p_type']) ? $mail_setting['p_type'] : array();
$mail_address = isset($mail_setting['mail_address']) ? mb_ereg_replace(",","\r\n",implode(",",$mail_setting['mail_address']) ): null;
$mail_send = isset($mail_setting['mail_send']) ? $mail_setting['mail_send'] : array();
$data = array(
  array('「新規作成」から「公開」になったとき（new）','new','checked'),
  array('「下書き」から「公開」になったとき(draft)','draft',''),
  array('「予約」の状態から「公開」になったとき(future)','future',''),
  array('更新（「公開」の状態から「公開」をしたとき(publish)）','publish',''),
  array('「レビュー待ち」の状態から「公開」になったとき(pending)','pending',''),
  array('「非公開」の状態から「公開」になったとき(private)','private',''),
  array('「ゴミ箱」の状態から「公開」になったとき(trash)','trash',''),
);
$type = array(
  array('投稿記事','post',''),
  array('固定ページ','page',''),
  array('おジャコブログ', 'blogs', 'checked'),
  array('おジャコの実績', 'works', '')
);

//3.表示する内容(HTML)
$wp_n = wp_nonce_field('oja_postMail');
echo <<<EOS
  <div class="wrap">
    <h2>投稿・更新メールの設定</h2>
    <form method="post" action="">
  {$wp_n}
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="mail_subject">お知らせメールの件名:</label></th>
        <td><input type="text" name="mail_subject" id="mail_subject" value="{$mail_subject}" /></td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="add_text">メール送信する記事のタイプ</label></th>
      <td><ul>
EOS;
foreach($type as $akey => $t ) {
  if (array_search($t[1], $post_type) !== false) {
    $t[2] = "checked";
  }else {
    $t[2] = "";
  }
  echo <<<EOS
    <li>
      <label for ="p_type_{$akey}" >
        <input type="checkbox" name="p_type[]" {$t[2]} class="checkbox" id="p_type_{$akey}" value="{$t[1]}" />{$t[0]}
      </label>
    </li>
  EOS;
}
echo <<<EOS
    </ul></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="add_text">メールアドレス:</label></th>
    <td><textarea name="mail_address" id="mail_address"/>{$mail_address}</textarea></td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="add_text">送信条件:</label></th>
  <td><ul>
EOS;
foreach( $data as $akey => $d ) {
  if (array_search($d[1], $mail_send) !== false) {
    $d[2] = "checked";
  }else {
    $d[2] = "";
  }
  echo <<<EOS
    <li>
      <label for ="mail_send_{$akey}" >
        <input type="checkbox" name="mail_send[]" {$d[2]} class="checkbox" id="mail_send_{$akey}" value="{$d[1]}" />{$d[0]}
      </label>
    </li>
  EOS;
}
echo <<<EOS
        </ul></td>
      </tr>
    </table>
    <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
    </form>
  </div>
EOS;