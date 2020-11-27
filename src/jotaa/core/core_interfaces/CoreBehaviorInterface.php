<?php declare(strict_types = 1);

namespace jotaa\core\core_interfaces;

use WeakReference;

interface CoreBehaviorInterface
{
    public function run(array $parameters = []) : void;
    public function getOwnerReference();
    public function getBehaviorName() : string;
}
