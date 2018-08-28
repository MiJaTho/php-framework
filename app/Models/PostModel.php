<?php declare(strict_types=1);

require_once PATH . 'Core/Model.php';

class PostModel extends Model
{
    public function getPosts(int $userId) :array
    {
        $userId = intval($userId);

        $sql = <<<QUERY
SELECT P.*, U.screenname, U.portrait
FROM posts P
JOIN users U ON P.user_id = U.id
WHERE `user_id` = $userId
ORDER BY p.created_at DESC
QUERY;
        
        
        $result = $this->db->query($sql);

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        return $posts;
    }

    public function createPost(int $userId, string $title, string $content)
    {
        $userId = intval($userId);
        $title = $this->db->escape_string($title);
        $content = $this->db->escape_string($content);

        $sql = <<<QUERY
INSERT INTO posts
(`user_id`, `title`, `content`) VALUES
($userId, '$title', '$content')
QUERY;
        
        return $this->db->query($sql);
    }
    
    
}