<?php

namespace Angkosal\Repository\Repositories\Contracts;

interface CriteriaInterface
{
    public function withCriteria(...$criteria);
}
