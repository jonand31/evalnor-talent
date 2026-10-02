<?php
/**
 * Plugin Name: Evalnor Talent
 * Description: WordPress bridge for Evalnor Talent. Business engine remains platform-independent.
 * Version: 0.1.0-dev
 * Requires PHP: 8.1
 * Text Domain: talent
 */
declare(strict_types=1);
defined('ABSPATH')||exit;
add_action('admin_menu',static function():void{add_management_page('Evalnor Talent','Evalnor Talent','manage_options','talent',static function():void{if(!current_user_can('manage_options'))return;echo '<div class="wrap"><h1>Evalnor Talent</h1><p>'.esc_html__('Evalnor WordPress bridge installed. The independent business engine remains outside WordPress and this bridge will expose only supported platform contracts.','talent').'</p><p><strong>'.esc_html__('Status: development bridge','talent').'</strong></p></div>';});});
add_action('rest_api_init',static function():void{register_rest_route('evalnor/talent/v1','/health',['methods'=>'GET','permission_callback'=>static fn()=>current_user_can('manage_options'),'callback'=>static fn()=>rest_ensure_response(['product'=>'talent','bridge'=>'wordpress','status'=>'development','core_detected'=>defined('EVALNOR_CORE_VERSION')||class_exists('Evalnor\\Core\\Core')])]);});
add_filter('evalnor_app_home',static fn(string $url):string=>$url);
