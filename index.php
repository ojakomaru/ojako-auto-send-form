<?php
/*
Plugin Name: Ojako Auto Send Form
Plugin URI: 
Description: おジャコ丸によるメール配信プラグイン
Version: 1.0.0
Author:ojako
Author URI: https://ojako1012.com/
License: GPL2
*/
?>
<?php
/**
 * ライセンスを有効にするための設定
 * 使用法
 * 上で「License: GPL2」を指定したら下記の記述をコピーライトの西暦、作者メールアドレスを書き換えて使用する
 */
?>
<?php
/*  Copyright 2021 ojako (email : youthfulday.8348@gmail.com)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//初期設定
// //プラグインを有効にした時だけ実行したい処理(クラスなので配列でクラスと関数を指定)
// register_activation_hook( __FILE__, array( 'OjakoASF', 'oja_plugin_start' ) );
// // プラグインを無効にした時だけ実行される処理
// register_deactivation_hook( __FILE__, array( 'OjakoASF', 'oja_plugin_stop' ) );

//クラス定義
class OjakoASF {
  public function __construct() {
    register_activation_hook (__FILE__, array($this, 'oja_plugin_start'));

    register_deactivation_hook (__FILE__, array($this, 'oja_plugin_stop'));

    // register_uninstall_hook (__FILE__, array($this, 'oja_plugin_end'));
  }
  public function oja_plugin_start() {
    //インストールしたよ;
    //データベース・ファイルの作成など
    $mail_setting =  get_option('ojako_mail_options');
    if ( !$mail_setting ) {
      // 設定のデフォルトの値
      $default_setting = [
        'mail_subject' => '記事を更新しました',
        'p_type'       => array('post'),
        'mail_address' => null,
        'mail_send'    => array('new')
      ];
      update_option( 'ojako_mail_options', $default_setting );
    }
  }
  public function oja_plugin_stop(){
    delete_option('ojako_mail_options');
    //プラグイン、停止したよ;
    //データの初期化など
  }
  public function oja_plugin_end(){
    //プラグイン、削除したよ;
    //作成したデータベース・データ・ファイルの削除など
    delete_option('ojako_mail_options');
  }
}
//クラスオブジェクトの生成
new OjakoASF;

require_once(dirname(__FILE__).'/lib/script.php');
//moduleFileの読み込み
require_once(dirname(__FILE__).'/module/post_mail.php');

