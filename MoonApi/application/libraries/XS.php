<?php

/**
 * 加载全文搜索引擎xunsearch相关api文件
 */
$xunsearchDir = Constants::XUNSEARCH_DIR_PATH . "lib/";
include_once $xunsearchDir . 'xs_cmd.inc.php';
include_once $xunsearchDir . 'XS.class.php';
include_once $xunsearchDir . 'XSDocument.class.php';
include_once $xunsearchDir . 'XSFieldScheme.class.php';
include_once $xunsearchDir . 'XSIndex.class.php';
include_once $xunsearchDir . 'XSServer.class.php';
include_once $xunsearchDir . 'XSSearch.class.php';
include_once $xunsearchDir . 'XSTokenizer.class.php';
?>