<?php
//dezend by http://www.sucaihuo.com/
function getAllPluginsName()
{
	return array('Rush', 'Merchant', 'wlCoupon', 'halfcard', 'Wlfightgroup', 'Pocket');
}

function createUniontid()
{
	global $_W;
	$moduleid = pdo_getcolumn('modules', array('name' => 'weliam_merchant'), 'mid');
	$moduleid = empty($moduleid) ? '000000' : sprintf('%06d', $moduleid);
	$uniontid = date('YmdHis') . $moduleid . random(8, 1);
	return $uniontid;
}

function m($filename = '')
{
	static $_modules = array();

	if (-1 < strpos($filename, '/')) {
		list($file, $name) = explode('/', $filename);
	}
	else {
		exit('文件结构不正确，正确结构（文件夹名/文件名）');
	}

	if (isset($_modules[$file][$name])) {
		return $_modules[$file][$name];
	}

	$model = PATH_CORE . 'library/' . $file . '/' . $name . '.lib.php';

	if (!is_file($model)) {
		exit('Library Class ' . $filename . ' Not Found!');
	}

	require $model;
	$class_name = ucfirst($name);
	$_modules[$file][$name] = new $class_name();
	return $_modules[$file][$name];
}

function p($name = '')
{
	$model = PATH_PLUGIN . strtolower($name) . '/config.xml';

	if (!is_file($model)) {
		return false;
	}

	return true;
}

function checkLimit($roleid, $arr = array())
{
	$allPerms = Perms::allParms();

	if (empty($allPerms[$arr['p']][$arr['ac']][$arr['do']])) {
		return true;
	}

	$limits = Perms::getRolePerms($roleid);
	if (empty($limits) || empty($arr)) {
		return false;
	}

	if (empty($limits[$arr['p']][$arr['ac']][$arr['do']])) {
		return false;
	}

	return true;
}

function web_url($segment, $params = array())
{
	global $_W;
	list($p, $ac, $do) = explode('/', $segment);

	if (defined('IN_WEB')) {
		$url = $_W['siteroot'] . 'web/agent.php?';
	}
	else {
		$url = $_W['siteroot'] . 'web/index.php?c=site&a=entry&m=' . MODULE_NAME . '&';
	}

	if (!empty($p)) {
		$url .= 'p=' . $p . '&';
	}

	if (!empty($ac)) {
		$url .= 'ac=' . $ac . '&';
	}

	$do = !empty($do) ? $do : 'index';

	if (!empty($do)) {
		$url .= 'do=' . $do . '&';
	}

	if (!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
	}

	return $url;
}

function app_url($segment, $params = array(), $addhost = true)
{
	global $_W;
	list($p, $ac, $do) = explode('/', $segment);

	if ($addhost == true) {
		$_W['siteroot'] = str_replace(array('/addons/' . MODULE_NAME, '/core/common', '/addons/bm_payms'), '', $_W['siteroot']);
		$url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=' . MODULE_NAME . '&';
	}
	else {
		$url = './index.php?i=' . $_W['uniacid'] . '&c=entry&m=' . MODULE_NAME . '&';
	}

	if (!empty($p)) {
		$url .= 'p=' . $p . '&';
	}

	if (!empty($ac)) {
		$url .= 'ac=' . $ac . '&';
	}

	$do = !empty($do) ? $do : 'index';

	if (!empty($do)) {
		$url .= 'do=' . $do . '&';
	}

	if (!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
	}

	if (substr($url, -1) == '&') {
		$url = substr($url, 0, strlen($url) - 1);
	}

	return $url;
}

function h5_url($page, $params = array(), $addhost = false)
{
	global $_W;
	$_W['siteroot'] = str_replace(array('/addons/' . MODULE_NAME, '/core/common', '/addons/bm_payms'), '', $_W['siteroot']);
	$is_have = strpos($page, '?');

	if ($is_have) {
		$info = explode('?', $page);
		$page = $info[0];
		$paramStr = $info[1];
	}

	$url = $_W['siteroot'] . 'addons/' . MODULE_NAME . '/h5/#/' . $page . '?i=' . $_W['uniacid'] . '&' . $paramStr;

	if (!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
	}

	return $url;
}

function is_agent()
{
	if (defined('IN_WEB')) {
		return true;
	}

	return false;
}

