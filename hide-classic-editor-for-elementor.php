<?php
/**
 * Plugin Name:       Hide Classic Editor for Elementor Pages
 * Plugin URI:        https://wumetax.com
 * Description:       Elementor 頁面強制使用 Elementor 編輯器，防止客戶誤用 WordPress 一般編輯器造成跑版。
 * Version:           1.3.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            jimmy
 * Author URI:        https://wumetax.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hide-classic-editor-for-elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 確認 Elementor 已安裝啟用
 */
add_action( 'admin_notices', 'hcefe_check_elementor' );
function hcefe_check_elementor() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Hide Classic Editor for Elementor：</strong> 請先安裝並啟用 Elementor 外掛。</p>
        </div>';
    }
}

/**
 * 1. 頁面列表 row actions：只保留「使用 Elementor 編輯」
 */
add_filter( 'page_row_actions', 'hcefe_modify_row_actions', 10, 2 );
add_filter( 'post_row_actions', 'hcefe_modify_row_actions', 10, 2 );
function hcefe_modify_row_actions( $actions, $post ) {
    $is_elementor = get_post_meta( $post->ID, '_elementor_edit_mode', true );

    if ( $is_elementor === 'builder' ) {
        $elementor_url = admin_url( 'post.php?post=' . $post->ID . '&action=elementor' );

        unset( $actions['edit'] );
        unset( $actions['edit_with_elementor'] );

        $actions = array_merge(
            [ 'edit' => sprintf(
                '<a href="%s">%s</a>',
                esc_url( $elementor_url ),
                esc_html__( '使用 Elementor 編輯', 'hide-classic-editor-for-elementor' )
            )],
            $actions
        );
    }

    return $actions;
}

/**
 * 2. 後台頁面標題連結導向 Elementor
 */
add_filter( 'get_edit_post_link', 'hcefe_redirect_title_to_elementor', 10, 3 );
function hcefe_redirect_title_to_elementor( $link, $post_id, $context ) {
    if ( ! is_admin() ) return $link;
    if ( $context !== 'display' ) return $link;

    $is_elementor = get_post_meta( $post_id, '_elementor_edit_mode', true );
    if ( $is_elementor === 'builder' ) {
        return admin_url( 'post.php?post=' . $post_id . '&action=elementor' );
    }

    return $link;
}

/**
 * 3. 上方管理列「編輯頁面」按鈕改為 Elementor
 */
add_action( 'admin_bar_menu', 'hcefe_fix_admin_bar_edit_link', 999 );
function hcefe_fix_admin_bar_edit_link( $wp_admin_bar ) {
    $edit_node = $wp_admin_bar->get_node( 'edit' );
    if ( ! $edit_node ) return;

    $post_id = get_the_ID();
    if ( ! $post_id ) return;

    $is_elementor = get_post_meta( $post_id, '_elementor_edit_mode', true );
    if ( $is_elementor === 'builder' ) {
        $wp_admin_bar->add_node([
            'id'    => 'edit',
            'title' => esc_html__( '使用 Elementor 編輯', 'hide-classic-editor-for-elementor' ),
            'href'  => esc_url( admin_url( 'post.php?post=' . $post_id . '&action=elementor' ) ),
        ]);
    }
}

/**
 * 4. 防止直接進入 WordPress 一般編輯器，自動跳轉 Elementor
 */
add_action( 'load-post.php', 'hcefe_redirect_classic_editor_to_elementor' );
function hcefe_redirect_classic_editor_to_elementor() {
    // 確保只在 admin 且非 AJAX 環境下執行，避免干擾 WooCommerce container
    if ( ! is_admin() || wp_doing_ajax() ) return;

    $post_id = isset( $_GET['post'] ) ? intval( $_GET['post'] ) : 0;
    $action  = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

    if ( ! $post_id || $action !== 'edit' ) return;

    $is_elementor = get_post_meta( $post_id, '_elementor_edit_mode', true );
    if ( $is_elementor === 'builder' ) {
        wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=elementor' ) );
        exit;
    }
}

/**
 * 5. CSS 隱藏 Elementor 原生重複按鈕（後台 + 前台管理列）
 */
add_action( 'admin_head', 'hcefe_hide_elementor_edit_button_css' );
add_action( 'wp_head', 'hcefe_hide_elementor_edit_button_css' );
function hcefe_hide_elementor_edit_button_css() {
    echo '<style>
        .edit_with_elementor,
        #wp-admin-bar-elementor_edit_page { display: none !important; }
    </style>';
}
