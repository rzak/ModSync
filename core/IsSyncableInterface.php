<?php

namespace ModSync;

interface IsSyncableInterface {

    public function sync();

    public function isSyncable();

    public function onInsert();

    public function onUpdate();
}