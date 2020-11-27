<?php declare(strict_types = 1);

namespace jotaa\core\core_interfaces;

use WeakReference;

interface CoreBehaviorInterface
{
    /**
     * run
     *
     * executes the attached behavior and returns its result
     *
     * @param array $parameters
     * @return mixed
     */
    public function run(array $parameters = []);
    public function getOwnerReference();
    public function getBehaviorName() : string;
}
