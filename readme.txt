=== Hide Classic Editor for Elementor Pages ===
Contributors: wumetax
Tags: elementor, classic editor, editor, page builder
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Elementor 頁面強制使用 Elementor 編輯器，防止誤用 WordPress 一般編輯器造成跑版。

== Description ==
當頁面是由 Elementor 建立時，此外掛會：
* 將頁面列表的「編輯」按鈕改為「使用 Elementor 編輯」
* 將後台頁面標題連結導向 Elementor 編輯器
* 將上方管理列的「編輯頁面」按鈕改為 Elementor
* 若直接進入 WordPress 一般編輯器，自動跳轉回 Elementor
* 隱藏 Elementor 原生的重複按鈕

== Installation ==
1. 上傳外掛資料夾到 `/wp-content/plugins/`
2. 在 WordPress 後台啟用外掛
3. 確認已安裝 Elementor

== Changelog ==
= 1.3.0 =
* 新增 CSS 隱藏 Elementor 原生重複按鈕
* 修正前台 Fatal error（get_current_screen 問題）
* 新增 Elementor 未安裝提示

= 1.0.0 =
* 初始版本
