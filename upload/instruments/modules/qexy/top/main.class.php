<?php
/**
 * Top balance module for WebMCR
 *
 * Main class
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
		$this->mcfg			= $api->getMcrConfig();
	}

	private function user_array(){

		$start		= $this->api->pagination($this->cfg['rop_list'], 0, 0); // Set start pagination

		$end		= $this->cfg['rop_list']; // Set end pagination

		$bd_names		= $this->mcfg['bd_names'];
		$bd_users		= $this->mcfg['bd_users'];
		$bd_money		= $this->mcfg['bd_money'];
		$site_ways		= $this->mcfg['site_ways'];

		$money = ($this->cfg['use_real']) ? $this->cfg['real_column'] : $bd_money['money'];

		$query = $this->db->query("SELECT `i`.`{$bd_money['login']}`, `i`.`$money`, `u`.`{$bd_users['female']}`, `u`.`{$bd_users['id']}`, `u`.default_skin, `g`.`name` AS `group`

									FROM `{$bd_names['iconomy']}` AS `i`

									LEFT JOIN `{$bd_names['users']}` AS `u`
										ON `u`.`{$bd_users['login']}` = `i`.`{$bd_money['login']}`

									LEFT JOIN `{$bd_names['groups']}` AS `g`
										ON `g`.id=`u`.`{$bd_users['group']}`

									ORDER BY `i`.`$money` DESC

									LIMIT $start,$end");

		if(!$query || $this->db->num_rows($query)<=0){ return $this->api->sp("main/user-none.html"); } // Check returned result

		ob_start();

		while($ar = $this->db->get_row($query)){

			$login = $this->db->HSC($ar[$bd_money['login']]);

			$group = (empty($ar['group'])) ? '<b class="text-error">Группа удалена</b>' : $this->db->HSC($ar['group']);

			$class = (intval($ar[$bd_users['female']])==0) ? '' : 'row-female';

			$charname = (intval($ar[$bd_users['female']])==0) ? 'Char_Mini.png' : 'Char_Mini_female.png';

			$avatar = (intval($ar['default_skin'])===1) ? 'default/'.$charname.'?refresh='.mt_rand(1000, 9999) : $login.'_Mini.png';

			$data = array(
				"ID"		=> intval($ar[$bd_users['id']]),
				"LOGIN"		=> $login,
				"GROUP"		=> $group,
				"USER_URL"	=> ($this->cfg['use_us']) ? BASE_URL.'?mode=users&uid='.$login : '#',
				"AVATAR"	=> BASE_URL.$site_ways['mcraft'].'/tmp/skin_buffer/'.$avatar,
				"MONEY"		=> floatval($ar[$money]),
				"NAME"		=> ($this->cfg['use_real']) ? $this->cfg['real_name'] : $this->cfg['unreal_name'],
				"CLASS"		=> $class
			);

			echo $this->api->sp("main/user-id.html", $data);
		}

		return ob_get_clean();
	}

	private function user_list(){

		$array = array(
			"Главная" => BASE_URL,
			$this->cfg['title'] => MOD_URL,
		);

		$this->bc		= $this->api->bc($array); // Set breadcrumbs
		$this->title	= 'Главная';

		$bd_names		= $this->mcfg['bd_names'];

		$sql			= "SELECT COUNT(*) FROM `{$bd_names['iconomy']}`"; // Set SQL query for pagination function

		$page			= "&pid="; // Set url for pagination function

		$pagination		= $this->api->pagination($this->cfg['rop_list'], $page, $sql); // Set pagination

		$list			= $this->user_array(); // Set content to variable

		$data = array(
			"PAGINATION"	=> $pagination,
			"CONTENT"		=> $list
		);

		return $this->api->sp('main/user-list.html', $data);
	}

	public function _list(){
		return $this->user_list();
	}
}

/**
 * Top balance module for WebMCR
 *
 * Main class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.0.0
 *
 */
?>