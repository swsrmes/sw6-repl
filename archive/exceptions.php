<?php

class DALException extends \Exception
{
    public string $error;

    private function __construct(string $error)
    {
        $this->error = $error;
        parent::__construct($error);
    }

    public static function not_found(): void
    {
        throw new NotFoundException('not_found');
    }
    
    public static function bad_request(): void
    {
        throw new DALException('bad_request');
    }
}

// separate file (psr-4 -.- )
class NotFoundException extends DALException {}


try {
    $foo = 'bar';

    DALException::not_found();

} catch (NotFoundException $e) {

    echo $e->getMessage();
}