function ifilter_url($params)
{
	return wl_filter_url($params);
}

function wl_setcookie($key, $value, $expire = 0, $httponly = false)
{
	global $_W;
	$expire = $expire != 0 ? TIMESTAMP + $expire : 0;
	$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
	$value = is_array($value) ? base64_encode(json_encode($value)) : $value;
	return setcookie('weliam_' . $key, $value, $expire, $_W['config']['cookie']['path'], $_W['config']['cookie']['domain'], $secure, $httponly);
}

function wl_getcookie($key)
{
	$sting = $_COOKIE['weliam_' . $key];
	$data = json_decode(base64_decode($sting), true);
	return is_array($data) ? $data : $sting;
}

function wl_json($errno, $message = '', $data = '')
{
	exit(json_encode(array('errno' => $errno, 'message' => $message, 'data' => $data)));
}

function wl_new_method($plugin, $controller, $method, $catalog = 'app')
{
	global $_W;
	$dir = IA_ROOT . '/addons/' . MODULE_NAME . '/';
	$file = $dir . $catalog . '/controller/' . $plugin . '/' . $controller . '.ctrl.php';

	if (!is_file($file)) {
		$file = $dir . 'plugin/' . $plugin . '/' . $catalog . '/controller/' . $controller . '.ctrl.php';
	}

	if (($catalog == 'web' || $catalog == 'sys') && !is_file($file)) {
		$_W['catalog'] = $catalog = $catalog == 'web' ? 'sys' : 'web';
		$file = $dir . $catalog . '/controller/' . $plugin . '/' . $controller . '.ctrl.php';

		if (!is_file($file)) {
			$file = $dir . 'plugin/' . $plugin . '/' . $catalog . '/controller/' . $controller . '.ctrl.php';
		}
	}

	if (is_file($file)) {
		require_once $file;
	}
	else {
		trigger_error('访问的模块 ' . $plugin . ' 不存在.', 512);
	}

	$class = ucfirst($controller) . '_WeliamController';

	if (class_exists($class, false)) {
		$instance = new $class();
	}
	else {
		$instance = new $controller();
	}

	if ($catalog == 'app') {
		$instance->inMobile = true;
	}

	if (!method_exists($instance, $method)) {
		trigger_error('控制器 ' . $controller . ' 方法 ' . $method . ' 未找到!');
	}

	$instance->{$method}();
	exit();
}

function wl_template($filename, $flag = '')
{
	global $_W;
	$name = MODULE_NAME;
	if (defined('IN_SYS') || defined('IN_WEB')) {
		$catalog = strstr($filename, 'common/') ? 'web' : $_W['catalog'];
		$source = IA_ROOT . ('/addons/' . $name . '/' . $catalog . '/view/default/' . $filename . '.html');
		$compile = IA_ROOT . ('/data/tpl/' . $catalog . '/' . $name . '/' . $catalog . '/view/default/' . $filename . '.tpl.php');

		if (!is_file($source)) {
			$source = IA_ROOT . ('/addons/' . $name . '/plugin/' . $_W['plugin'] . '/' . $catalog . '/view/default/' . $filename . '.html');
		}
	}

	if (defined('IN_APP')) {
		$template = 'default';

		if (!empty($_W['wlsetting']['templat']['appview'])) {
			$template = $_W['wlsetting']['templat']['appview'];
		}

		$source = IA_ROOT . ('/addons/' . $name . '/app/view/' . $template . '/' . $filename . '.html');
		$compile = IA_ROOT . ('/data/tpl/app/' . $name . '/app/view/' . $template . '/' . $filename . '.tpl.php');

		if (!is_file($source)) {
			$source = IA_ROOT . ('/addons/' . $name . '/app/view/default/' . $filename . '.html');
		}

		if (!is_file($source)) {
			$source = IA_ROOT . ('/addons/' . $name . '/plugin/' . $_W['plugin'] . '/app/view/' . $template . '/' . $filename . '.html');
		}

		if (!is_file($source)) {
			$source = IA_ROOT . ('/addons/' . $name . '/plugin/' . $_W['plugin'] . '/app/view/default/' . $filename . '.html');
		}

		if (!is_file($source)) {
			$filenames = explode('/', $filename);
			$source = IA_ROOT . ('/addons/' . $name . '/plugin/' . $filenames[0] . '/app/view/default/' . $filename . '.html');
		}

		if (!empty($_W['wlsetting']['trade']['credittext']) || !empty($_W['wlsetting']['trade']['moneytext'])) {
			$compile = IA_ROOT . ('/data/tpl/app/' . $name . '/' . $_W['uniacid'] . '/app/view/' . $template . '/' . $filename . '.tpl.php');
		}

		if (!empty($_W['wlsetting']['halfcard']['text']['halfcardtext']) || !empty($_W['wlsetting']['halfcard']['text']['privilege'])) {
			$compile = IA_ROOT . ('/data/tpl/app/' . $name . '/' . $_W['uniacid'] . '/app/view/' . $template . '/' . $filename . '.tpl.php');
		}
	}

	if (!is_file($source)) {
		exit('Error: template source \'' . $filename . '\' is not exist!!!');
	}

	if (!is_file($compile) || filemtime($compile) < filemtime($source)) {
		Template::wl_template_compile($source, $compile, true);
	}

	if ($flag == TEMPLATE_FETCH) {
		extract($GLOBALS, EXTR_SKIP);
		ob_end_flush();
		ob_clean();
		ob_start();
		include $compile;
		$contents = ob_get_contents();
		ob_clean();
		return $contents;
	}

	if ($flag == 'template') {
		extract($GLOBALS, EXTR_SKIP);
		return $compile;
	}

	return $compile;
}

