<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}
?>
<!DOCTYPE HTML>
<html <?php echo 'lang="' . esc_attr(get_bloginfo('language')) . '"';?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
	<meta name="renderer" content="webkit"/>
	<meta name="force-rendering" content="webkit"/>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
	<style>

</style>
</head>
<body <?php body_class('bg-gray-100 text-gray-800 flex flex-col min-h-screen'); ?>>

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center h-16">
            
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-2">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-2 text-xl font-bold text-gray-800 hover:text-blue-600 transition group">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center shadow-md group-hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span><?php bloginfo('name'); ?></span>
                </a>
            </div>
            
            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'flex items-stretch space-x-4', // 菜单项间距（拉伸到统一高度）
                    'fallback_cb'    => false,
                ));
                ?>
                <?php if (!has_nav_menu('primary')) : ?>
                    <div class="flex space-x-6">
                        <a href="<?php echo home_url('/'); ?>" class="text-gray-600 hover:text-blue-600 font-medium">首页</a>
                        <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">未设置菜单</a>
                    </div>
                <?php endif; ?>
                
                <!-- 预留右侧操作区 (如搜索/登录) -->
                <div class="ml-6 pl-6 border-l border-gray-200 hidden lg:flex items-center space-x-3">
                    <button class="text-gray-400 hover:text-blue-600 transition p-2 rounded-full hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </nav>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-btn" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-blue-600 focus:outline-none transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-xl absolute w-full left-0 z-40 animate-fade-in-down origin-top">
             <div class="container mx-auto px-4 py-2">
                 <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'flex flex-col space-y-1 py-2',
                        'fallback_cb'    => false,
                    ));
                 ?>
                 <?php if (!has_nav_menu('primary')) : ?>
                    <div class="flex flex-col space-y-1 py-2">
                        <a href="<?php echo home_url('/'); ?>" class="block py-2 px-4 text-gray-600 hover:bg-gray-50 rounded">首页</a>
                    </div>
                 <?php endif; ?>
             </div>
        </div>
    </header>


<div class="container mx-auto px-4 py-8 flex-grow">
    <div class="flex flex-col lg:flex-row gap-4">
        
        <!-- Main Content -->
        <main class="w-full lg:w-3/4">
            <?php echo posts_mian_list_card(); ?>
        </main>

        <!-- Sidebar -->
<aside class="w-full lg:w-1/4 space-y-4">
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <?php dynamic_sidebar('sidebar-1'); ?>
    <?php else : ?>
        <!-- Static Fallback Content (Matched to your HTML) -->
        
        <!-- Search -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold mb-4 border-l-4 border-blue-500 pl-3">搜索</h3>
            <form role="search" method="get" class="flex" action="<?php echo home_url('/'); ?>">
                <input type="text" name="s" placeholder="搜索文章..." class="w-full px-4 py-2 border border-gray-300 rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <!-- About -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold mb-4 border-l-4 border-blue-500 pl-3">关于博主</h3>
            <div class="flex flex-col items-center">
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff&size=128" alt="Avatar" class="w-24 h-24 rounded-full mb-4">
                <p class="text-gray-600 text-center mb-4">请在 WordPress 后台-外观-小工具中添加“文本”小工具来替换此内容。</p>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold mb-4 border-l-4 border-blue-500 pl-3">分类目录</h3>
            <ul class="space-y-2">
                <?php 
                $categories = get_categories();
                foreach($categories as $category) {
                    echo '<li><a href="' . get_category_link($category->term_id) . '" class="flex justify-between items-center text-gray-600 hover:text-blue-600 transition group">';
                    echo '<span>' . $category->name . '</span>';
                    echo '<span class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full group-hover:bg-blue-100 group-hover:text-blue-600">' . $category->count . '</span>';
                    echo '</a></li>';
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>
</aside>

    </div>
</div>


    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-8 md:py-12 mt-auto">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4"><?php bloginfo('name'); ?></h3>
                    <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                        <?php bloginfo('description'); ?>
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">快速链接</h3>
                    <ul class="space-y-2 text-sm md:text-base">
                        <?php wp_list_pages(array('title_li' => '', 'depth' => 1)); ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">订阅我们</h3>
                    <p class="text-gray-400 mb-4 text-sm md:text-base">订阅我们的邮件列表，获取最新文章推送。</p>
                    <div class="flex">
                        <input type="email" placeholder="您的邮箱地址" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-l focus:outline-none focus:border-blue-500 text-white text-sm md:text-base">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700 transition text-sm md:text-base">订阅</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 md:mt-10 pt-6 text-center text-sm text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        if(btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>
