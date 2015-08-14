<?php
/**
 * Top balance module for WebMCR
 *
 * Admin class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.0.0
 *
 */

// Check Qexy constant
if (!defined('QEXY')){ exit("Hacking Attempt!"); }

class module{

	// Set default variables
	private $user			= false;
	private $db				= false;
	private $api			= false;
	public	$title			= '';
	public	$bc				= '';
	private	$cfg			= array();

	// Set constructor vars
	public function __construct($api){
		$this->user			= $api->user;
		$this->db			= $api->db;
		$this->cfg			= $api->cfg;
		$this->api			= $api;

		if($this->user->lvl < $this->cfg['lvl_admin']){
			$this->api->notify("Доступ запрещен!", "", "403", 3);
		}
	}

	public function _list(){
		$f_security		= 'top_settings';

		if($_SERVER['REQUEST_METHOD']=='POST'){
			if(!$this->api->csrf_check($f_security)){ $this->api->notify("Hacking Attempt!", "&do=403", "403", 3); }

			$this->cfg['title'] = $this->db->HSC(strip_tags(@$_POST['title']));
			$this->cfg['lvl_access'] = intval(@$_POST['lvl_access']);
			$this->cfg['lvl_admin'] = intval(@$_POST['lvl_admin']);
			$this->cfg['rop_list'] = (intval(@$_POST['rop_list'])<=0) ? 1 : intval(@$_POST['rop_list']);
			$this->cfg['use_us'] = (intval(@$_POST['use_us'])===1) ? true : false;
			$this->cfg['use_real'] = (intval(@$_POST['use_real'])===1) ? true : false;
			$this->cfg['unreal_name'] = $this->db->HSC(strip_tags(@$_POST['unreal_name']));
			$this->cfg['real_name'] = $this->db->HSC(strip_tags(@$_POST['real_name']));

			if(!$this->api->savecfg($this->cfg, 'configs/top.cfg.php')){
				$this->api->notify("Произошла ошибка сохранения настроек", "&do=admin", "Ошибка!", 3);
			}
			
				$this->api->notify("Настройки успешно сохранены", "&do=admin", "Поздравляем!", 1);

		}

		$array = array(
			"Главная" => BASE_URL,
			$this->cfg['title'] => MOD_URL,
			"Панель управления" => MOD_URL.'&do=admin',
		);

		$this->bc		= $this->api->bc($array); // Set breadcrumbs
		$this->title	= "Панель управления";

		$data = array(
			"USE_US" => ($this->cfg['use_us']===true) ? 'selected' : '',
			"USE_REAL" => ($this->cfg['use_real']===true) ? 'selected' : '',
			"F_SET"			=> $this->api->csrf_set($f_security),
			"F_SECURITY"	=> $f_security,
		);

		return $this->api->sp('admin/settings_main.html', $data);
	}

}

/**
 * Top balance module for WebMCR
 *
 * Admin class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.0.0
 *
 */
?>