<?php

namespace ModSync\Element;

use StdClass;
use ModSync;

trait HasProfilingTrait {

    private $_profiling;

    /**
     * Begin profiling by recording start time
     */
    final public function beginProfiling() {
        if (null === $this->_profiling) {
            $this->_profiling = new StdClass;
            $this->_profiling->beginTime = microtime();
            $this->_profiling->enabled = (boolean) self::getModX()->getOption('modsync__profiling_enabled', null, true);
            $this->_profiling->threshold = (int) self::getModX()->getOption('modsync__profiling_threshold', null, 1);
        }
    }

    /**
     * Report execution time
     */
    final public function reportProfiling() {
        if (null !== $this->_profiling && $this->_profiling->enabled) {
            $this->_profiling->endTime = microtime();

            $begin = explode(' ', $this->_profiling->beginTime);
            $end = explode(' ', $this->_profiling->endTime);
            $this->_profiling->duration = ($end[1] + $end[0]) - ($begin[1] + $begin[0]);

            ModSync\Logger::debug(sprintf(get_called_class() . ': %s (%1.3f seconds)', $this->getName(), $this->_profiling->duration));
            if ($this->_profiling->duration > $this->_profiling->threshold) {
                ModSync\Logger::warn(sprintf(get_called_class() . ': inefficient code - %s (%1.3f seconds)', $this->getName(), $this->_profiling->duration));
            }
        }
    }

}