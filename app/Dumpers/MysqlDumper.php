<?php

namespace Pavliq\LaravelBackupPackage\Dumpers;

use Pavliq\LaravelBackupPackage\Interfaces\DumpInterface;

class MysqlDumper implements DumpInterface
{
    protected string $db_host;
    protected string $db_name;
    protected string $db_user;
    protected string $db_password;

    public function __construct($config)
    {
        $this->db_host = $config['host'];
        $this->db_user = $config['username'];
        $this->db_name = $config['database'];
        $this->db_password = $config['password'];
    }

    public function dump(string $fullPath): string
    {
        return sprintf(
            'mysqldump -h %s -u %s --password="%s" %s > "%s"',
            $this->db_host,
            $this->db_user,
            $this->db_password,
            $this->db_name,
            $fullPath
        );
    }
}