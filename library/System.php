<?php

// 自定义
defined('SF_DEBUG') or define('SF_DEBUG', true);
// 框架开始运行时间
defined('SF_START_TIME') or define('SF_START_TIME', microtime(true));
// 核心文件目录
defined('SF_LIBRARY_PATH') or define('SF_LIBRARY_PATH', __DIR__.DIRECTORY_SEPARATOR);
// 应用目录
defined('SF_APP_PATH') or define('SF_APP_PATH', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR);
// 入口目录
defined('SF_PUBLIC_PATH') or define('SF_PUBLIC_PATH', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR);