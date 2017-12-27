<?php
namespace app\models;
class Kafka
{
    //可以多个用,分隔
    public $broker_list = 'localhost:9092';
    public $topic = 'topic';
    //topic的物理分区
    public $partition = 0;
    protected $producer = NULL;
    protected $consumer = NULL;

    public function __construct()
    {
        $rk = new \RdKafka\Producer();
        $rk->setLogLevel(LOG_DEBUG);
        if (empty($rk->addBrokers($this->broker_list)))
        {
            throw new \yii\base\InvalidConfigException('addbroker error');
        }
        $this->producer = $rk;
    }

    public function send($messages = [])
    {
        $topic = $this->producer->newTopic($this->topic);
        return $topic->produce(RD_KAFKA_PARTITION_UA, $this->partition, json_encode($messages));
    }

    public function consumer($object, $callback)
    {
        $conf = new \RdKafka\Conf();
        $conf->set('group.id', 0);
        $conf->set('metadata.broker.list', $this->broker_list);

        $topicConf = new \RdKafka\TopicConf();
        //largest表示接受接收最大的offset(即最新消息),smallest表示最小offset,即从topic的开始位置消费所有消息
        $topicConf->set('auto.offset.reset', 'smallest');

        $conf->setDefaultTopicConf($topicConf);

        $consumer = new \RdKafka\KafkaConsumer($conf);

        //可以订阅多个
        //监听消息队列
        $consumer->subscribe([$this->topic]);

        echo 'waiting for messages......';
        while (TRUE)
        {
            // The argument is the timeout.
            $message = $consumer->consume(1000);
            //print_r($message);
            //RD_KAFKA_RESP_ERR_NO_ERROR = 0
            if ($message->err == RD_KAFKA_RESP_ERR_NO_ERROR)
            {
                echo $message->payload, "\n";
                call_user_func(array($object, $callback), $message->payload);
            }
            sleep(1);
        }
    }
}