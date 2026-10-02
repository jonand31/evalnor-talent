<?php
declare(strict_types=1);
$actions=[];$filters=[];$options=[];$caps=['manage_options'=>true];$routes=[];$uuid=0;
function add_action($h,$c,$p=10){global $actions;$actions[$h][]=$c;} function add_filter($h,$c,$p=10){global $filters;$filters[$h][]=$c;}
function register_activation_hook($f,$c){global $actions;$actions['activation'][]=$c;} function add_menu_page(...$a){return 'hook';}
function sanitize_key($v){return strtolower(preg_replace('/[^a-zA-Z0-9_\-]/','',(string)$v)??'');} function sanitize_text_field($v){return trim(strip_tags((string)$v));} function sanitize_email($v){return filter_var((string)$v,FILTER_SANITIZE_EMAIL);}
function wp_generate_uuid4(){global $uuid;return 'talent-'.(++$uuid);} function get_option($k,$d=false){global $options;return array_key_exists($k,$options)?$options[$k]:$d;}
function add_option($k,$v,$x='',$a=null){global $options;if(array_key_exists($k,$options))return false;$options[$k]=$v;return true;} function update_option($k,$v,$a=null){global $options;$options[$k]=$v;return true;}
function get_role($r){return new class{function add_cap($c){global $caps;$caps[$c]=true;}};} function current_user_can($c){global $caps;return !empty($caps[$c]);}
function get_current_user_id(){return 1;} function wp_json_encode($v,$f=0){return json_encode($v,$f);} function dbDelta($s){return true;}
function register_rest_route($a,$b,$c){global $routes;$routes[$a.$b]=$c;return true;} function rest_ensure_response($v){return $v;}
function wp_die($m){throw new RuntimeException((string)$m);} function wp_unslash($v){return $v;} function check_admin_referer($a){return true;}
function esc_html($v){return $v;} function esc_attr($v){return $v;} function esc_url($v){return $v;} function rest_url($v){return $v;} function admin_url($v){return $v;} function wp_nonce_field($v){}
class WP_REST_Request implements ArrayAccess{private array $d=[];function offsetExists($o):bool{return isset($this->d[$o]);}function offsetGet($o):mixed{return $this->d[$o]??null;}function offsetSet($o,$v):void{$this->d[$o]=$v;}function offsetUnset($o):void{unset($this->d[$o]);}function get_json_params(){return $this->d;}}
class WP_Error{} define('ABSPATH',__DIR__.'/fake-wp/');define('ARRAY_A','ARRAY_A');define('EVALNOR_CORE_VERSION','1.2.0');
class FakeWpdb{public string $prefix='wp_';private array $r=[];function get_charset_collate(){return '';}function prepare($s,...$a){return ['sql'=>$s,'args'=>$a];}
function replace($t,$d,$f=[]){$this->r[$d['product_id'].':'.$d['record_id']]=$d;return 1;}function query($p){$a=$p['args'];for($i=0;$i+5<count($a);$i+=6){$k=$a[$i].':'.$a[$i+1];$created=$this->r[$k]['created_at']??$a[$i+4];$this->r[$k]=['product_id'=>$a[$i],'record_id'=>$a[$i+1],'status'=>$a[$i+2],'payload'=>$a[$i+3],'created_at'=>$created,'updated_at'=>$a[$i+5]];}return 1;}
function delete($t,$w,$f=[]){$k=$w['product_id'].':'.$w['record_id'];if(!isset($this->r[$k]))return 0;unset($this->r[$k]);return 1;}
function get_var($p){$a=$p['args'];if(str_contains($p['sql'],'COUNT'))return count(array_filter($this->r,fn($r)=>$r['product_id']===$a[0]));$k=$a[0].':'.$a[1];return $this->r[$k]['payload']??null;}
function get_results($p,$m){$a=$p['args'];$rows=array_values(array_filter($this->r,fn($r)=>$r['product_id']===$a[0]));return array_map(fn($r)=>['payload'=>$r['payload']],array_slice($rows,(int)$a[count($a)-1],(int)$a[count($a)-2]));}}
$wpdb=new FakeWpdb();

namespace Evalnor\Core\WordPress { final class PermissionService{static function allows(string $p):bool{return false;}} }
namespace {
$core=getenv('EVALNOR_CORE_PATH');if(!$core)throw new RuntimeException('EVALNOR_CORE_PATH missing');
require $core.'/wordpress/src/ProductRecordRepository.php';require $core.'/wordpress/src/ProductRuntime.php';require __DIR__.'/../wordpress/talent.php';
function ok($v,$m){if(!$v)throw new RuntimeException($m);}
evalnor_talent_register_runtime();ok(evalnor_talent_runtime_available(),'Talent must detect Core 1.2.0');
global $options;$options['evalnor_talent_records_v2']=[['id'=>'legacy','name'=>'Legacy','email'=>'legacy@example.com','job'=>'Ops','status'=>'new','created_at'=>'2026-01-01T00:00:00+00:00','updated_at'=>'2026-01-01T00:00:00+00:00']];
\Evalnor\Core\WordPress\ProductRuntime::install('talent');
while(\Evalnor\Core\WordPress\ProductRuntime::migrateStorage('talent',500)['remaining']>0){}
ok(evalnor_talent_can(),'Talent permission must resolve through Core');
ok(count(evalnor_talent_rows())===1,'Legacy Talent row must survive SQL migration');
$r=evalnor_talent_upsert(evalnor_talent_clean(['name'=>'Jane','email'=>'jane@example.com','job'=>'Engineer','status'=>'interview']));
ok(!empty($r['id']),'Talent create must return stable id');ok(count(evalnor_talent_rows())===2,'Talent create must persist');
$export=\Evalnor\Core\WordPress\ProductRuntime::export('talent');ok(count($export['records'])===2,'Talent export must include SQL records');
\Evalnor\Core\WordPress\ProductRuntime::registerRestRoutes('talent');global $routes;ok(isset($routes['evalnor/talent/v1/records']),'Talent REST records route must register');
ok(evalnor_talent_delete($r['id']),'Talent delete must work');ok(count(evalnor_talent_rows())===1,'Talent delete must persist');
echo "Talent/Core integration contract passed\n";
}