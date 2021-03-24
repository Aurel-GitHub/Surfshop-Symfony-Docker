<?php

namespace App\Entity;

use App\Entity\Category;

class Search
{

    
    /**
     * @var int
     *
     */
    public $page = 1;

    /**
     * @var string
     */
    public $string = '';

    /**
     * @var Category[]
     */
    public $categories = [];

    /**
     * @var null|float
     */
    public $max;

    /**
     * @var null|float
     */
    public $min;

}