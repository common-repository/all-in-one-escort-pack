<?php 
/*
	Plugin Name: All in ONE ESCORT PACK
	Description: escort all in pack
	Author: Hideto Sano
	Version: 1.0.0
*/

define( 'ES_PLUGIN_URL', untrailingslashit( plugins_url( 'includes', __FILE__ ) ) );
define( 'ES_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'ES_PLUGIN_FILE_PATH', __FILE__);

//------------------------------------------------
//初期設定
//------------------------------------------------
new EscortClass();

///////////////////////////////////////////////////////////////////////////////////////////

class EscortClass {

	function __construct() 
	{
		//専用のメニューを追加する
		add_action('admin_menu', array($this, 'escort_menu'));
		add_action('save_post', array($this, 'save_custom_postdata'));
		add_action('admin_enqueue_scripts', array($this, 'custom_enqueue'));

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(!is_plugin_active('attend-manger/attend-manager.php'))
		{
			add_action('admin_menu', array($this, 'add_cast_inputbox'));
		}
	}

	function add_cast_inputbox()
	{
		add_meta_box( 'my_cast_info', 'キャスト情報', array($this, 'cast_area'), 'post', 'normal' );
	}

	function cast_area()
	{
		global $post;
		$form_parts_data = get_option('escort_post_type_profile');
		if($form_parts_data != "")
		{
			$ecort_profile = json_decode($form_parts_data, true);
			foreach ($ecort_profile as $profile) 
			{
				$val   = get_post_meta($post->ID, 'am-'.$profile['filed'], true);
				$parts = $this->convertFormParts($profile['filed_type'], $profile['filed'], $val);
 				$form_parts .= <<<__HTML__
 	<tr>
 	    <td>
 	       	{$profile['label']}
 	    </td>
 	    <td>
 	        {$parts}
 	    </td>
 	</tr>
__HTML__;
			}
		}

	   echo <<<__HTML__
<style type="text/css">
	#list-table td
	{
		padding: 20px 20px 0px 0px;
	}
</style>
<div class="inside">
<input type="hidden" name="custom_data_flg" value="1">
<table id="list-table">
<tbody id="the-list" data-wp-lists="list:meta">{$form_parts}</tbody>
</table>
</div>
__HTML__;
	}

	//------------------------------------------------
	// フォームパーツを変換する
	//------------------------------------------------
	function convertFormParts($type, $name, $value)
	{
		switch($type)
		{
			case "text":
			case "date":
				return <<<__HTML__
<input type="{$type}" name="custom_data[am-{$name}]" value="{$value}">
__HTML__;
			case "textarea":
				return <<<__HTML__
<textarea name="custom_data[am-{$name}]">{$value}</textarea>
__HTML__;
			default:
				return;
		}
	}

	//------------------------------------------------
	// 管理メニューを追加する
	//------------------------------------------------
	function escort_menu()
	{
		add_menu_page(
				'All in ONE ES',
				'All in ONE ES',
				'administrator',
				'escort_main_menu',
				array($this, 'escort_viewer')
			);

		add_submenu_page(
				'escort_main_menu',
				'Add-ons',
				'Add-ons',
				'administrator',
				'escort_main_menu_3',
				array($this, 'escort_add_ons')
			);

		add_submenu_page(
				'escort_main_menu',
				'システム設定',
				'システム設定',
				'administrator',
				'escort_main_menu_1',
				array($this, 'escort_edit_setting')
			);

		add_submenu_page(
				'escort_main_menu',
				'料金設定',
				'料金設定',
				'administrator',
				'escort_main_menu_2',
				array($this, 'escort_price_setting')
			);
	}

	function custom_enqueue($hook_suffix) 
	{
		wp_enqueue_script('custom_js', ES_PLUGIN_URL . '/js/escort.js', array('jquery'));
		wp_enqueue_style('custom_css', ES_PLUGIN_URL . '/css/style.css');
	}

	function escort_add_ons()
	{
		echo <<<__HTML__
	<div class="wrap">
		<h2 class="page-header">ALL in ONE Escort Pack Add-Ons</h2>
		<div class="es-alert">
			<p style="">ALL IN ONE ESCORT PACK プラグインに機能を追加するアドオンが利用できます。<br>
			それぞれのアドオンは、個別のプラグインとしてインストールする(管理画面で更新できる)か、テーマに含める(管理画面で更新できない)かしてください。</p>
		</div>
		<div id="add-ons" class="clearfix">
		<div class="add-on-group clearfix">
			<div class="add-on wp-box " style="min-height: 434px;">
				<a target="_blank" href="http://attendthemes.com/"><img src="http://attendthemes.com/wp-content/themes/am-themes/img/demo.png" style="max-width:220px;"></a>
				<div class="inner">
					<h3>
						<a target="_blank" href="http://attendthemes.com/">出勤管理 STARTER</a>
					</h3>
					<p>キャストの出勤情報を登録・表示を簡単に設定できます。</p>
				</div>
				<div class="footer">
					<a target="_blank" href="http://attendthemes.com" class="button">購入してインストールする</a>
				</div>
			</div>
<!--
			<div class="add-on wp-box " style="min-height: 434px;">
				<a target="_blank" href="http://attendthemes.com/"><img src="http://attendthemes.com/wp-content/themes/am-themes/img/demo.png" style="max-width:220px;"></a>
				<div class="inner">
					<h3>
						<a target="_blank" href="http://attendthemes.com/">出勤管理 BASIC</a>
					</h3>
					<p>キャストの出勤情報を登録・表示を簡単に設定できます。</p>
				</div>
				<div class="footer">
					<a target="_blank" href="http://attendthemes.com" class="button">購入してインストールする</a>
				</div>
			</div>
			<div class="add-on wp-box " style="min-height: 434px;">
				<a target="_blank" href="http://attendthemes.com/"><img src="http://attendthemes.com/wp-content/themes/am-themes/img/demo.png" style="max-width:220px;"></a>
				<div class="inner">
					<h3>
						<a target="_blank" href="http://attendthemes.com/">出勤管理 PRO</a>
					</h3>
					<p>キャストの出勤情報を登録・表示を簡単に設定できます。</p>
				</div>
				<div class="footer">
					<a target="_blank" href="http://attendthemes.com" class="button">購入してインストールする</a>
				</div>
			</div>
			<div class="add-on wp-box " style="min-height: 434px;">
				<a target="_blank" href="http://www.advancedcustomfields.com/add-ons/options-page/"><img src="http://demo.nigata.co/wp-content/plugins/advanced-custom-fields/images/add-ons/options-page-thumb.jpg"></a>
				<div class="inner">
					<h3>
						<a target="_blank" href="http://www.advancedcustomfields.com/add-ons/options-page/">オプションページ</a>
					</h3>
					<p>ウェブサイト全体で使用できるグローバルデータを作成します。</p>
				</div>
				<div class="footer">
					<a target="_blank" href="http://www.advancedcustomfields.com/add-ons/options-page/" class="button">購入してインストールする</a>
				</div>
			</div>
-->
		</div>
		</div>
	</div>
__HTML__;
	}

	function escort_price_setting()
	{
		if(isset($_POST['delete_id']))
		{
			list($a, $delete_id) = explode('-', $_POST['delete_id']);
			unset($_POST['postdata'][$delete_id]);
			unset($_POST['delete_id']);

			$post_data = json_encode($_POST['postdata']);
			update_option('escort_post_type_price', $post_data);
			$mes =<<<__HTML__
<div id="message" class="updated below-h2">
	<p>削除しました。</p>
</div>
__HTML__;
		}
		elseif (isset($_POST['postdata'])) 
		{
			$post_data = json_encode($_POST['postdata']);
			update_option('escort_post_type_price', $post_data);
			$mes =<<<__HTML__
<div id="message" class="updated below-h2">
	<p>登録しました。</p>
</div>
__HTML__;
		}
		$ecort_profile = json_decode(get_option('escort_post_type_price'), true);
?>
	<div class="wrap">
		<?php echo $mes; ?>
		<h2 class="page-header">料金設定</h2>
	  	<form action="" method="post" id="myForm">
		<div class="table-responsive">
			<table width="100%" id="es-table" class="widefat">
			<thead>
			<tr>
				<th style="width:5%">#</th>
				<th style="width:30%">ラベル名</th>
				<th style="width:30%">フィールド名</th>
				<th style="width:5%"></th>
			</tr>
			</thead>
			<tfoot>
			<tbody>
<?php
			$num = 1;
			foreach($ecort_profile as $profile){
?>
			<tr>
				<td><span class="circle"><?php echo $num; ?></span></td>
				<td><input type="text" name="postdata[<?php echo $num; ?>][label]" value="<?php echo $profile['label'] ?>"></td>
				<td><input type="text" name="postdata[<?php echo $num; ?>][filed]" value="<?php echo $profile['filed'] ?>"></td>
				<td><a class="delprofile" id="del-<?php echo $num; ?>" href="#">削 除</a></td>
			</tr>
<?php
				$num++;
			}
?>
			<tr id="es-tb-last">
				<th colspan="3"></th>
				<th><a href="#" id="es-add-btn" class="button-primary">+ フィールドを追加</a></th>
			</tr>
			</tbody>
			</tfoot>
			</table>
			<p class="submit">
				<button type="submit" class="button-primary">保 存</button>
			</p>
			</form>
		</div>
	</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	var num = <?php echo $num ?>;
	$("#es-add-btn").click(function () {
		$("#es-tb-last").before('<tr><td><span class="circle">'+ num +'</span></td><td><input type="text" name="postdata['+ num +'][label]" /></td><td><input type="text" name="postdata['+ num +'][filed]"  /></td><td></td></tr>').fadeIn(1000);
		num = num + 1;
	});
});
</script>
<?php
	}

	//------------------------------------------------
	// 
	//------------------------------------------------
	function escort_edit_setting()
	{
		if(isset($_POST['delete_id']))
		{
			list($a, $delete_id) = explode('-', $_POST['delete_id']);
			unset($_POST['postdata'][$delete_id]);
			unset($_POST['delete_id']);

			$post_data = json_encode($_POST['postdata']);
			update_option('escort_post_type_system', $post_data);
			$mes =<<<__HTML__
<div id="message" class="updated below-h2">
	<p>削除しました。</p>
</div>
__HTML__;
		}
		elseif (isset($_POST['postdata'])) 
		{
			$post_data = json_encode($_POST['postdata']);
			update_option('escort_post_type_system', $post_data);
			$mes =<<<__HTML__
<div id="message" class="updated below-h2">
	<p>登録しました。</p>
</div>
__HTML__;
		}
		$ecort_profile = json_decode(get_option('escort_post_type_system'), true);
?>
	<div class="wrap">
		<?php echo $mes; ?>
		<h2 class="page-header">システム設定</h2>
	  	<form action="" method="post" id="myForm">
		<div class="table-responsive">
			<table width="100%" id="es-table" class="widefat">
			<thead>
			<tr>
				<th style="width:5%">#</th>
				<th style="width:25%">ラベル名</th>
				<th style="width:25%">フィールド名</th>
				<th style="width:25%">オプション有無</th>
				<th style="width:5%"></th>
			</tr>
			</thead>
			<tfoot>
			<tbody>
<?php
			$num = 1;
			foreach($ecort_profile as $profile){
?>
			<tr>
				<td><span class="circle"><?php echo $num; ?></span></td>
				<td><input type="text" name="postdata[<?php echo $num; ?>][label]" value="<?php echo $profile['label'] ?>"></td>
				<td><input type="text" name="postdata[<?php echo $num; ?>][filed]" value="<?php echo $profile['filed'] ?>"></td>
				<td>
					<input type="radio" name="postdata[<?php echo $num; ?>][option]" value="1" <?php if($profile['option'] == 1) echo "checked"; ?>> YES
					<input type="radio" name="postdata[<?php echo $num; ?>][option]" value="0" <?php if($profile['option'] == 0) echo "checked"; ?>> NO
				</td>
				<td><a class="delprofile" id="del-<?php echo $num; ?>" href="#">削 除</a></td>
			</tr>
<?php
				$num++;
			}
?>
			<tr id="es-tb-last">
				<th colspan="4"></th>
				<th><a href="#" id="es-add-btn" class="button-primary">+ フィールドを追加</a></th>
			</tr>
			</tbody>
			</tfoot>
			</table>
			<p class="submit">
				<button type="submit" class="button-primary">保 存</button>
			</p>
			</form>
		</div>
	</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	var num = <?php echo $num ?>;
	$("#es-add-btn").click(function () {
		$("#es-tb-last").before('<tr><td><span class="circle">'+ num +'</span></td><td><input type="text" name="postdata['+ num +'][label]" /></td><td><input type="text" name="postdata['+ num +'][filed]"  /></td><td><input type="radio" name="postdata['+ num +'][option]" value="1"> YES <input type="radio" name="postdata['+ num +'][option]" value="0" checked> NO</td><td></td></tr>').fadeIn(1000);
		num = num + 1;
	});
});
</script>
<?php
	}

	//------------------------------------------------
	// 
	//------------------------------------------------
	function escort_viewer()
	{
		if(isset($_POST['delete_id']))
		{
			list($a, $delete_id) = explode('-', $_POST['delete_id']);
			unset($_POST['postdata'][$delete_id]);
			unset($_POST['delete_id']);

			$post_data = json_encode($_POST['postdata']);
			update_option('escort_post_type_profile', $post_data);
			$mes =<<<__HTML__
<div id="message" class="updated below-h2">
	<p>削除しました。</p>
</div>
__HTML__;
		}
		elseif (isset($_POST['postdata'])) 
		{
			$post_data = json_encode($_POST['postdata']);
			update_option('escort_post_type_profile', $post_data);
			$mes =<<<__HTML__
<div id="message" class="updated below-h2">
	<p>登録しました。</p>
</div>
__HTML__;
		}
		$ecort_profile = json_decode(get_option('escort_post_type_profile'), true);
?>
	<div class="wrap">
		<?php echo $mes; ?>
		<h2 class="page-header">プロフィール</h2>
	  	<form action="" method="post" id="myForm">
		<div class="table-responsive">
			<table width="100%" id="es-table" class="widefat">
			<thead>
			<tr>
				<th style="width:5%">#</th>
				<th style="width:30%">ラベル名</th>
				<th style="width:30%">フィールド名</th>
				<th style="width:30%">フィールドタイプ</th>
				<th style="width:5%"></th>
			</tr>
			</thead>
			<tfoot>
			<tbody>
<?php
			$num = 1;
			foreach($ecort_profile as $profile){
?>
			<tr>
				<td><span class="circle"><?php echo $num; ?></span></td>
				<td><input type="text" name="postdata[<?php echo $num; ?>][label]" value="<?php echo $profile['label'] ?>"></td>
				<td><input type="text" name="postdata[<?php echo $num; ?>][filed]" value="<?php echo $profile['filed'] ?>"></td>
				<td>
					<select name="postdata[<?php echo $num; ?>][filed_type]">
						<option value="text" <?php if($profile['filed_type'] == "text") echo "selected"; ?>>テキスト</option>
						<option value="textarea" <?php if($profile['filed_type'] == "textarea") echo "selected"; ?>>テキストエリア</option>
						<option value="date" <?php if($profile['filed_type'] == "text") echo "selected"; ?>>日付</option>
					</select>
				</td>
				<td><a class="delprofile" id="del-<?php echo $num; ?>" href="#">削 除</a></td>
			</tr>
<?php
				$num++;
			}
?>
			<tr id="es-tb-last">
				<th colspan="4"></th>
				<th><a href="#" id="es-add-btn" class="button-primary">+ フィールドを追加</a></th>
			</tr>
			</tbody>
			</tfoot>
			</table>
			<p class="submit">
				<button type="submit" class="button-primary">保 存</button>
			</p>
			</form>
		</div>
	</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	var num = <?php echo $num ?>;
	$("#es-add-btn").click(function () {
		$("#es-tb-last").before('<tr><td><span class="circle">'+ num +'</span></td><td><input type="text" name="postdata['+ num +'][label]" /></td><td><input type="text" name="postdata['+ num +'][filed]"  /></td><td><select name="postdata['+ num  +'][filed_type]"><option value="text">テキスト</option><option value="textarea">テキストエリア</option><option value="date">日付</option></select></td><td></td></tr>').fadeIn(1000);
		num = num + 1;
	});
});
</script>
<?php
	}

	//------------------------------------------------
	//投稿ボタンを押した際のデータ更新と保存
	//------------------------------------------------
	function save_custom_postdata($post_id)
	{
		if (isset($_POST['custom_data']) && $_POST['custom_data_flg'] == 1) 
		{
			foreach ($_POST['custom_data'] as $key => $value) 
			{
				$this->save_custom_data($key, $post_id);
			}
		}
	}

	//------------------------------------------------
	//投稿データをチェックして登録する
	//------------------------------------------------
	function save_custom_data($key, $post_id)
	{
		if(isset($_POST["custom_data"][$key]))
		{
			$data = $_POST["custom_data"][$key];
		}
		else
		{
			$data = '';
		}

		//-1になると項目が変わったことになるので、項目を更新する
		if( strcmp($data, get_post_meta($post_id, $key, true)) != 0 )
		{
			update_post_meta($post_id, $key, $data);
		}
		elseif($data == "")
		{
			delete_post_meta($post_id, $key ,get_post_meta($post_id, $key ,true));
		}
	}

}
//-- /end class

?>