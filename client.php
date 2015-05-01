<?php
/*
OFXProxy
client.php: ログインフォームを生成する
Copyright (C) 2012-2015 OFFICE OUTERGUY. All rights reserved.
mailto:contact@beatrek.com
Dual-licensed under the GNU AGPLv3 and Beatrek Origin License.
*/

require_once("./common.inc");

$banks = array();
$creditcards = array();
$investments = array();
$prepaids = array();

// INIファイルに定義されている金融機関をtype毎に整理する
$fis = get_fi_settings();
foreach($fis as $fi) {
	switch($fi["type"]) {
	case ENV_STR_OFX_BROKER_BANK:
		array_push($banks, $fi);
		break;
	case ENV_STR_OFX_BROKER_CREDITCARD:
		array_push($creditcards, $fi);
		break;
	case ENV_STR_OFX_BROKER_INVESTMENT:
		array_push($investments, $fi);
		break;
	case ENV_STR_OFX_BROKER_PREPAID:
		array_push($prepaids, $fi);
		break;
	default:
		// 何もしない
		break;
	}
}

$title = ENV_PRODUCT_FAMILY;
$content = "";

// デバッグ機能が有効の場合、警告を表示する
if(ENV_BOOL_DEBUG == true) $content .= "<p id=\"caution\" style=\"padding: 4px 8px 4px 8px; color: #FFFFFF; background: #FF0000; font-weight: bold; text-indent: 0px;\">【警告】開発者向け（デバッグ）機能が有効のため、ログイン情報を含む詳細な記録が残ります。開発者以外の方は、ログイン情報を入力しないでください。または、開発者へご相談ください。</p>\r\n";

$content .= "<p style=\"float: right; clear: right; margin: 0px 0px 1em 1em;\"><img src=\"" . ENV_FILE_DIR_CLIENT . "wsofx.gif\" width=\"88\" height=\"31\" alt=\"We Support OFX\" /></p>\r\n";
$content .= "<p><a href=\"https://github.com/outerguy/ofxproxy/\">OFXProxy</a>は、対応金融機関の口座情報を<abbr title=\"Open Financial Exchange\">OFX</abbr>ファイルとしてダウンロードできるサービスです。生成した電子明細をOFX対応マネー管理ソフトに取り込めます。</p>\r\n";
$content .= "<p>なお、金融機関の非表示設定は、最大60日間、Cookieに保持されます。</p>\r\n";

// fiidが指定されていない場合、または指定されていたものの該当金融機関が1つも存在しなかった場合、すべての金融機関を表示する
$content .= "<ul>\r\n";
$content .= "<li><a href=\"#" . ENV_STR_OFX_BROKER_BANK . "\">銀行</a></li>\r\n";
$content .= "<li><a href=\"#" . ENV_STR_OFX_BROKER_CREDITCARD . "\">クレジットカード</a></li>\r\n";
$content .= "<li><a href=\"#" . ENV_STR_OFX_BROKER_INVESTMENT . "\">証券</a></li>\r\n";
$content .= "<li><a href=\"#" . ENV_STR_OFX_BROKER_PREPAID . "\">前払式帳票</a></li>\r\n";
$content .= "<li><a href=\"#SETTING\">金融機関の非表示設定</a></li>\r\n";
$content .= "</ul>\r\n";
$content .= "\r\n";
$content .= "<h2 id=\"LINK\">関連リンク</h2>\r\n";
$content .= "<ul>\r\n";
$content .= "<li><a href=\"http://www.beatrek.com/home/ofxproxy.htm\">プロジェクトサイト</a></li>\r\n";
$content .= "</ul>\r\n";
$content .= "\r\n";
$content .= "<h2 id=\"" . ENV_STR_OFX_BROKER_BANK . "\">銀行</h2>\r\n";
$content .= "\r\n";
foreach($banks as $bank) $content .= get_ofxform($bank);
$content .= "<h2 id=\"" . ENV_STR_OFX_BROKER_CREDITCARD . "\">クレジットカード</h2>\r\n";
$content .= "\r\n";
foreach($creditcards as $creditcard) $content .= get_ofxform($creditcard);
$content .= "<h2 id=\"" . ENV_STR_OFX_BROKER_INVESTMENT . "\">証券</h2>\r\n";
$content .= "\r\n";
foreach($investments as $investment) $content .= get_ofxform($investment);
$content .= "<h2 id=\"" . ENV_STR_OFX_BROKER_PREPAID . "\">前払式帳票</h2>\r\n";
$content .= "\r\n";
foreach($prepaids as $prepaid) $content .= get_ofxform($prepaid);
$content .= "<h2 id=\"SETTING\">金融機関の非表示設定</h2>\r\n";
$content .= "<form action=\"javascript: void(0);\">\r\n";
$content .= "<ul>";
$content .= "<li><label for=\"all_hide\"><input type=\"checkbox\" name=\"all_hide\" id=\"all_hide\" onchange=\"var inputs = self.document.getElementsByTagName(&quot;input&quot;); var i; for(i in inputs) if(inputs[i].type == &quot;checkbox&quot; &amp;&amp; inputs[i].id != &quot;all_hide&quot;) { inputs[i].checked = this.checked; self.document.getElementById(inputs[i].id.replace(&quot;_hide&quot;, &quot;&quot;)).style.display = (this.checked == true? &quot;none&quot;: &quot;block&quot;); } save_cookie_ofxform(); self.document.getElementById(&quot;all_hide&quot;).focus();\" /><strong>すべて選択/解除</strong></label></li>\r\n";
foreach($fis as $fi) {
	$content .= "<li><label for=\"" . $fi["fiid"] . "_hide\"><input type=\"checkbox\" name=\"" . $fi["fiid"] . "_hide\" id=\"" . $fi["fiid"] . "_hide\" onchange=\"self.document.getElementById(&quot;" . $fi["fiid"] . "&quot;).style.display = (this.checked == true? &quot;none&quot;: &quot;block&quot;); save_cookie_ofxform(); self.document.getElementById(&quot;" . $fi["fiid"] . "_hide&quot;).focus();\" />" . $fi["name"] . "</label></li>\r\n";
}
$content .= "</ul>\r\n";
$content .= "</form>\r\n";
$content .= "\r\n";

