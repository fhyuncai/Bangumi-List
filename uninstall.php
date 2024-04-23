<?php

if (!defined("WP_UNINSTALL_PLUGIN")) {
    exit;
}

delete_option("bangumi_list");
