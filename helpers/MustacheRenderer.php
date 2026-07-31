<?php
require_once(__DIR__ . '/../vendor/autoload.php');
class MustacheRenderer
{
    private $mustache;
    private $database;

    public function __construct($viewsFolder, $database = null) {
        $this->database = $database;
        $this->mustache = new Mustache_Engine(['loader' => new Mustache_Loader_FilesystemLoader($viewsFolder
        ), 'partials_loader' => new Mustache_Loader_FilesystemLoader($viewsFolder),]);
    }

    public function render($viewName, $data = [])
    {
        $template = $this->mustache->loadTemplate($viewName);
        echo $template->render($data);
    }
}