function wl_message($msg, $redirect = '', $type = '')
{
	global $_W;
	global $_GPC;

	if ($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	}

	if ($redirect == 'referer') {
		$redirect = referer();
	}

	if ($redirect == 'close') {
		$redirect = 'wx.closeWindow()';
		$close = 1;
	}

	if ($redirect == '') {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info';
	}
	else {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'success';
	}

	if (IN_WXAPP == 'wxapp') {
		exit(json_encode(array('errno' => $type == 'success' ? 0 : 1, 'message' => $msg, 'data' => $redirect)));
	}

	if ($_W['isajax'] || !empty($_GET['isajax']) || $type == 'ajax') {
		if ($type != 'ajax' && !empty($_GPC['target'])) {
			exit('
<script type="text/javascript">
parent.require([\'jquery\', \'util\'], function($, util){
	var url = ' . (!empty($redirect) ? 'parent.location.href' : '\'\'') . ';
	var modalobj = util.message(\'' . $msg . '\', \'\', \'' . $type . '\');
	if (url) {
		modalobj.on(\'hide.bs.modal\', function(){$(\'.modal\').each(function(){if($(this).attr(\'id\') != \'modal-message\') {$(this).modal(\'hide\');}});top.location.reload()});
	}
});
</script>');
		}
		else {
			if ($_W['isajax'] && !empty($_GPC['token'])) {
				$ret = array('status' => $type == 'success' ? 1 : 0, 'result' => $type == 'success' ? array('url' => $redirect ? $redirect : referer()) : array());
				$ret['result']['message'] = $msg;
				exit(json_encode($ret));
			}
			else {
				$vars = array();

				if (is_array($msg)) {
					$vars['errno'] = $msg['errno'];
					$vars['message'] = $msg['message'];
					exit(json_encode($vars));
				}
				else {
					$vars['message'] = $msg;
				}

				$vars['redirect'] = $redirect;
				$vars['type'] = $type;
				exit(json_encode($vars));
			}
		}
	}
	else {
		if (is_array($msg)) {
			$msg = $msg['message'];
		}
	}

	if (empty($msg) && !empty($redirect)) {
		header('location: ' . $redirect);
	}

	$label = $type;

	if ($type == 'error') {
		$label = 'danger';
	}

	if ($type == 'ajax' || $type == 'sql') {
		$label = 'warning';
	}

	include wl_template('common/message', TEMPLATE_INCLUDEPATH);
	exit();
}

function wl_debug($value)
{
	echo '<br><pre>';
	print_r($value);
	echo '</pre>';
	exit();
}

function wl_log($filename, $title, $data)
{
	global $_W;

	if ($uniacid != '') {
		$_W['uniacid'] = $uniacid;
	}

	$url_log = PATH_DATA . 'log/' . date('Y-m-d', time()) . '/' . $filename . '.log';
	$url_dir = PATH_DATA . 'log/' . date('Y-m-d', time());
	Util::mkDirs($url_dir);
	file_put_contents($url_log, var_export('/=======' . date('Y-m-d H:i:s', time()) . '【' . $title . '】=======/', true) . PHP_EOL, FILE_APPEND);
	file_put_contents($url_log, var_export($data, true) . PHP_EOL, FILE_APPEND);
}

function wl_filter_url($params)
{
	global $_W;

	if (empty($params)) {
		return '';
	}

	$query_arr = array();
	$parse = parse_url($_W['siteurl']);

	if (!empty($parse['query'])) {
		$query = $parse['query'];
		parse_str($query, $query_arr);
	}

	$params = explode(',', $params);

	foreach ($params as $val) {
		if (!empty($val)) {
			$data = explode(':', $val);
			$query_arr[$data[0]] = trim($data[1]);
		}
	}

	$query_arr['page'] = 1;
	$query = http_build_query($query_arr);
	return defined('IN_WEB') ? './agent.php?' . $query : './index.php?' . $query;
}

function wl_tpl_form_field_member($value = array())
{
	$s = '';
	$s = '
		<script type="text/javascript">
			function search_members() {
	       	if( $.trim($("#search-kwd").val())==""){
	            Tip.focus("#search-kwd","请输入关键词");
	            return;
	        }
	
			$("#module-menus").html("正在搜索....");
			$.get("' . web_url('member/wlMember/selectMember') . '", {
				keyword: $.trim($("#search-kwd").val())
			}, function(dat){
				$("#module-menus").html(dat);
			});
		}
	    function select_member(o) {
			$("#openid").val(o.openid);
			$("#saler").val(o.nickname);
			$("#search-kwd").val(o.nickname);
			$("#module-menus").html("");
			$("#myModal").modal("hide");
			$(".modal-backdrop").remove();
		}
		</script>';
	$s .= '
		<div class="form-group">
        	<label class="col-sm-2 control-label">选择微信账号</label>
            <div class="col-sm-9">
                <input type="hidden" id="openid" name="openid" value="' . $value['openid'] . '" />
                <div class="input-group">
                    <input type="text" name="nickname" maxlength="30" value="' . $value['nickname'] . '" id="saler" class="form-control" readonly />
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#myModal">选择微信账号</button>
                    </div>
                </div>
      			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" style="width: 660px;">
                        <div class="modal-content">
                            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择微信账号</h3></div>
                            <div class="modal-body" >
                                <div class="row">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd" placeholder="可搜索微信昵称，openid，UID" />
                                        <span class="input-group-btn"><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                    </div>
                                </div>
                               	<div id="module-menus" style="padding-top:5px;"></div>
                            </div>
                           	<div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                        </div>
                    </div>
                </div>
        	</div>
		</div>
		';
	return $s;
}

function qr($url)
{
	global $_W;

	if (empty($url)) {
		return false;
	}

	m('qrcode/QRcode')->png($url, false, QR_ECLEVEL_H, 4);
}

function puv()
{
	global $_W;
	if ($_W['uniacid'] <= 0 || empty($_W['areaid'])) {
		return NULL;
	}

	$puv = pdo_getcolumn(PDO_NAME . 'puv', array('uniacid' => $_W['uniacid'], 'date' => date('Ymd'), 'areaid' => $_W['areaid']), 'id');

	if (empty($puv)) {
		pdo_insert(PDO_NAME . 'puv', array('areaid' => $_W['areaid'], 'uniacid' => $_W['uniacid'], 'pv' => 0, 'uv' => 0, 'date' => date('Ymd')));
		$puv = pdo_insertid();
	}

	pdo_query('UPDATE ' . tablename(PDO_NAME . 'puv') . (' SET `pv` = `pv` + 1 WHERE id = ' . $puv));

	if ($_W['mid']) {
		$myp = pdo_getcolumn(PDO_NAME . 'puvrecord', array('uniacid' => $_W['uniacid'], 'date' => date('Ymd'), 'mid' => $_W['mid'], 'areaid' => $_W['areaid']), 'id');

		if (empty($myp)) {
			pdo_query('UPDATE ' . tablename(PDO_NAME . 'puv') . (' SET `uv` = `uv` + 1 WHERE id = ' . $puv));
			pdo_insert(PDO_NAME . 'puvrecord', array('areaid' => $_W['areaid'], 'uniacid' => $_W['uniacid'], 'pv' => 0, 'mid' => $_W['mid'], 'date' => date('Ymd')));
			$myp = pdo_insertid();
		}

		pdo_query('UPDATE ' . tablename(PDO_NAME . 'puvrecord') . (' SET `pv` = `pv` + 1 WHERE id = ' . $myp));
	}
}

function checkshare()
{
	global $_W;

	if ($_W['controller'] == 'supervise') {
		return 1;
	}

	if ($_W['method'] == 'orderlist') {
		return 1;
	}

	if ($_W['controller'] == 'home' && $_W['method'] == 'paySuccess') {
		return 1;
	}

	if ($_W['controller'] == 'coupon_app' && $_W['method'] == 'couponDetail') {
		return 1;
	}

	if ($_W['controller'] == 'fightapp' && $_W['method'] == 'expressorder') {
		return 1;
	}

	return 0;
}

defined('IN_IA') || exit('Access Denied');

if (!function_exists('is_wxapp')) {
	function is_wxapp()
	{
		global $_W;
		if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'miniProgram')) {
			return true;
		}

		return false;
	}
}

