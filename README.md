<h1 align="center"> ding-exception </h1>

<p align="center"> .</p>


## Installing

```shell
$ composer require shawn/ding-exception
```

## Usage

 file app/Exceptions/Handler.php

## Contributing

```shell
use Shawn\DingException\DingException;

class Handler extends ExceptionHandler
{
  // ...
  
    public function report(Exception $exception)
    {
        // $robot 选择哪台机器人进行异常通知
        // $is_trace 是否通知详细的trace信息
        DingException::notifyException($exception,$robot,$is_trace);
        parent::report($exception);
    }

}
```
## 安装成功后执行
```shell
php artisan vendor:publish --provider="DingNotice\DingNoticeServiceProvider"
```