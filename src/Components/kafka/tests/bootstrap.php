<?php

declare(strict_types=1);

use function Imi\env;
use function Imi\ttyExec;
use function Yurun\Swoole\Coroutine\batch;

require \dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @return bool
 */
function checkHttpServerStatus()
{
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        try
        {
            $context = stream_context_create(['http' => ['timeout' => 1]]);
            $body = @file_get_contents('http://127.0.0.1:8080/', false, $context);
            if ('imi' === $body)
            {
                return true;
            }
        }
        catch (ErrorException $e)
        {
        }
    }

    return false;
}

/**
 * 开启服务器.
 *
 * @return void
 */
function startServer()
{
    $dirname = \dirname(__DIR__);
    $mode = env('KAFKA_TEST_MODE');
    if (!$mode)
    {
        throw new InvalidArgumentException('Invalid env KAFKA_TEST_MODE');
    }
    $servers = [
        'HttpServer'    => [
            'start'         => $dirname . '/example/bin/start-server.sh ' . $mode,
            'stop'          => $dirname . '/example/bin/stop-server.sh ' . $mode,
            'checkStatus'   => 'checkHttpServerStatus',
        ],
    ];

    $callbacks = [];
    foreach ($servers as $name => $options)
    {
        $callbacks[] = static function () use ($options, $name) {
            // start server
            $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
            echo "Starting {$name}...", \PHP_EOL;
            shell_exec($cmd);

            register_shutdown_function(static function () use ($name, $options) {
                \Swoole\Runtime::enableCoroutine(false);
                // stop server
                $cmd = $options['stop'];
                echo "Stoping {$name}...", \PHP_EOL;
                shell_exec($cmd);
                echo "{$name} stoped!", \PHP_EOL, \PHP_EOL;
            });

            if (($options['checkStatus'])())
            {
                echo "{$name} started!", \PHP_EOL;
            }
            else
            {
                throw new \RuntimeException("{$name} start failed");
            }
        };
    }

    batch($callbacks, 120, max(swoole_cpu_num() - 1, 1));
    register_shutdown_function(static function () {
        echo 'check ports...', \PHP_EOL;
        ttyExec(\PHP_BINARY . ' ' . __DIR__ . '/bin/checkPorts.php');
    });
}

startServer();
\Swoole\Coroutine::defer(static function () {
    \Imi\Event\Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [], null, \Imi\Swoole\Server\Event\Param\WorkerExitEventParam::class);
});
