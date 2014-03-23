<?php
/*
 * This file is part of the Black-System package.
 *
 * (c) black-solutions.com <info@black-solutions.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Created by Murat BEŞER <murat@black-solutions.com> 
 * Date: 23.03.2014 09:43
 */

namespace Black\OS\Inotify;

use Black\OS\Inotify\Exception\MissingPhpModuleException;
use Black\OS\Inotify\Exception\NullMaskException;
use Black\OS\Inotify\PathNotFoundException;
use Black\OS\Inotify\Result;

class Monitor
{

    const ACCESS = IN_ACCESS;
    const MODIFY = IN_MODIFY;
    const ATTRIB = IN_ATTRIB;
    const CLOSE_WRITE = IN_CLOSE_WRITE;
    const CLOSE_NOWRITE = IN_CLOSE_NOWRITE;
    const OPEN = IN_OPEN;
    const MOVED_TO = IN_MOVED_TO;
    const MOVED_FROM = IN_MOVED_FROM;
    const CREATE = IN_CREATE;
    const DELETE = IN_DELETE;
    const DELETE_SELF = IN_DELETE_SELF;
    const MOVE_SELF = IN_MOVE_SELF;
    const CLOSE = IN_CLOSE;
    const MOVE = IN_MOVE;
    const ALL_EVENTS = IN_ALL_EVENTS;
    const UNMOUNT = IN_UNMOUNT;
    const Q_OVERFLOW = IN_Q_OVERFLOW;
    const IGNORED = IN_IGNORED;
    const ISDIR = IN_ISDIR;
    const ONLYDIR = IN_ONLYDIR;
    const DONT_FOLLOW = IN_DONT_FOLLOW;
    const MASK_ADD = IN_MASK_ADD;
    const ONESHOT = IN_ONESHOT;


    private $handle;

    protected static function isInotifyPhpExtensionLoaded()
    {
        return extension_loaded('inotify');
    }

    public function __construct()
    {
        if (!$this->isInotifyPhpExtensionLoaded()) {
            throw new MissingPhpModuleException('Missing PHP module \'inotify\'.');
        }
        $this->handle = inotify_init();
    }

    public function addWatch($pathname, $mask)
    {
        if (!file_exists($pathname)) {
            throw new PathNotFoundException('Path \'' . $pathname . '\' not found.');
        }

        if (is_null($mask)) {
            throw new NullMaskException('Mask cannot be null.');
        }

        return inotify_add_watch($this->handle, $pathname, $mask);
    }

    public function queueLen()
    {
        return inotify_queue_len($this->handle);
    }

    public function read()
    {
        $read = array($this->handle);
        $write = null;
        $except = null;
        stream_select($read, $write, $except, 0);
        stream_set_blocking($this->handle, 0);
        if (($read_results =
                inotify_read($this->handle)) === false
        ) {
            return array();
        }
        $results = array();
        foreach ($read_results as $info) {
            $results[] = new Result(
                $info['wd'],
                $info['mask'],
                $info['cookie'],
                $info['name']);
        }
        return $results;
    }

    public function rmWatch($watch_descriptor)
    {
        return inotify_rm_watch($watch_descriptor);
    }

}

