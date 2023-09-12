<?php

// 如果 uninstall.php不是被 wp 调用，则 exit
if (!defined("WP_UNINSTALL_PLUGIN")) {
    exit;
}

delete_option("bangumi_list");