if (!function_exists('is_weixin')) {
	function is_weixin()
	{
		global $_W;
		if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
			return false;
		}

		return true;
	}
}

if (!function_exists('is_h5app')) {
	function is_h5app()
	{
		if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'CK 2.0')) {
			return true;
		}

		return false;
	}
}

if (!function_exists('is_ios')) {
	function is_ios()
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
			return true;
		}

		return false;
	}
}

if (!function_exists('is_mobile')) {
	function is_mobile()
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/(android|bb\\d+|meego).+mobile|avantgo|bada\\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\\-(n|u)|c55\\/|capi|ccwa|cdm\\-|cell|chtm|cldc|cmd\\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\\-s|devi|dica|dmob|do(c|p)o|ds(12|\\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\\-|_)|g1 u|g560|gene|gf\\-5|g\\-mo|go(\\.w|od)|gr(ad|un)|haie|hcit|hd\\-(m|p|t)|hei\\-|hi(pt|ta)|hp( i|ip)|hs\\-c|ht(c(\\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\\-(20|go|ma)|i230|iac( |\\-|\\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\\/)|klon|kpt |kwc\\-|kyo(c|k)|le(no|xi)|lg( g|\\/(k|l|u)|50|54|\\-[a-w])|libw|lynx|m1\\-w|m3ga|m50\\/|ma(te|ui|xo)|mc(01|21|ca)|m\\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\\-2|po(ck|rt|se)|prox|psio|pt\\-g|qa\\-a|qc(07|12|21|32|60|\\-[2-7]|i\\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\\-|oo|p\\-)|sdk\\/|se(c(\\-|0|1)|47|mc|nd|ri)|sgh\\-|shar|sie(\\-|m)|sk\\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\\-|v\\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\\-|tdg\\-|tel(i|m)|tim\\-|t\\-mo|to(pl|sh)|ts(70|m\\-|m3|m5)|tx\\-9|up(\\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\\-|your|zeto|zte\\-/i', substr($useragent, 0, 4))) {
			return true;
		}

		return false;
	}
}

if (!function_exists('show_json')) {
	function show_json($status = 1, $return = NULL)
	{
		$ret = array('status' => $status, 'result' => $status == 1 ? array('url' => referer()) : array());

		if (!is_array($return)) {
			if ($return) {
				$ret['result']['message'] = $return;
			}

			exit(json_encode($ret));
		}
		else {
			$ret['result'] = $return;
		}

		if (isset($return['url'])) {
			$ret['result']['url'] = $return['url'];
		}
		else {
			if ($status == 1) {
				$ret['result']['url'] = referer();
			}
		}

		exit(json_encode($ret));
	}
}

if (!function_exists('array_column')) {
	function array_column($input, $column_key, $index_key = NULL)
	{
		$arr = array();

		foreach ($input as $d) {
			if (!isset($d[$column_key])) {
				return NULL;
			}

			if ($index_key !== NULL) {
				return array($d[$index_key] => $d[$column_key]);
			}

			$arr[] = $d[$column_key];
		}

		if ($index_key !== NULL) {
			$tmp = array();

			foreach ($arr as $ar) {
				$tmp[key($ar)] = current($ar);
			}

			$arr = $tmp;
		}

		return $arr;
	}
}

?>
