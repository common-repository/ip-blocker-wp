<?php
//On créé le menu

function menu_ipblocker() {
add_menu_page('IP Blocker WP', 'IP Blocker WP', 'manage_options', 'ipb', 'ipb', plugins_url('ip-blocker-wp/ip-blocker-wp.png') );
}
add_action('admin_menu', 'menu_ipblocker');


//On créé les tables mysql
function creer_table_ipb() {
global $wpdb;
$table_ipb = $wpdb->prefix . 'ipb';
$sql_ipb = "CREATE TABLE IF NOT EXISTS $table_ipb (
id_ipb int(11) NOT NULL AUTO_INCREMENT,
ip text DEFAULT NULL,
ban_date date DEFAULT NULL,
unban_date date DEFAULT NULL,
UNIQUE KEY id (id_ipb)
);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//dbDelta( $sql_ipb );
$wpdb->query($sql_ipb);
}

function creer_table_ipb_p() {
global $wpdb;
$table_ipb_p = $wpdb->prefix . 'ipb_p';
$sql_ipb_p = "CREATE TABLE IF NOT EXISTS $table_ipb_p (
id_ipb_p int(11) NOT NULL AUTO_INCREMENT,
url_redirect text DEFAULT NULL,
email text DEFAULT NULL,
alerte_email text DEFAULT NULL,
alerte_sms text DEFAULT NULL,
id_freemobile text DEFAULT NULL,
cle_freemobile text DEFAULT NULL,
UNIQUE KEY id (id_ipb_p)
);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$wpdb->query($sql_ipb_p);
}

//On insere les infos dans la table IPB
function insert_table_ipb_p() {
global $wpdb;
$table_ipb_p = $wpdb->prefix . 'ipb_p';
$wpdb->insert( 
$table_ipb_p, 
array('id_ipb_p'=>' ','url_redirect'=>'http://www.google.fr','email'=>get_option(admin_email),'alerte_email'=>'ON','alerte_sms'=>'OFF','id_freemobile'=>'xxxxxxxxx','cle_freemobile'=>'xxxxxxxxx'), 
array('%s','%s','%s','%s','%s','%s','%s')
);
}

?>