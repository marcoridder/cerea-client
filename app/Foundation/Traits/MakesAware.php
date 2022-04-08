<?php namespace App\Foundation\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;

trait MakesAware
{
    protected function makeAware(string $contractToResolve)
    {
        app()->resolving($contractToResolve, function ($class) use ($contractToResolve)
        {
            $reflectionClass = (new \ReflectionClass($contractToResolve));
            $methods = $reflectionClass->getMethods();
            if ( ! $bindingMethod = reset($methods) ) {
                throw new BindingResolutionException("{$contractToResolve} has no method to bind");
            }
            $parameters = $bindingMethod->getParameters();
            $parameter = reset($parameters);
            if ( ! $parameter ) {

                throw new BindingResolutionException("{$bindingMethod} has no parameters to resolve the targeted class");
            }
            if ( ! $classToBind = $parameter->getType() ) {
                throw new BindingResolutionException("{$bindingMethod} has no type hinting to resolve the targeted class, typehint the {$parameter->getName()}");
            }
            $contractToBind = $classToBind->getName();
            $class->{$bindingMethod->getName()}(app()->make($contractToBind));
        });
    }
}
