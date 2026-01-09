<?php
namespace Roc;
use Roc\Modules\Settings\Main as SettingsMain;
use Roc\Modules\Common\Main as CommonMain;
use Roc\Modules\Templates\Main as TemplatesMain;

if (!class_exists('Roc', false)) {
    class Roc
    {
        public function __construct()
        {
            // 注册自定义的自动加载函数
            spl_autoload_register([$this, 'Roc_autoload']);
            
            // 添加核心的动作钩子
            add_action('after_setup_theme', [$this, 'load_framework_and_modules']);
        }

        /**
         * 统一加载框架和初始化模块
         * 使用 after_setup_theme 钩子确保在主题功能设置后执行
         */
        public function load_framework_and_modules()
        {
            // 仅在后台区域加载 Codestar Framework
            if (is_admin()) {
                if (!class_exists('CSF')) {
                    // 加载 Codestar Framework
                    $codestar_path = ROC_THEME_DIR . ROC_DS . 'Modules' . ROC_DS . 'codestar-framework' . ROC_DS . 'codestar-framework.php';
                    $codestar_paths = ROC_THEME_DIR . ROC_DS . 'Modules' . ROC_DS . 'csf-framework' . ROC_DS . 'classes' . ROC_DS . 'zib-csf.class.php';
                    if (file_exists($codestar_path) && file_exists($codestar_paths)) {
                        // require_once $codestar_path;
                        // require_once $codestar_paths;
                        if (!class_exists('CSF')) {
                            add_action('admin_notices', function() {
                                echo '<div class="notice notice-error"><p>CSF 类不存在。可能是框架主文件未能成功加载或加载时机太晚。</p></div>';
                            });
                            return;
                        }
                    }
                }
            }
            
            // 初始化主题的设置模块 (后台)
            if (is_admin()) {
                $SettingsMain = new SettingsMain();
                $SettingsMain->init();
            }

            // 初始化主题的公共功能模块 (前后台都需要)
            $CommonMain = new CommonMain();
            $CommonMain->init();
            $TemplatesMain = new TemplatesMain();
            $TemplatesMain->init();
        }

        /**
         * 自定义自动加载函数
         * @param string $class 完整的类名 (包含命名空间)
         */
        public function Roc_autoload($class)
        {
            // 仅处理我们自定义的 'z\' 命名空间
            if (strpos($class, 'Roc\\') === 0) {
                // 移除命名空间前缀 'z\'
                $class_path = str_replace('Roc\\', '', $class);
                
                // 将命名空间分隔符 '\' 替换为目录分隔符
                $file_path = ROC_THEME_DIR . ROC_DS . str_replace('\\', ROC_DS, $class_path) . '.php';

                if (file_exists($file_path)) {
                    require_once $file_path;
                }
            }
        }
    }

    new Roc();
}