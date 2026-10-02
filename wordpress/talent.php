<?php
/**
 * Plugin Name: Evalnor Talent
 * Description: Operational WordPress adapter for Evalnor Talent.
 * Version: 0.6.0
 * Requires PHP: 8.1
 * Text Domain: talent
 */
declare(strict_types=1);
defined('ABSPATH')||exit;

use Evalnor\Core\WordPress\ProductRuntime;

define('EVALNOR_TALENT_VERSION','0.6.0');
define('EVALNOR_TALENT_OPTION','evalnor_talent_records_v2');
define('EVALNOR_TALENT_AUDIT','evalnor_talent_audit_v1');

function evalnor_talent_runtime_available():bool{return class_exists(ProductRuntime::class);}
function evalnor_talent_register_runtime():void{
 if(!evalnor_talent_runtime_available())return;
 ProductRuntime::register([
  'id'=>'talent','name'=>'Evalnor Talent','version'=>EVALNOR_TALENT_VERSION,'schema_version'=>2,
  'capability'=>'evalnor_manage_talent','option'=>EVALNOR_TALENT_OPTION,'audit_option'=>EVALNOR_TALENT_AUDIT,
  'schema_option'=>'evalnor_talent_schema','rest_namespace'=>'evalnor/talent/v1',
  'sanitize'=>static function(array $in,array $base):array{
   $status=sanitize_key((string)($in['status']??'new'));
   if(!in_array($status,['new','screening','interview','offer','hired'],true))$status='new';
   return ['name'=>sanitize_text_field((string)($in['name']??'')),'email'=>sanitize_email((string)($in['email']??'')),'job'=>sanitize_text_field((string)($in['job']??'')),'status'=>$status];
  },
 ]);
}
add_action('plugins_loaded','evalnor_talent_register_runtime',20);

register_activation_hook(__FILE__,static function():void{
 if(!evalnor_talent_runtime_available())wp_die('Evalnor Talent requires Evalnor Core 1.2.0 or newer.');
 evalnor_talent_register_runtime();ProductRuntime::install('talent');
});

function evalnor_talent_can():bool{return evalnor_talent_runtime_available()?ProductRuntime::can('talent'):false;}
function evalnor_talent_rows():array{return evalnor_talent_runtime_available()?ProductRuntime::rows('talent'):[];}
function evalnor_talent_clean(array $in):array{return ProductRuntime::clean('talent',$in);}
function evalnor_talent_upsert(array $row):array{return ProductRuntime::upsert('talent',$row);}
function evalnor_talent_delete(string $id):bool{return ProductRuntime::delete('talent',$id);}

add_action('admin_notices',static function():void{
 if(evalnor_talent_runtime_available())return;
 echo '<div class="notice notice-error"><p>Evalnor Talent requires Evalnor Core 1.2.0 or newer.</p></div>';
});

add_action('admin_menu',static function():void{
 if(evalnor_talent_runtime_available())add_menu_page('Evalnor Talent','Evalnor Talent','evalnor_manage_talent','talent','evalnor_talent_screen','dashicons-chart-area',58);
});
function evalnor_talent_screen():void{
 if(!evalnor_talent_can())wp_die('Access denied');
 if(isset($_POST['evalnor_action'])&&check_admin_referer('evalnor_talent_save')){
  if($_POST['evalnor_action']==='save')evalnor_talent_upsert(evalnor_talent_clean(wp_unslash($_POST)));
  if($_POST['evalnor_action']==='delete')evalnor_talent_delete(sanitize_key((string)($_POST['id']??'')));
 }
 $rows=evalnor_talent_rows();$counts=[];foreach(['new','screening','interview','offer','hired'] as $s)$counts[$s]=0;
 foreach($rows as $r)if(isset($counts[$r['status']??'']))$counts[$r['status']]++;
 echo '<div class="wrap"><h1>Evalnor Talent <small>v'.esc_html(EVALNOR_TALENT_VERSION).'</small></h1><p>';
 foreach($counts as $s=>$n)echo '<strong>'.esc_html($s).':</strong> '.esc_html((string)$n).' &nbsp; ';
 echo '</p><h2>Ajouter — Candidat</h2><form method="post">';wp_nonce_field('evalnor_talent_save');
 echo '<input type="hidden" name="evalnor_action" value="save"><table class="form-table">';
 echo '<tr><th>Nom</th><td><input class="regular-text" name="name" required></td></tr>';
 echo '<tr><th>Courriel</th><td><input class="regular-text" type="email" name="email" required></td></tr>';
 echo '<tr><th>Poste</th><td><input class="regular-text" name="job" required></td></tr><tr><th>Statut</th><td><select name="status">';
 foreach(['new','screening','interview','offer','hired'] as $s)echo '<option>'.esc_html($s).'</option>';
 echo '</select></td></tr></table><p><button class="button button-primary">Enregistrer</button></p></form><h2>Candidats</h2><table class="widefat striped"><thead><tr><th>Nom</th><th>Courriel</th><th>Poste</th><th>Statut</th><th>Action</th></tr></thead><tbody>';
 foreach($rows as $r){echo '<tr><td>'.esc_html((string)($r['name']??'')).'</td><td>'.esc_html((string)($r['email']??'')).'</td><td>'.esc_html((string)($r['job']??'')).'</td><td>'.esc_html((string)($r['status']??'')).'</td><td><form method="post">';wp_nonce_field('evalnor_talent_save');echo '<input type="hidden" name="evalnor_action" value="delete"><input type="hidden" name="id" value="'.esc_attr((string)$r['id']).'"><button class="button">Supprimer</button></form></td></tr>';}
 echo '</tbody></table><p><a class="button" href="'.esc_url(rest_url('evalnor/talent/v1/export')).'">Export JSON</a></p></div>';
}
add_action('rest_api_init',static function():void{if(evalnor_talent_runtime_available())ProductRuntime::registerRestRoutes('talent');},20);
add_filter('evalnor_app_home',static fn(string $url):string=>evalnor_talent_can()?admin_url('admin.php?page=talent'):$url);
