<?php declare(strict_types = 1);

namespace jotaa\core\core_interfaces;

use WeakReference;

interface CoreHasBehaviorInterface
{
    public function attachBehavior(string $behaviorName, CoreBehaviorInterface $behavior) : void;

    public function removeBehavior(string $behaviorName) : CoreBehaviorInterface;
    
    public function callBehavior(string $behaviorName, array $parameters = []) : void;
}
