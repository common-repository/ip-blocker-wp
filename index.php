<?php
/**
 * Plugin Name: IP Blocker WP par JM Créa
 * Plugin URI: http://www.jm-crea.com
 * Description: Bloquez des adresses IP qui essaient de se rendre sur votre site.
 * Version: 1.0
 * Author: JM Crea
 * Author URI: http://www.jm-crea.com
 */
 
/* INSTALLATION DU PLUGIN */ 
include( plugin_dir_path( __FILE__ ) . 'includes/install.php');
register_activation_hook(__FILE__, 'creer_table_ipb');
register_activation_hook(__FILE__, 'creer_table_ipb_p');
register_activation_hook(__FILE__, 'insert_table_ipb_p');

/* APPEL DU CSS */
add_action( 'admin_enqueue_scripts', 'style_ipb' );
function style_ipb() {
wp_register_style('css_ipb', plugins_url( 'css/style.css', __FILE__ ));
wp_enqueue_style('css_ipb');	
}


function ipb() {	
echo "<h1>IP Blocker WP</h1>
<h2>Bloquez des adresses IP</h2>";
echo '<p>Bloquez les adresses IP de certains visiteurs ou robots. Ils ne pourront plus accéder à votre site. </p>';

if (isset($_GET['action'])&&($_GET['action'] == 'add')) {
echo '<div class="updated"><p>IP blacklistée avec succès !</p></div>';		
}
if (isset($_GET['action'])&&($_GET['action'] == 'maj-ip')) {
echo '<div class="updated"><p>Ban modifié avec succès !</p></div>';		
}
if (isset($_GET['action'])&&($_GET['action'] == 'del-ip')) {
echo '<div class="updated"><p>Ban supprimé avec succès !</p></div>';		
}
if (isset($_GET['action'])&&($_GET['action'] == 'maj-param')) {
echo '<div class="updated"><p>Paramètres mis à jour avec succès !</p></div>';		
}
echo '<div class="wrap">
<h2 class="nav-tab-wrapper">';
if ( (isset($_GET['tab']))&&($_GET['tab'] == 'ipb') ) {
echo '<a class="nav-tab nav-tab-active" href="' . admin_url() . 'admin.php?page=ipb&tab=ipb">IP Bloquées</a>';
}
else {
echo '<a class="nav-tab" href="' . admin_url() . 'admin.php?page=ipb&tab=ipb">IP Bloquées</a>';
}
if ( (isset($_GET['tab']))&&($_GET['tab'] == 'param') ) {
echo '<a class="nav-tab nav-tab-active" href="' . admin_url() . 'admin.php?page=ipb&tab=param">Paramètres</a>';
}
else {
echo '<a class="nav-tab" href="' . admin_url() . 'admin.php?page=ipb&tab=param">Paramètres</a>';	
}

if ( (isset($_GET['tab']))&&($_GET['tab'] == 'aide') ) {
echo '<a class="nav-tab nav-tab-active" href="' . admin_url() . 'admin.php?page=ipb&tab=aide">Aide Free Mobile</a>';
}
else {
echo '<a class="nav-tab" href="' . admin_url() . 'admin.php?page=ipb&tab=aide">Aide Free Mobile</a>';	
}
if ( (isset($_GET['tab']))&&($_GET['tab'] == 'autres_plugins') ) {
echo '<a class="nav-tab nav-tab-active" href="' . admin_url() . 'admin.php?page=ipb&tab=autres_plugins">Nos autres plugins</a>';
}
else {
echo '<a class="nav-tab" href="' . admin_url() . 'admin.php?page=ipb&tab=autres_plugins">Nos autres plugins</a>';	
}
echo '</h2></div>';



if ( (isset($_GET['tab']))&&($_GET['tab'] == 'ipb') ) {
echo "<h1>Bloquer des adresses IP</h1>";
echo '
<form name="form1" method="post" action=""> 
<input type="text" name="ip" id="ip" onfocus="if(this.value == \'Adresse IP\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Adresse IP\'; }" value="Adresse IP"> 
<small>(dd-mm-YYYY)</small> <input type="text" name="ban_date" id="ban_date" onfocus="if(this.value == \'Date du ban\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Date du ban\'; }" value="Date du ban">
<small>(dd-mm-YYYY)</small> <input type="text" name="unban_date" id="unban_date" onfocus="if(this.value == \'Date du déban\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'Date du déban\'; }" value="Date du déban">
 <input type="submit" name="ajouter" id="ajouter" value="Bannir" class="button button-primary" >
</form>';


if (isset($_POST['ajouter'])) {
$ip = stripslashes($_POST['ip']);
$ban_date = stripslashes($_POST['ban_date']);
$unban_date = stripslashes($_POST['unban_date']);
global $wpdb;
$table_ipb = $wpdb->prefix . 'ipb';
$wpdb->insert( 
$table_ipb, 
array('ip'=>$ip,'ban_date'=>strftime('%Y-%m-%d', strtotime($ban_date)),'unban_date'=>strftime('%Y-%m-%d', strtotime($unban_date))), 
array('%s','%s','%s','%s')
);
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
echo '<script>document.location.href="' . admin_url() . 'admin.php?page=ipb&tab=ipb&action=add"</script>';
}


global $wpdb;
$table_ipb = $wpdb->prefix . 'ipb';
$voir_ipb = $wpdb->get_results("SELECT * FROM $table_ipb");
echo "<div id='listing_ip'>";
echo "<table class='wp-list-table widefat striped'>";
echo "<tr>";
echo "<th scope='row'><strong>IP BLOQUEES</strong></th>";
echo "<th scope='row'><strong>DATE DU BAN</strong></th>";
echo "<th scope='row'><strong>DATE DU DEBAN</strong></th>";
echo "<th scope='row'><strong>ACTIONS</strong></th>";
echo "</tr>";
foreach ($voir_ipb as $ipb) { 
echo "<tr>";
echo "<form enctype='multipart/form-data' method='post' class='actions_form'>";
echo "<td><input type='text' name='ip' id='ip' value='" . $ipb->ip . "' class='input_size'> <small><a href='http://my-address-ip.com/whois-address-ip-" . $ipb->ip . ".html' target='_blank'>Whois</a></td>";
echo "<td><img src='" . plugins_url( 'images/picto_agenda.png', __FILE__ ) . "' alt='Date du ban' /> <small>(dd-mm-YYYY)</small> <input type='text' name='ban_date' id='ban_date' value='" . strftime('%d-%m-%Y', strtotime($ipb->ban_date)) . "' class='input_size'></td>"; 
echo "<td><img src='" . plugins_url( 'images/picto_agenda.png', __FILE__ ) . "' alt='Date du déban' /> <small>(dd-mm-YYYY)</small> <input type='text' name='unban_date' id='unban_date' value='" . strftime('%d-%m-%Y', strtotime($ipb->unban_date)) . "' class='input_size'></td>";
echo "<td>";
//Form modifier
echo "
<input type='hidden' name='id_ipb' id='id_ipb' value='" . $ipb->id_ipb . "'>
<input type='submit' name='modifier' id='modifier' value='Modifier' class='button button-primary' >
</form>";
//Form débannir
echo "<form enctype='multipart/form-data' method='post' class='actions_form'>
<input type='submit' name='unban' id='unban' value='Débannir' class='button button-primary' >
<input type='hidden' name='id_ipb' id='id_ipb' value='" . $ipb->id_ipb . "'>
</form>";
echo "</td>";
echo "</tr>";
}
echo "</table></div>";
if (isset($_POST['modifier'])) {
$ip = stripslashes($_POST['ip']);
$ban_date = stripslashes($_POST['ban_date']);
$unban_date = stripslashes($_POST['unban_date']);
$id_ipb = stripslashes($_POST['id_ipb']);
//echo $ip . '<br>' . $ban_date . '<br>' . $unban_date . '<br>' . $id;
global $wpdb;
$table_ipb = $wpdb->prefix . 'ipb';
$wpdb->query($wpdb->prepare("UPDATE $table_ipb SET ip='$ip',ban_date='" . strftime('%Y-%m-%d', strtotime($ban_date)) . "',unban_date='" . strftime('%Y-%m-%d', strtotime($unban_date)) . "' WHERE id_ipb='$id_ipb'",APP_POST_TYPE));
echo '<script>document.location.href="' . admin_url() . 'admin.php?page=ipb&tab=ipb&action=maj-ip"</script>';
}
if (isset($_POST['unban'])) {
$id_ipb = stripslashes($_POST['id_ipb']);
global $wpdb;
$table_ipb = $wpdb->prefix . 'ipb';	
$wpdb->query($wpdb->prepare("DELETE FROM $table_ipb WHERE id_ipb='$id_ipb'"));
echo '<script>document.location.href="' . admin_url() . 'admin.php?page=ipb&tab=ipb&action=del-ip"</script>';
}
}

if ( (isset($_GET['tab']))&&($_GET['tab'] == 'param') ) {
global $wpdb;
$table_ipb_p = $wpdb->prefix . 'ipb_p';
$voir_ipb_p = $wpdb->get_row("SELECT * FROM $table_ipb_p");
echo "<h1>Paramètres</h1>
<div id='cadre_blanc'>
<form enctype='multipart/form-data' method='post'>
<table border='0' cellspacing='8' cellpadding='0'>
<tr>
<td colspan='2'><h3>Alerte mail</h3></td>
</tr>
<tr>
<td>Etre alerté par mail : </td>
<td>";
if ($voir_ipb_p->alerte_email == 'ON') {
echo "<input type='radio' name='alerte_email' id='radio' value='ON' checked='checked'> OUI ";
echo "<input type='radio' name='alerte_email' id='radio' value='OFF'> NON";
}
else {
echo "<input type='radio' name='alerte_email' id='radio' value='ON' > OUI ";
echo "<input type='radio' name='alerte_email' id='radio' value='OFF' checked='checked'> NON";	
}
echo "
</td>
</tr>
<tr>
<td>Email : </td>
<td><input type='text' name='email' id='email' value='" . $voir_ipb_p->email . "'></td>
</tr>
<tr>
<td colspan='2'><h3>Alerte SMS (abonnés Free Mobile)</h3></td>
</tr>
<tr>
<td>Etre alerté par sms : </td>
<td>";
if ($voir_ipb_p->alerte_sms == 'ON') {
echo "
<input type='radio' name='alerte_sms' id='radio2' value='ON' checked='checked'> OUI
<input type='radio' name='alerte_sms' id='radio2' value='OFF'> NON";
}
else {
echo "<input type='radio' name='alerte_sms' id='radio2' value='ON' > OUI
<input type='radio' name='alerte_sms' id='radio2' value='OFF' checked='checked'> NON";	
}
echo "
</td>
</tr>
<tr>
<td>ID Free Mobile :</td>
<td><input type='password' name='id_freemobile' id='id_freemobile' value='" . $voir_ipb_p->id_freemobile . "'></td>
</tr>
<tr>
<td>Clé Free Mobile : </td>
<td><input type='password' name='cle_freemobile' id='cle_freemobile' value='" . $voir_ipb_p->cle_freemobile . "'></td>
</tr>
<tr>
<td colspan='2'><h3>URL de redirection</h3></td>
</tr>
<tr>
<td>URL de redirection : </td>
<td><input type='text' name='url_redirect' id='url_redirect' value='" . $voir_ipb_p->url_redirect . "'></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type='submit' name='terminer' id='terminer' value='Terminer' class='button button-primary'></td>
</tr>
</table>
</form>
</div>";

if (isset($_POST['terminer'])) {
$alerte_email = stripslashes($_POST['alerte_email']);
$email = stripslashes($_POST['email']);
$alerte_sms = stripslashes($_POST['alerte_sms']);
$id_freemobile = stripslashes($_POST['id_freemobile']);
$cle_freemobile = stripslashes($_POST['cle_freemobile']);
$url_redirect = stripslashes($_POST['url_redirect']);
global $wpdb;
$table_ipb_p = $wpdb->prefix . 'ipb_p';
$wpdb->query($wpdb->prepare("UPDATE $table_ipb_p SET url_redirect='$url_redirect',email='$email',alerte_email='$alerte_email',alerte_sms='$alerte_sms',id_freemobile='$id_freemobile',cle_freemobile='$cle_freemobile' WHERE id_ipb_p='1'",APP_POST_TYPE));
echo '<script>document.location.href="' . admin_url() . 'admin.php?page=ipb&tab=param&action=maj-param"</script>';
}
}

if ( (isset($_GET['tab']))&&($_GET['tab'] == 'aide') ) {
echo "<h1>Aide au paramètrage freemobile</h1>
<div id='cadre_blanc'>
<h2>Connectez-vous sur votre espace freemobile</h2>
<p>Rendez-vous sur votres <a href='https://mobile.free.fr/moncompte/' target='_blank'>espace abonné freemobile</a> puis identifiez-vous.</p>
<h2>Activez vos notifications SMS</h2>
<p>Cliquez sur <strong><u>Mes options</u></strong> puis activez <strong><u>Notification par SMS</u></strong> et récupérez <strong><u>Votre clé d'identification au service</u></strong>
<h2>Information importante</h2>
<p>Votre identifiant freemobile ainsi que votre clé d'activation ne peuvent être cryptés dans la base de données Wordpress. L'identifiant et la clé d'activation sont affichés en brut dans la base de données de votre site Wordpress.</p>
<p>Le développeur du plugin (JM Créa) se dégage de toute responsabilité si vous utilisez ce plugin et que votre site se fait hacké.</p>
</div>";
}

if ( (isset($_GET['tab']))&&($_GET['tab'] == 'autres_plugins') ) {
	echo "<h1>Nos autres plugins</h1>";
	echo '
	<div id="listing_plugins">
	<h3>Social Share</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/social-share-par-jm-crea.jpg', __FILE__ ) . '" alt="Social Share par JM Créa" />
	<p>Social Share par JM Créa vous permet de partager votre contenu sur les réseaux sociaux.</p>
	<div align="center"><a href="https://fr.wordpress.org/plugins/social-share-by-jm-crea/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>
	
    <div id="listing_plugins">
	<h3>Search box Google</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/search-box-google-par-jm-crea.jpg', __FILE__ ) . '" alt="Search Box Google par JM Créa" />
	<p>Search Box Google permet d’intégrer le mini moteur de recherche de votre site dans les résultats Google.</p>
	<div align="center"><a href="https://fr.wordpress.org/plugins/search-box-google-par-jm-crea/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>
	
	<div id="listing_plugins">
	<h3>Notify Update</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/notify-update-par-jm-crea.jpg', __FILE__ ) . '" alt="Notify Update par JM Créa" />
	<p> Notify Update par JM Créa vous notifie par email et sms (pour les abonnés freemobile) lors d’une mise à jour de votre WordPress.</p>
	<div align="center"><a href="https://fr.wordpress.org/plugins/notify-update-par-jm-crea/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>

	<div id="listing_plugins">
	<h3>Notify Connect</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/notify-connect-par-jm-crea.jpg', __FILE__ ) . '" alt="Notify Connect par JM Créa" />
	<p>Notify connect créé par JM Créa permet d’être notifié par email et sms (pour les abonnés freemobile) lorsqu’un admin se connecte sur l\'admin.</p>
	<div align="center"><a href="https://fr.wordpress.org/plugins/notify-connect-par-jm-crea/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>
	
	<div id="listing_plugins">
	<h3>Simple Google Adsense</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/simple-google-adsense-par-jm-crea.jpg', __FILE__ ) . '" alt="Simple Google Adsense par JM Créa" />
	<p>Simple Google Adsense par JM Créa permet d’afficher vos publicités Google Adsense avec de simples shortcodes.</p>
	<div align="center"><a href="https://fr.wordpress.org/plugins/simple-google-adsense-par-jm-crea/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>
	
	<div id="listing_plugins">
	<h3>Knowledge Google</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/knowledge-google-par-jm-crea.jpg', __FILE__ ) . '" alt="Knowledge Google par JM Créa" />
	<p>Knowledge Google par JM Créa permet d\'afficher les liens de vos réseaux sociaux directement dans les résultats Google.</p>
	<div align="center"><a href="https://wordpress.org/plugins/knowledge-google-par-jm-crea/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>
	
	
	<div id="listing_plugins">
	<h3>IP Blocker WP</h3>
	<img src="' . plugins_url( 'autres-plugins-jm-crea/ip-blocker-wp-par-jm-crea.jpg', __FILE__ ) . '" alt="IP Blocker WP par JM Créa" />
	<p>IP Blocker WP permet de bloquer des adresses IP (front & back) à votre site et vous notifie par email et sms (pour les abonnés Free Mobile) des tentatives.</p>
	<div align="center"><a href="https://fr.wordpress.org/plugins/ip-blocker-wp/" target="_blank"><button class="button button-primary">Télécharger</button></a></div>
	</div>';
	
}
if (!isset($_GET['tab'])) {
echo '<script>document.location.href="' . admin_url() . 'admin.php?page=ipb&tab=ipb"</script>';
}

}


