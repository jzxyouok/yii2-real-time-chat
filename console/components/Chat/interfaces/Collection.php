<?php

namespace console\components\Chat\interfaces;

interface Collection
{
	public function save ($runValidation = true, $attributeNames = NULL);
	public function delete ();

	public function blocked ($args);
}