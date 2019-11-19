<?php
//dezend by http://www.sucaihuo.com/
class Commons
{
	/**
     * Comment: 操作成功输出方法
     * Author: zzw
     * Date: 2019/7/16 9:35
     * @param string $message
     * @param array  $data
     */
	public function renderSuccess($message = '操作成功', $data = array())
	{
		exit(json_encode(array('errno' => 0, 'message' => $message, 'data' => $data)));
	}

	/**
     * Comment: 操作失败返回内容
     * Author: zzw
     * Date: 2019/7/16 9:36
     * @param string $message
     * @param array  $data
     */
	public function renderError($message = '操作失败', $data = array())
	{
		exit(json_encode(array('errno' => 1, 'message' => $message, 'data' => $data)));
	}

	/**
     * Comment: 操作成功输出方法
     * Author: zzw
     * Date: 2019/7/16 9:35
     * @param string $message
     * @param array  $data
     */
	static public function sRenderSuccess($message = '操作成功', $data = array())
	{
		exit(json_encode(array('errno' => 0, 'message' => $message, 'data' => $data)));
	}

	/**
     * Comment: 操作失败返回内容
     * Author: zzw
     * Date: 2019/7/16 9:36
     * @param string $message
     * @param array  $data
     */
	static public function sRenderError($message = '操作失败', $data = array())
	{
		exit(json_encode(array('errno' => 1, 'message' => $message, 'data' => $data)));
	}
}

defined('IN_IA') || exit('Access Denied');

?>
