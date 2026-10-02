<?php
/**
 * Plugin Name: Evalnor Talent
 * Description: Evalnor Talent WordPress application adapter for the Evalnor platform.
 * Version: 0.2.0
 * Requires PHP: 8.1
 * Text Domain: talent
 */
declare(strict_types=1);
defined('ABSPATH')||exit;
const EVALNOR_TALENT_VERSION='0.2.0';
const EVALNOR_TALENT_OPTION='evalnor_talent_records_v1';
register_activation_hook(__FILE__,static function():void{$r=get_role('administrator');if($r)$r->add_cap('evalnor_manage_talent');if(get_option(EVALNOR_TALENT_OPTION,null)===null)add_option(EVALNOR_TALENT_OPTION,[], '', false);});
function evalnor_talent_can():bool{return current_user_can('evalnor_manage_talent')||current_user_can('evalnor_manage_business')||current_user_can('manage_options');}
function evalnor_talent_records():array{$v=get_option(EVALNOR_TALENT_OPTION,[]);return is_array($v)?array_values($v):[];}
function evalnor_talent_sanitize(array $in):array{
 $out=['id'=>isset($in['id'])?sanitize_key((string)$in['id']):wp_generate_uuid4(),'created_at'=>sanitize_text_field((string)($in['created_at']??gmdate('c'))),'updated_at'=>gmdate('c')];
 $out['name']=sanitize_text_field((string)($in['name']??''));
 $out['email']=sanitize_text_field((string)($in['email']??''));
 $out['job']=sanitize_text_field((string)($in['job']??''));
 $out['status']=sanitize_text_field((string)($in['status']??''));
 if(!in_array($out['status'],['new','screening','interview','offer','hired'],true))$out['status']='new';
 return $out;
}
function evalnor_talent_save(array $record):array{$all=evalnor_talent_records();$found=false;foreach($all as $i=>$old){if(($old['id']??'')===$record['id']){$record['created_at']=$old['created_at']??$record['created_at'];$all[$i]=$record;$found=true;break;}}if(!$found)$all[]=$record;update_option(EVALNOR_TALENT_OPTION,$all,false);return $record;}
add_action('admin_menu',static function():void{add_menu_page('Evalnor Talent','Evalnor Talent','evalnor_manage_talent','talent','evalnor_talent_screen','dashicons-chart-area',58);});
function evalnor_talent_screen():void{if(!evalnor_talent_can())wp_die(esc_html__('Access denied.','talent'));$rows=evalnor_talent_records();echo '<div class="wrap"><h1>Evalnor Talent</h1><p><strong>v'.esc_html(EVALNOR_TALENT_VERSION).'</strong> · Core: '.(defined('EVALNOR_CORE_VERSION')||class_exists('Evalnor\\Core\\Core')?'✓':'optional').'</p><h2>Candidates</h2><p>'.esc_html(count($rows)).' record(s)</p><table class="widefat striped"><thead><tr><th>ID</th><th>name</th><th>email</th><th>job</th><th>status</th><th>Updated</th></tr></thead><tbody>';foreach($rows as $r){echo '<tr><td>'.esc_html((string)($r['id']??'')).'</td><td>'.esc_html((string)($r['name']??'')).'</td><td>'.esc_html((string)($r['email']??'')).'</td><td>'.esc_html((string)($r['job']??'')).'</td><td>'.esc_html((string)($r['status']??'')).'</td><td>'.esc_html((string)($r['updated_at']??'')).'</td></tr>';}echo '</tbody></table><p>REST API: <code>/wp-json/evalnor/talent/v1/records</code></p></div>';}
add_action('rest_api_init',static function():void{
 register_rest_route('evalnor/talent/v1','/health',['methods'=>'GET','permission_callback'=>'evalnor_talent_can','callback'=>static fn()=>rest_ensure_response(['product'=>'talent','version'=>EVALNOR_TALENT_VERSION,'status'=>'ready','records'=>count(evalnor_talent_records()),'core_detected'=>defined('EVALNOR_CORE_VERSION')||class_exists('Evalnor\\Core\\Core')])]);
 register_rest_route('evalnor/talent/v1','/records',['methods'=>'GET','permission_callback'=>'evalnor_talent_can','callback'=>static fn()=>rest_ensure_response(evalnor_talent_records())]);
 register_rest_route('evalnor/talent/v1','/records',['methods'=>'POST','permission_callback'=>'evalnor_talent_can','callback'=>static function(WP_REST_Request $req){$data=$req->get_json_params();if(!is_array($data))return new WP_Error('invalid_payload','Invalid JSON payload',['status'=>400]);return rest_ensure_response(evalnor_talent_save(evalnor_talent_sanitize($data)));}]);
});
add_filter('evalnor_app_home',static function(string $url):string{return evalnor_talent_can()?admin_url('admin.php?page=talent'):$url;});