function alerte_ipb() {
global $wpdb;
$table_ipb_p = $wpdb->prefix . 'ipb_p';
$voir_ipb_p = $wpdb->get_row("SELECT * FROM $table_ipb_p WHERE id_ipb_p='1'");	
}

function verif_ipb() {
$monip = $_SERVER['REMOTE_ADDR'];
//Check IP
global $wpdb;
$table_ipb = $wpdb->prefix . 'ipb';
$voir_ipb = $wpdb->get_row("SELECT * FROM $table_ipb WHERE ip='$monip'");

//Check URL Redirect
global $wpdb;
$table_ipb_p = $wpdb->prefix . 'ipb_p';
$voir_ipb_p = $wpdb->get_row("SELECT * FROM $table_ipb_p WHERE id_ipb_p='1'");

if ( ($voir_ipb->ip == $monip)&&(date('Y-m-d') >= $voir_ipb->ban_date)&&(date('Y-m-d') <= $voir_ipb->unban_date) ) {

//Alerte mail
if ($voir_ipb_p->alerte_email == 'ON') {
$destinataire = $voir_ipb_p->email;
$sujet = "IP Blocker WP";
$From  = "From:" . $voir_ipb_p->email . "\n";
$From .= "MIME-version: 1.0\n";
$From .= "Content-type: text/html; charset= utf-8\n";
$From .= "Reply-To: " . $voir_ipb_p->email . "\n";
$From .= "Return-Path: <" . $voir_ipb_p->email . ">\n";
$From .= "X-Mailer: IP Blocker WP\n";	
$msg = "
Bonjour,<br><br>
IP Blocker WP vous informe que l'adresse IP : <strong>$monip</strong> vient de se faire bannir de votre site " . get_bloginfo('name') . " : le " . date('d-m-Y') . " à " . date('H:i') . "h<br><br>
A qui appartient cette IP ? : <a href='http://my-address-ip.com/whois-address-ip-$monip.html' target='_blank'>Whois IP</a><br><br>
<small><u>PS</u> : Si vous trouver ce plugin utile, merci de laisser une notre sur <a href='https://fr.wordpress.org/plugins/ip-blocker-wp/' target='_blank'>wordpress.org</a></small>";
mail($destinataire,$sujet,$msg,$From);
}
//Alerte sms
if ( ($voir_ipb_p->alerte_sms == 'ON')&&($voir_ipb_p->id_freemobile !== '')&&($voir_ipb_p->cle_freemobile !== '')||($voir_ipb_p->alerte_sms == 'ON')&&($voir_ipb_p->id_freemobile !== 'xxxxxxxxx')&&($voir_ipb_p->cle_freemobile !== 'xxxxxxxxx') ) {
$url_freemobile = "https://smsapi.free-mobile.fr/sendmsg?user=" . $voir_ipb_p->id_freemobile . "&pass=" . $voir_ipb_p->cle_freemobile . "&msg=IP Blocker WP vient de bannir l'adresse IP : $monip sur votre site " .   get_bloginfo("name") . " le " . date('d/m/Y') . " à " . date('H:i') . "h";
$handle = curl_init($url_freemobile);
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($handle);
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);
}
echo '<script>document.location.href="' . $voir_ipb_p->url_redirect . '"</script>';
}
}
add_action( 'wp_head', 'verif_ipb' );
add_action( 'admin_head', 'verif_ipb' );

function head_meta_ipb() {
echo("<meta name='IP Blocker WP par JM Créa' content='1.0' />\n");
}
add_action('wp_head', 'head_meta_ipb');