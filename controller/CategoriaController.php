<?php

class CategoriaController
{
    private $request;
    private $renderer;
    private $categoriaModel;

    public function __construct($request, $renderer, $categoriaModel)
    {
        $this->request = $request;
        $this->renderer = $renderer;
        $this->categoriaModel = $categoriaModel;
    }

}