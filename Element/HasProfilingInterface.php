<?php

namespace ModSync\Element;

use ModSync;

interface HasProfilingInterface {

    public function beginProfiling();

    public function reportProfiling();
}