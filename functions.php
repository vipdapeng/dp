<?php
define('ROC_DS', DIRECTORY_SEPARATOR);
define('ROC_THEME_DIR', get_template_directory());
define('ROC_THEME_URI', get_template_directory_uri());
define('ROC_VERSION', '1.0');
require ROC_THEME_DIR . ROC_DS . 'load.php';

function _z($name, $default = false, $subname = '')
{
    // 声明静态变量以加快检索速度
    static $options = null;
    if ($options === null) {
        $options = get_option('z_options');
    }

    if (isset($options[$name])) {
        if ($subname) {
            return isset($options[$name][$subname]) ? $options[$name][$subname] : $default;
        } else {
            return $options[$name];
        }
    }
    return $default;
}

function z_getBaseUrl($url) {
    $parts = parse_url($url);

    if ($parts === false || empty($parts['scheme'])) {
        return null;
    }

    return $parts['scheme'] . '://' . $parts['host'];
}

function z_getHostUrl($url) {
    $parts = parse_url($url);

    if ($parts === false || empty($parts['host'])) {
        return null; // 无效的URL
    }

    return $parts['host'];
}
?>
































<?php
function tailwind_blog_setup() {
    // 启用特色图片支持
    add_theme_support('post-thumbnails');
    
    // 注册导航菜单
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'tailwind-blog'),
    ));

    // 启用标题标签
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'tailwind_blog_setup');

