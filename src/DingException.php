<?php

namespace Shawn\DingException;

class DingException
{
    // 使用laravel队列通知
    public static function notifyException($exception, $robot = 'default', bool $is_trace = false, bool $log = false)
    {
        $exceptionInfo['url'] = \request()->fullUrl();
        $exceptionInfo['exceptionClass'] = get_class($exception);
        $exceptionInfo['file'] = $exception->getFile();
        $exceptionInfo['line'] = $exception->getLine();
        $exceptionInfo['message'] = $exception->getMessage();
        $exceptionInfo['trace'] = $exception->getTraceAsString();
        $exceptionInfo['robot'] = $robot;
        //是否打印详细的trace信息
        $exceptionInfo['is_trace'] = $is_trace;
        $exceptionInfo['log'] = $log;

        dispatch(new DingTalkJob($exceptionInfo));
    }

}