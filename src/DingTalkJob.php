<?php

namespace Shawn\DingException;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use DingNotice\DingTalk;
use Log;

class DingTalkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $exceptionInfo = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->exceptionInfo = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = [
            '- 时间：' . Carbon::now()->toDateTimeString(),
            '- Url地址：' . $this->exceptionInfo['url'],
            '- 异常类：' . $this->exceptionInfo['exceptionClass'],
            '- 异常信息：' . $this->exceptionInfo['message'],
            '- 文件：' . $this->exceptionInfo['file'],
            '- 行数：' . $this->exceptionInfo['line'],
        ];
        if ($this->exceptionInfo['is_trace']) {
            $message[] = '- Exception Trace：' . $this->exceptionInfo['trace'];
        }
        try {
            $dingConfig = config('ding.' . $this->exceptionInfo['robot']);
            $key = md5($this->exceptionInfo['message'] . $this->exceptionInfo['line']);
            if (!Cache::has($key)) {
                $ding = new DingTalk([
                    "default" => [
                        'enabled' => $dingConfig['enabled'],
                        'token' => $dingConfig['token'],
                        'timeout' => $dingConfig['timeout'],
                    ]
                ]);
                $ding->markdown($this->exceptionInfo['message'], implode(PHP_EOL, $message));
                Cache::put($key, 1, 30);
                Log::info("ding-send:" . json_encode($dingConfig) . $ding);
            }
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
