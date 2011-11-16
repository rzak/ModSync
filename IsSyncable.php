<?php

interface ModSync_IsSyncable
{

    public function sync();
    public function isSyncable();
    public function onInsert();
    public function onUpdate();
}