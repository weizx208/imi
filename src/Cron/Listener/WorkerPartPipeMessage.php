<?php
namespace Imi\Cron\Listener;

use Imi\App;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use Imi\Util\Process\ProcessType;
use Imi\Util\Process\ProcessAppContexts;

/**
 * @Listener("IMI.PIPE_MESSAGE.cronTask")
 */
class WorkerPartPipeMessage implements IEventListener
{
    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * @Inject("CronWorker")
     *
     * @var \Imi\Cron\CronWorker
     */
    protected $cronWorker;

    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        if(ProcessType::WORKER !== App::get(ProcessAppContexts::PROCESS_TYPE))
        {
            return;
        }
        $data = $e->getData()['data'];
        $this->cronWorker->exec($data['id'], $data['data'], $data['task'], $data['type']);
    }

}
