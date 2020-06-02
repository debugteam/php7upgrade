<?php declare(strict_types=1);

/**
 * Class Migrate
 * Get some automatization into PHP7 Migration
 */
class Migrate {

    /**
     * @var array
     */
    private $allFiles = [];

    /**
     * @var string
     */
    private $projectDir = '';

    /**
     * Migrate constructor.
     * @param $projectDir
     */
    public function __construct($projectDir)
    {

        $this->projectDir = $projectDir;

    }

    /**
     *
     */
    public function processMigration() : void
    {

        $this->getFiles();
        foreach($this->allFiles as $filepath) {
            /**
             * @var SplFileInfo $filepath
             */
            $filecontent = file_get_contents($filepath->getPathname());
            $rocnfilecontent = $this->oldClassNames($filecontent);
            $mysqlifcontent = $this->oldMySQLDriver($rocnfilecontent, $filepath->getPathname());
            $finalcontent = $mysqlifcontent;
            if ($finalcontent != $filecontent) {
                file_put_contents($filepath->getPathname(), $finalcontent);
            }
        }

    }

    /**
     *
     */
    private function getFiles() : void
    {

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->projectDir));
        $this->allFiles = array_filter(iterator_to_array($iterator), function($file) {
            return $file->isFile() && $file->getExtension() == 'php';
        });

    }

    /**
     * @param string $filecontent
     * @return array
     */
    private function getClassNames($filecontent='') : array
    {

        $classnames = [];
        preg_match_all('/^\s?(abstract\s+)?class (\w+)/m', $filecontent, $matches, PREG_SET_ORDER);
        foreach($matches as $match) {
            $classnames[] = $match[2];
        }
        return $classnames;

    }

    /**
     * @param $filecontent
     * @return string
     */
    private function oldClassNames($filecontent) : string
    {

        $classnames = $this->getClassNames($filecontent);
        if (empty($classnames)) {
            return $filecontent;
        }

        if (!strpos($filecontent, 'function '.$classnames[0])) {
            return $filecontent;
        }

        foreach($classnames as $classname) {
            $filecontent = str_replace(
                'function '.$classname,
                'public function __construct',
                $filecontent
            );
        }

        return $filecontent;
    }

    /**
     * @param $filecontent
     * @return string
     */
    private function oldMySQLDriver($filecontent, $filepath) : string
    {

        $filecontent = str_replace('mysql_affected_rows', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbAffectedRows', $filecontent);
        $filecontent = str_replace('mysql_close', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbClose', $filecontent);
        $filecontent = str_replace('mysql_data_seek', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbDataSeek', $filecontent);
        $filecontent = str_replace('mysql_errno', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbErrno', $filecontent);
        $filecontent = str_replace('mysql_error', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbError', $filecontent);
        $filecontent = str_replace('mysql_fetch_array', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFetchArray', $filecontent);
        $filecontent = str_replace('mysql_fetch_assoc', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFetchAssoc', $filecontent);
        $filecontent = str_replace('mysql_fetch_lengths', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFetchLengths', $filecontent);
        $filecontent = str_replace('mysql_fetch_object', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFetchObject', $filecontent);
        $filecontent = str_replace('mysql_fetch_row', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFetchRow', $filecontent);
        $filecontent = str_replace('mysql_field_seek', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFieldSeek', $filecontent);
        $filecontent = str_replace('mysql_free_result', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbFreeResult', $filecontent);
        $filecontent = str_replace('mysql_get_client_info', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbGetClientInfo', $filecontent);
        $filecontent = str_replace('mysql_get_host_info', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbGetHostInfo', $filecontent);
        $filecontent = str_replace('mysql_get_proto_info', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbGetProtoInfo', $filecontent);
        $filecontent = str_replace('mysql_get_server_info', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbGetServerInfo', $filecontent);
        $filecontent = str_replace('mysql_info', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbInfo', $filecontent);
        $filecontent = str_replace('mysql_insert_id', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbInsertId', $filecontent);
        $filecontent = str_replace('mysql_num_rows', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbNumRows', $filecontent);
        $filecontent = str_replace('mysql_ping', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbPing', $filecontent);
        $filecontent = str_replace('mysql_query', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbQuery', $filecontent);
        $filecontent = str_replace('mysql_escape_string', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbRealEscapeStr', $filecontent);
        $filecontent = str_replace('mysql_real_escape_string', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbRealEscapeStr', $filecontent);
        $filecontent = str_replace('mysql_select_db', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbSelectDb', $filecontent);
        $filecontent = str_replace('mysql_set_charset', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbSetCharset', $filecontent);
        $filecontent = str_replace('mysql_stat', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbStat', $filecontent);
        $filecontent = str_replace('mysql_thread_id', '\Debugteam\MySQLiWrapper\MySQLiBase::getInstance()->dbThreadId', $filecontent);

        return $filecontent;
    }
}

$projectdir = '/var/www/projects/assessmentworks/application/';
$migrate = new Migrate($projectdir);
$migrate->processMigration();
