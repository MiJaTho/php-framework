<?php declare(strict_types=1);

require_once PATH . 'Core/Model.php';

class UserModel extends Model
{
    /**
     * Get a user by username or id.
     *
     * @param string|int
     * @return array|void
     */
    public function getUser($identifier) {
        if (is_string($identifier)) {
            $username = $this->db->escape_string($identifier);

            $sql = "SELECT * FROM `users` WHERE `username`='$username'";
        }

        else {
            $id = intval($identifier);

            $sql = "SELECT * FROM `users` WHERE `id`='$id'";
        }

        $result = $this->db->query($sql);

        if ($this->db->errno) {
            throw new Exception("DB Query error {$this->db->errno}: {$this->db->error}");
        }

        if ($result->num_rows) {
            $user = $result->fetch_assoc();
            $result->free();

            return $user;
        }
    }

    /**
     * Create a new user in the database.
     * 
     * @param $username
     * @param $password
     * @return bool
     */
    public function createUser(
        string $username,
        string $password
    ) : bool
    {
        $username = $this->db->escape_string($username);
        $password = $this->db->escape_string($password);

        $sql = "INSERT INTO `users` "
            . "(`username`,  `password`) VALUES "
            . "('$username', '$password')";
        
        // TODO: should return ID of new user
        return $this->db->query($sql);
    }
}
