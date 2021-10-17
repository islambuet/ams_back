<?php
$db_main=env('DB_DATABASE','basic');

define('TABLE_SYSTEM_TASKS', $db_main.'.system_tasks');
define('TABLE_SYSTEM_USERS_GROUPS', $db_main.'.users_groups');
define('TABLE_SYSTEM_USER_HIDDEN_COLUMNS', $db_main.'.system_users_hidden_columns');
define('TABLE_SYSTEM_TOKENS', $db_main.'.system_tokens');

define('TABLE_SYSTEM_CONFIGURATIONS', $db_main.'.system_configurations');
define('TABLE_SYSTEM_HISTORIES', $db_main.'.system_histories');
define('TABLE_USERS_TYPES', $db_main.'.users_types');