$html = str_replace(array("<!--[title]-->", "<!--[content]-->"), array(trim($title), trim($content)), file_get_contents(ENV_FILE_DIR_CLIENT . ENV_FILE_TEMPLATE_HTML));
header("HTTP/1.0 200 OK");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");
// header("Content-Length: " . strlen($html));
echo $html;

function get_ofxform($fi) {
	$ret = "";
	$ret .= "<div id=\"" . $fi["fiid"] . "\">\r\n";
	$ret .= "<h3><a href=\"" . $fi["home"] . "\" class=\"ofxlink\" onclick=\"return link_ofxform(this);\" onkeypress=\"this.onclick();\">" . $fi["name"] . "</a></h3>\r\n";
	$ret .= "<form method=\"post\" action=\"./server.php?fiid=" . $fi["fiid"] . "\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"Shift_JIS\" class=\"ofxform\" onsubmit=\"return exec_ofxform(this);\">\r\n";
	$ret .= "<dl>\r\n";
	$forms = explode("|", $fi["form"]);
	foreach($forms as $form) {
		list($text, $attr) = explode("|", $fi[$form], 2);
		$attr = strtolower($attr);
		switch($attr) {
		case "text":
		case "password":
			break;
		default:
			$attr = "text";
			break;
		}
		$ret .= "<dt>" . $text . "</dt>\r\n";
		$ret .= "<dd><input type=\"" . $attr . "\" name=\"" . $fi["fiid"] . "_" . $form . "\" value=\"\" size=\"16\" maxlength=\"256\" class=\"ofxinput\" /></dd>\r\n";
	}
	$ret .= "</dl>\r\n";
	$ret .= "<div class=\"ofximage\"><input type=\"hidden\" name=\"fiid\" value=\"" . $fi["fiid"] . "\" /><img src=\"" . ENV_FILE_DIR_CLIENT . "btn_1.gif\" width=\"107\" height=\"40\" alt=\"We Support OFXロゴ\" /><input type=\"image\" src=\"" . ENV_FILE_DIR_CLIENT . "btn_2.gif\" alt=\"明細をダウンロードする\" style=\"width: 152px; height: 40px;\" /><a href=\"" . ENV_FILE_DIR_CLIENT . "help.html#" . $fi["fiid"] . "\" class=\"ofxlink\" onclick=\"return help_ofxform(this);\" onkeypress=\"this.onclick();\"><img src=\"" . ENV_FILE_DIR_CLIENT . "btn_5.gif\" width=\"61\" height=\"40\" alt=\"ヘルプ\" /></a></div>\r\n";
	$ret .= "</form>\r\n";
	$ret .= "</div>\r\n";
	$ret .= "\r\n";
	return $ret;
}

?>
