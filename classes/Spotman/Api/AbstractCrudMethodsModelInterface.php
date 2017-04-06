<?php
namespace Spotman\Api;

interface AbstractCrudMethodsModelInterface
{
    public function create();

    public function update();

    public function save();

    public function delete();
}
