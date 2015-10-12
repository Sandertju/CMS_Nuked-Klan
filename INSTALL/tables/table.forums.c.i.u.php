<?php

$dbTable->setTable($this->_session['db_prefix'] .'_forums');

/*
 * Callback function for update row of _forums database table
 */
function updateForumsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('REMOVE_EDITOR', $updateList))
        $setFields['comment'] = str_replace(array('<p>', '</p>'), '', $row['comment']);

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'comment');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_forums` (
            `id` int(5) NOT NULL auto_increment,
            `cat` int(11) NOT NULL default \'0\',
            `nom` text NOT NULL,
            `comment` text NOT NULL,
            `moderateurs` text NOT NULL,
            `niveau` int(1) NOT NULL default \'0\',
            `level` int(1) NOT NULL default \'0\',
            `ordre` int(5) NOT NULL default \'0\',
            `level_poll` int(1) NOT NULL default \'0\',
            `level_vote` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_forums` VALUES
        (1, 1, \''. $this->_db->quote($this->_i18n['FORUM']) .'\', \''. $this->_db->quote($this->_i18n['TEST_FORUM']) .'\', \'\', 0, 0, 0, 1 ,1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // TODO : Version ???
    if (version_compare($this->_session['version'], '1.7.9', '='))
        $dbTable->setUpdateFieldData('REMOVE_EDITOR', 'comment');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'comment');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateForumsRow');
}

?>