// 注册侧边栏
function tailwind_blog_widgets_init() {
    register_sidebar(array(
        'name'          => __('Main Sidebar', 'tailwind-blog'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'tailwind-blog'),
        'before_widget' => '<div id="%1$s" class="bg-white p-6 rounded-lg shadow-sm mb-4 widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="text-lg font-bold mb-4 border-l-4 border-blue-500 pl-3">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'tailwind_blog_widgets_init');

// 引入样式和脚本
function tailwind_blog_scripts() {
    wp_enqueue_style('tailwind-blog-style', get_stylesheet_uri());
    // 这里我们直接在 header.php 使用 CDN，实际开发建议本地化
}
add_action('wp_enqueue_scripts', 'tailwind_blog_scripts');



/**
 * 自定义分页函数 (适配 Tailwind CSS - 无 wp_is_mobile 依赖)
 */
function tailwind_paginate_links($args = array()) {
    global $wp_query;

    $defaults = array(
        'url_base'     => str_replace(999999999, '%#%', get_pagenum_link(999999999)),
        'total'        => $wp_query->found_posts,
        'current'      => max(1, get_query_var('paged')),
        'page_size'    => get_query_var('posts_per_page'),
        // 响应式按钮文字：移动端隐藏文字只显示图标 (hidden sm:inline)，sm以上显示文字
        'prev_text'    => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg><span class="hidden sm:inline ml-1">上一页</span>',
        'next_text'    => '<span class="hidden sm:inline mr-1">下一页</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
        'array'        => false,
        'class'        => 'flex flex-wrap justify-center items-center gap-2 mt-4 md:mt-6', // flex-wrap 确保移动端不溢出
    );

    $args = wp_parse_args($args, $defaults);

    $current      = (int) $args['current'];
    $total        = (int) $args['total'];
    $page_size    = (int) $args['page_size'];
    if ($page_size < 1) $page_size = 10;
    
    $total_pages  = ceil($total / $page_size); 
    $link_base    = $args['url_base'];
    
    $mid_size     = 2; 

    if ($total_pages < 2) {
        return '';
    }

    $page_links = array();

    // 样式定义
    // 数字按钮
    $number_class   = 'w-10 h-8 flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:shadow-sm transition text-sm font-medium';
    // 当前页
    $active_class   = 'w-10 h-8 flex items-center justify-center rounded-md border border-blue-600 bg-blue-600 text-white shadow-md text-sm font-medium';
    // 上下页按钮 (可点击)
    $nav_class      = 'h-8 px-3 flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:shadow-sm transition text-sm font-medium';
    // 上下页按钮 (禁止点击)
    $disabled_class = 'h-8 px-3 flex items-center justify-center rounded-md border border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed opacity-60 text-sm font-medium';

    // 上一页
    if ($current && 1 < $current) {
        $link = $link_base ? str_replace('%#%', $current - 1, $link_base) : 'javascript:void(0);';
        $page_links[] = sprintf('<a class="%s" href="%s">%s</a>', $nav_class, esc_url($link), $args['prev_text']);
    } else {
        // 始终显示上一页但不可点
        $page_links[] = sprintf('<span class="%s">%s</span>', $disabled_class, $args['prev_text']);
    }

    // 循环页码
    for ($n = 1; $n <= $total_pages; $n++):
        if ($n == $current):
            $page_links[] = sprintf('<span class="%s">%s</span>', $active_class, $n);
        else:
            // 逻辑修改：去掉首尾强制显示，去掉省略号，只显示 range 内的
            if ($n >= $current - $mid_size && $n <= $current + $mid_size):
                $link = $link_base ? str_replace('%#%', $n, $link_base) : 'javascript:void(0);';
                $page_links[] = sprintf('<a class="%s" href="%s">%s</a>', $number_class, esc_url($link), $n);
            endif;
        endif;
    endfor;

    // 下一页
    if ($current && $current < $total_pages) {
        $link = $link_base ? str_replace('%#%', $current + 1, $link_base) : 'javascript:void(0);';
        $page_links[] = sprintf('<a class="%s" href="%s">%s</a>', $nav_class, esc_url($link), $args['next_text']);
    } else {
        // 始终显示下一页但不可点
        $page_links[] = sprintf('<span class="%s">%s</span>', $disabled_class, $args['next_text']);
    }
    
    // 跳转输入框 - 使用 hidden md:flex 在移动端隐藏
    if ($total_pages > 5 && $link_base) {
         $jump_link = str_replace('%#%', "'+this.value+'", $link_base); 
         $page_links[] = sprintf(
             '<div class="hidden md:flex items-center space-x-2 ml-2">
                <input type="number" min="1" max="%d" placeholder="Go" 
                    class="w-14 h-8 px-2 text-center border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm"
                    onkeydown="if(event.keyCode==13){ var url=\'%s\'; window.location.href = url.replace(\'\' + this.value + \'\', this.value); return false; }"
                >
             </div>',
             $total_pages,
             str_replace("'+this.value+'", "99999", $jump_link)
         );
    }

    if ($args['array']) {
        return $page_links;
    }

    $html = '<div class="' . $args['class'] . '">';
    $html .= implode('', $page_links);
    $html .= '</div>';
    
    return $html;
}

// 自定义卡片函数（包含循环逻辑 + 手动置顶渲染 + 分页）
function posts_mian_list_card($args = array()) {
    // 开始构建内容，先包裹 Grid 容器
    // 注意：这里的 grid class 需要与 index.php 中移除的那个保持一致
    $content_html = '';
    
    // --- 1. 手动渲染置顶文章 (仅在首页第一页) ---
    // 配合 tailwind_fix_sticky_pagination 排除逻辑
    if (is_home() && !is_paged()) {
        $sticky_posts = get_option('sticky_posts');
        if (!empty($sticky_posts)) {
            $args_sticky = array(
                'post__in' => $sticky_posts,
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1, // 显示所有置顶
                'orderby' => 'date',    // 置顶内部按时间排序
                'order'   => 'DESC'
            );
            $sticky_query = new WP_Query($args_sticky);
            if ($sticky_query->have_posts()) {
                while ($sticky_query->have_posts()) {
                    $sticky_query->the_post();
                    // 强制传参 is_sticky_render 以便生成徽章（如果原生 is_sticky 失效）
                    $content_html .= get_single_post_card_html(array_merge($args, array('force_sticky' => true)));
                }
                wp_reset_postdata();
            }
        }
    }
    // ------------------------------------------

    // --- 2. 渲染主循环文章 ---
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            $content_html .= get_single_post_card_html($args);
        }
    } else {
        if (empty($content_html)) { // 如果连置顶也没有
             $content_html = '<p class="col-span-1 md:col-span-2 lg:col-span-3 text-center text-gray-500 py-10">暂无文章</p>';
        }
    }
    
    // --- 3. 组装最终 HTML ---
    // Grid 容器包裹文章列表
    $final_html = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">';
    $final_html .= $content_html;
    $final_html .= '</div>';
    
    // --- 4. 追加分页组件 ---
    // 直接在此处调用分页函数并拼接到后面
    $final_html .= tailwind_paginate_links();
    
    return $final_html;
}

// 单个文章卡片生成函数
function get_single_post_card_html($args = array()) {
    // 调整顺序：先生成 badge，以便传入 graphic
    $badge   = get_posts_list_badge($args);
    // 这里传入空字符串，但在 get_posts_thumb_graphic 中会处理为空时的默认值
    
    // 检测是否强制显示置顶标记
    $is_force_sticky = !empty($args['force_sticky']);
    $graphic = get_posts_thumb_graphic('', $badge, $is_force_sticky); 

    $title   = get_posts_list_title();
    $excerpt = get_posts_list_excerpt();
    $meta    = get_posts_list_meta(true, true);

    $class = 'bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-gray-100 overflow-hidden group';
    
    $html = '<article class="' . $class . '">';
    $html .= $graphic;
    // 修改 p-5 为 p-4
    $html .= '<div class="p-4 pt-0 flex flex-col flex-grow">';
    $html .= $title;
    $html .= $excerpt;
    // $badge 移动到图片上方，此处移除
    $html .= '<div class="mt-auto pt-2 border-t border-gray-100">';
    $html .= $meta;
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</article>';
    return $html;
}

function get_posts_list_excerpt($class = 'bg-gray-50 rounded-md mb-3 p-4 h-20 overflow-hidden') {
    $excerpt = get_the_excerpt();
    if (empty($excerpt)) {
        $excerpt = wp_trim_words(get_the_content(), 60, '...');
    }
    return '<div class="' . $class . '">
                <div class="text-gray-500 text-sm line-clamp-2 leading-relaxed">' . $excerpt . '</div>
            </div>';
}

function get_posts_thumb_graphic($class = 'relative overflow-hidden p-4', $overlay_content = '', $force_sticky = false) {
    // 修复：如果传入空字符串，强制使用默认样式，确保 relative 属性存在
    if (empty($class)) {
        $class = 'relative overflow-hidden p-4';
    }

    $thumbnail = '';
    if (has_post_thumbnail()) {
        $thumbnail = get_the_post_thumbnail(null, 'medium_large', array('class' => 'w-full h-48 object-cover rounded-lg transform group-hover:scale-105 transition duration-500'));
    } else {
        $thumbnail = '<div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>';
    }

    // 添加置顶标记 (红色标签，左上角)
    // 检查 WP 原生 is_sticky() 或者手动传入的 flag
    $sticky_badge = '';
    if (is_sticky() || $force_sticky) {
        // 使用 absolute top-6 left-6 确保在 padding 内
        $sticky_badge = '<span class="absolute top-6 left-6 z-20 bg-red-500/90 backdrop-blur-sm text-white text-xs px-2 py-1 rounded shadow-sm font-medium cursor-default select-none pointer-events-none">置顶</span>';
    }
    
    return '<div class="' . $class . '">
                <a href="' . get_permalink() . '">' . $thumbnail . '</a>
                ' . $sticky_badge . '
                ' . $overlay_content . '
            </div>';
}

function get_posts_list_title($class = 'text-lg font-bold mb-2 text-gray-900 hover:text-blue-600 transition leading-tight line-clamp-2 overflow-hidden h-12') {
    return '<h2 class="' . $class . '"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
}

function get_posts_list_badge($args = array()) {
    $categories = get_the_category();
    if (empty($categories)) return '';
    
    // 修改为绝对定位样式，悬浮于图片右下角，无链接
    // bottom-6 right-6 确保在 p-4 内缩的基础上稍微向内偏移
    $html = '<div class="absolute bottom-6 right-6 z-10 flex flex-wrap gap-2 justify-end pointer-events-none cursor-default select-none">';
    foreach($categories as $cat) {
        // 改为 span，移除 href，保留视觉样式
        $html .= '<span class="text-xs bg-white/90 backdrop-blur-sm text-blue-600 px-2 py-1 rounded shadow-sm font-medium cursor-default select-none">' . $cat->name . '</span>';
    }
    $html .= '</div>';
    return $html;
}

function get_posts_list_meta($show_author = true, $is_card = false, $post = null) {
    if (!$post) $post = get_post();
    
    // Left: Author Avatar + Time Ago
    $author_id = $post->post_author;
    $author_avatar = get_avatar($author_id, 24, '', '', array('class' => 'w-6 h-6 rounded-full mr-2 border border-gray-200'));
    $date = human_time_diff(get_the_time('U', $post), current_time('timestamp')) . '前';
    
    $meta_left = '<div class="flex items-center">';
    $meta_left .= $author_avatar;
    $meta_left .= '<span class="text-xs text-gray-500">' . $date . '</span>';
    $meta_left .= '</div>';

    // Right: Views + Comments
    $comments_count = get_comments_number($post);
    $views_count = rand(100, 3000); 

    $meta_right = '<div class="flex items-center text-gray-400 text-xs space-x-3">';
    $meta_right .= '<div class="flex items-center" title="阅读">';
    $meta_right .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
    $meta_right .= $views_count;
    $meta_right .= '</div>';
    $meta_right .= '<div class="flex items-center" title="评论">';
    $meta_right .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>';
    $meta_right .= $comments_count;
    $meta_right .= '</div>';
    $meta_right .= '</div>';

    $html = '<div class="flex justify-between items-center w-full">';
    $html .= $meta_left;
    $html .= $meta_right;
    $html .= '</div>';

    return $html;

}

/**
 * 终极置顶文章修正方案 (手动注入版)
 * 逻辑：
 * 1. 主查询彻底排除置顶文章，只查“剩下的”。
 * 2. 首页Page1，减少查询数量，留位置给手动注入的置顶。
 * 3. Page2+，计算偏移量接续。
 * 4. 渲染时 (posts_mian_list_card) 手动 Loop 输出置顶文章。
 */
function tailwind_fix_sticky_pagination($query) {
    if (is_admin() || !$query->is_main_query() || !is_home()) {
        return;
    }

    $sticky_posts = get_option('sticky_posts');
    if (empty($sticky_posts)) {
        return;
    }

    $ppp = (int) get_option('posts_per_page');
    $sticky_count = count($sticky_posts);
    
    // 1. 彻底排除置顶文章，防止混在普通流中
    $query->set('post__not_in', $sticky_posts);
    $query->set('ignore_sticky_posts', true);

    if ($query->is_paged()) {
        // --- Page 2+ ---
        // 首页实际上只展示了 ($ppp - $sticky) 篇普通文章
        // 所以 Page 2 应该从那里开始接
        $paged = $query->get('paged');
        $offset = ($ppp - $sticky_count) + ( ($paged - 2) * $ppp );
        
        $query->set('offset', $offset);
    } else {
        // --- Page 1 ---
        // 只请求剩余名额
        $query_count = $ppp - $sticky_count;
        if ($query_count < 1) $query_count = 1;
        
        $query->set('posts_per_page', $query_count);
    }
}
add_action('pre_get_posts', 'tailwind_fix_sticky_pagination');

/**
 * 修复 found_posts
 */
function tailwind_fix_sticky_pagination_count($found_posts, $query) {
    if ($query->is_home() && $query->is_main_query()) {
        $sticky_posts = get_option('sticky_posts');
        if (!empty($sticky_posts)) {
            // 加回被排除的置顶文章数量
            return $found_posts + count($sticky_posts);
        }
    }
    return $found_posts;
}
add_filter('found_posts', 'tailwind_fix_sticky_pagination_count', 10, 2);










/**
 * 多级菜单：给父级 LI 注入 group/relative，子菜单默认隐藏，悬停显示（桌面端）
 */
function tailwind_nav_menu_item_classes($classes, $item, $args) {
    if (!empty($args->theme_location) && $args->theme_location === 'primary') {
        // 让 sub-menu 能用 group-hover 生效，并且绝对定位相对父级
        $classes[] = 'group';
        $classes[] = 'relative';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'tailwind_nav_menu_item_classes', 10, 3);

/**
 * 多级菜单：sub-menu 样式（更窄 + p-4）
 * - 移动端：保持可见并缩进（更易用）
 * - 桌面端：默认隐藏，hover 父级显示；面板宽度更紧凑、内边距 p-4
 */
function tailwind_nav_menu_submenu_css_class($classes, $args, $depth) {
    if (!empty($args->theme_location) && $args->theme_location === 'primary') {
        $mobile  = 'block pl-4 border-l border-gray-100 my-1 space-y-1';
        // 桌面端：加 top-full 和 mt-2 留出间距，确保可鼠标移入
        // 关键：md:pt-4 + md:mt-2 避免父级与子菜单之间出现“缝隙”导致 hover 丢失
        $desktop = 'md:absolute md:top-full md:left-0 md:mt-2 md:w-40 md:bg-white md:rounded-md md:shadow-lg md:border md:border-gray-100 md:hidden md:group-hover:block md:z-50 md:p-4';
        $classes[] = $mobile . ' ' . $desktop;
    }
    return $classes;
}
add_filter('nav_menu_submenu_css_class', 'tailwind_nav_menu_submenu_css_class', 10, 3);

/**
 * 菜单样式注入 (方案 A)
 * 自动为菜单链接添加 Tailwind 样式
 */
function tailwind_nav_menu_link_attributes($atts, $item, $args) {
    if ($args->theme_location == 'primary') {
        // 通用样式：字体、颜色、过渡
        // 移动端优先：块级显示、内边距、圆角、背景悬停 (hover:bg-gray-50)
        // 桌面端 (md:)：取消背景悬停、取消内边距（由外层控制间距）
        $class = 'block py-2 px-3 text-gray-700 hover:text-blue-600 font-medium rounded hover:bg-gray-50 md:hover:bg-transparent md:h-16 md:flex md:items-center md:py-0 md:px-0 transition-colors duration-200';
        
        // 高亮当前选中的菜单项 (匹配 class 或当前页状态)
        if (in_array('current-menu-item', $item->classes) || $item->current || $item->current_item_ancestor) {
            $class .= ' text-blue-600';
        }

        $atts['class'] = isset($atts['class']) ? $atts['class'] . ' ' . $class : $class;
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'tailwind_nav_menu_link_attributes', 10, 3);