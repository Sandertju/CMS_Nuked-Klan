<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_forums_messages`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_forums_messages` (
            `id` int(5) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `txt` text NOT NULL,
            `date` varchar(12) NOT NULL default \'\',
            `edition` text NOT NULL,
            `auteur` text NOT NULL,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `auteur_ip` varchar(20) NOT NULL default \'\',
            `bbcodeoff` int(1) NOT NULL default \'0\',
            `smileyoff` int(1) NOT NULL default \'0\',
            `cssoff` int(1) NOT NULL default \'0\',
            `usersig` int(1) NOT NULL default \'0\',
            `emailnotify` int(1) NOT NULL default \'0\',
            `thread_id` int(5) NOT NULL default \'0\',
            `forum_id` mediumint(10) NOT NULL default \'0\',
            `file` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `thread_id` (`thread_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT id, txt FROM `'. $db_prefix .'_forums_messages`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['txt'] = mysql_real_escape_string(bbcode(stripslashes($row['txt']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_forums_messages` SET txt = \''. $row['txt'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>