<?php declare(strict_types=1);

require_once PATH . 'Core/Map.php';

class Request
{

    /**
     * The domain name of the current request.
     * 
     * Example: [www.domain.de]/path/to/app/post/12/edit
     *
     * @var array
     */
    private $domain;

    /**
     * The full path of the current request.
     * 
     * Example: www.domain.de[/path/to/app/post/12/edit]
     *
     * @var array
     */
    private $fullPath;

    /**
     * The base path of the current request.
     * 
     * Example: www.domain.de[/path/to/app]/post/12/edit
     *
     * @var string
     */
    private $basePath;

    /**
     * The path of the URL for the current request.
     * 
     * Example: www.domain.de/path/to/app[/post/12/edit]
     *
     * @var string
     */
    private $path;
    
    /**
     * The request method of the current request.
     *
     * @var string
     */
    private $method;

    /**
     * The GET and POST parameters of the current request.
     *
     * @var Map
     */
    private $params;

    /**
     * The cookies of the current request.
     *
     * @var Map
     */
    private $cookies;

    /**
     * A reference to the user's PHP session data.
     *
     * @var Map
     */
    private $session;

    /**
     * Create a new Request instance.
     */
    public function __construct()
    {
        $this->domain = $_SERVER['HTTP_HOST'];
        // REQUEST_URI ist ein potenzielles Sicherheitsrisiko, da der Benutzer eine URL wie folgt bereitstellen kann:
        // index.php? ../../../ ssh - Alle Pfade sollten vor der Ausgabe bereinigt werden
       
        $this->fullPath = $_SERVER['REQUEST_URI'];

        $this->basePath = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));

        $pathWithUrlParameters = str_replace($this->basePath, '', $_SERVER['REQUEST_URI']);
        $this->path = explode('?', $pathWithUrlParameters)[0];
        // Alternativ:
        // $prefixLength = strlen($this->basePath);
        // if ($postfixIndex = strpos($this->fullPath, "?")) {
        //     $pathLength = $postfixIndex - $prefixLength;
        //     $this->path = substr($this->fullPath, $prefixLength, $pathLength);
        // }
        // else {
        //     $this->path = substr($this->fullPath, $prefixLength);
        // }

        $this->method = $_SERVER['REQUEST_METHOD'];

        $params = array_merge($_POST, $_GET);
        $this->params = new Map($params);

        $this->cookies = new Map($_COOKIE);

        $this->session = new Map($_SESSION, true);
    }

    /**
     * Get the domain name of the current request.
     * 
     * Example: [www.domain.de]/path/to/app/post/12/edit
     *
     * @return string
     */
    public function domain() : string
    {
        return $this->domain;
    }

    /**
     * Get the full path of the current request.
     *
     * Example: www.domain.de[/path/to/app/post/12/edit]
     * 
     * @return string
     */
    public function fullPath() : string
    {
        return $this->fullPath;
    }

    /**
     * Get the full url of the current request.
     *
     * @return string
     */
    public function url() : string
    {
        return $this->domain . $this->fullPath;
    }

    /**
     * Get the base path of the current request.
     *      
     * The base path includes everything that follows the domain
     * and that preceeds the route.
     * 
     * Example: www.domain.de[/path/to/app]/post/12/edit
     *
     * @return string
     */
    public function basePath() : string
    {
        return  $this->basePath;
    }

    /**
     * Get the route path of the current request.
     *
     * Example: www.domain.de/path/to/app[/post/12/edit]
     * 
     * @return string
     */
    public function path() : string
    {
        return $this->path;
    }

    /**
     * Check if the request uses a given request method.
     *
     * @param string $method
     * @return bool
     */
    public function is(string $method) : bool
    {
        
        return strtolower($this->method) === strtolower($method);
    }

    /**
     * Get the request method of the current request.
     *
     * @return bool
     */
    public function method() : string
    {
        return $this->method;
    }

    /**
     * Get the GET and POST parameters of the current request.
     *
     * @return Map
     */
    public function params() : Map
    {
        return $this->params;
    }

    /**
     * Get the cookies of the current request.
     *
     * @return Map
     */
    public function cookies() : Map
    {
        return $this->cookies;
    }

    /**
     * Get the current user's PHP session.
     *
     * @return Map
     */
    public function session() : Map
    {
        return $this->session;
    }
}
