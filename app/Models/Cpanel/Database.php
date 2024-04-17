<?php

namespace App\Models\Cpanel;

class Database extends \App\Models\Cpanel\Base {

    /**
     * @var string
     */
    private $module = 'Mysql';

    /**
     * Return MySQL databases
     * https://api.docs.cpanel.net/openapi/cpanel/operation/create_database/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getMysqlDBs()
    {
        $response = $this->execute($this->module, 'list_databases');
        return $response;
    }

    /**
     * Create MySQL database
     * https://api.docs.cpanel.net/openapi/cpanel/operation/create_database/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function createMysqlDB($name)
    {
        $params = [
            'name' => $name
        ];

        $response = $this->execute($this->module, 'create_database', $params);
        return $response;
    }

    /**
     * Delete MySQL database
     * https://api.docs.cpanel.net/openapi/cpanel/operation/delete_database/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function deleteMysqlDB($name)
    {
        $params = [
            'name' => $name
        ];

        $response = $this->execute($this->module, 'delete_database', $params);
        return $response;
    }

    /**
     * Return MySQL users
     * https://api.docs.cpanel.net/openapi/cpanel/operation/Mysql-list_users/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getMysqlUsers()
    {
        $response = $this->execute($this->module, 'list_users');
        return $response;
    }

    /**
     * Create MySQL user
     * https://api.docs.cpanel.net/openapi/cpanel/operation/Mysql-create_user/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function createMysqlUser($user, $password)
    {
        $params = [
            'name' => $user,
            'password' => $password
        ];

        $response = $this->execute($this->module, 'create_user', $params);
        return $response;
    }

    /**
     * Delete MySQL user
     * https://api.docs.cpanel.net/openapi/cpanel/operation/Mysql-delete_user/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function deleteMysqlUser($user)
    {
        $params = [
            'name' => $user
        ];

        $response = $this->execute($this->module, 'delete_user', $params);
        return $response;
    }

    /**
     * Update MySQL user privileges
     * https://api.docs.cpanel.net/openapi/cpanel/operation/set_privileges_on_database/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function updateMysqlUserPrivileges($database, $user)
    {
        $params = [
            'database'   => $database,
            'user'       => $user,
            'privileges' => 'ALL PRIVILEGES'
        ];

        $response = $this->execute($this->module, 'set_privileges_on_database', $params);
        return $response;
    }

    /**
     * Return MySQL name length
     * https://api.docs.cpanel.net/openapi/cpanel/operation/get_restrictions/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function getMysqlUserLength()
    {
        $response = $this->execute($this->module, 'get_restrictions');
        return $response;
    }

    /**
     * Enable remote MySQL host
     * https://api.docs.cpanel.net/openapi/cpanel/operation/add_host/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function addRemoteHost($host, $note = null)
    {
        $params = ['host' => $host];
        $response = $this->execute($this->module, 'add_host', $params);

        if($response && $note) {
            $params = ['note' => $note];
            $response = $this->execute($this->module, 'add_host_note', $params);
        }

        return $response;
    }

    /**
     * Delete remote MySQL host
     * https://api.docs.cpanel.net/openapi/cpanel/operation/delete_host/
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function removeRemoteHost($host)
    {
        $params = [
            'host' => $host
        ];

        $response = $this->execute($this->module, 'delete_host', $params);
        return $response;
    }
}