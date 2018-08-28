<?php declare(strict_types=1);

require_once PATH . 'Core/Model.php';

class TestModel extends Model
{
    public function getTestData()
    {
        return [1, 2, 3];
    }
}