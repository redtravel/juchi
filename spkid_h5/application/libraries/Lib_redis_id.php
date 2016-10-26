<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lib_redis_id {
 
    private $r;
    private $lockedNames = [];
 
    public function __construct(){
        $this->r=new Redis();
        $this->r->connect('192.168.20.93', 6379);
    }
 
    function set_queue_id($ids){
        if(is_array($ids) && isset($ids)){
            foreach ($ids as $id){
                $this->r->RPUSH('next_autoincrement',$id);
            }
        }
    }
 
    function get_next_autoincrement(){
        return $this->r->LPOP('next_autoincrement');
    }
    
    function get_size(){
        return $this->r->LSIZE('next_autoincrement');
    }
    
    /**

    * ����

    * @param  [type]  $name           ���ı�ʶ��

    * @param  integer $timeout        ѭ����ȡ���ĵȴ���ʱʱ�䣬�ڴ�ʱ���ڻ�һֱ���Ի�ȡ��ֱ����ʱ��Ϊ0��ʾʧ�ܺ�ֱ�ӷ��ز��ȴ�

    * @param  integer $expire         ��ǰ�����������ʱ��(��)���������0�������������ʱ������δ���ͷţ���ϵͳ���Զ�ǿ���ͷ�

    * @param  integer $waitIntervalUs ��ȡ��ʧ�ܺ�������Ե�ʱ����(΢��)

    * @return [type]                  [description]

    */
    public function lock($name, $timeout = 0, $expire = 15, $waitIntervalUs = 100000) {
        if ($name == null) return false;
        
        //ȡ�õ�ǰʱ��
        $now = time();
        
        //��ȡ��ʧ��ʱ�ĵȴ���ʱʱ��
        $timeoutAt = $now + $timeout;
        
        //�����������ʱ��
        $expireAt = $now + $expire;
 
        $redisKey = "Lock:{$name}";
        while (true) {
            
        //��rediskey���������ʱ�̴浽redis��������ʱ�̸����ᱻ�Զ��ͷ�
        $result = $this->r->setnx($redisKey, $expireAt);
 
        if ($result != false) {

            //����key��ʧЧʱ��
            $this->r->expire($redisKey, $expireAt);

            //������־�ŵ�lockedNames������
            $this->lockedNames[$name] = $expireAt;
            return true;
        }
 
            
        //����Ϊ��λ�����ظ���key��ʣ������ʱ��
        $ttl = $this->r->ttl($redisKey);


        //ttlС��0 ��ʾkey��û����������ʱ�䣨key�ǲ��᲻���ڵģ���Ϊǰ��setnx���Զ�������

        //�����������״�����Ǿ��ǽ��̵�ĳ��ʵ��setnx�ɹ��� crash ���½����ŵ�expireû�б�����

        //��ʱ����ֱ������expire��������Ϊ����
        if ($ttl < 0) {
            $this->r->set($redisKey, $expireAt);
            $this->lockedNames[$name] = $expireAt;
            return true;
        }
 
            
        /*****ѭ������������*****/

        //���û������ʧ�ܵĵȴ�ʱ�� ���� �ѳ������ȴ�ʱ���ˣ��Ǿ��˳�
        if ($timeout <= 0 || $timeoutAt < microtime(true)) break;
 
            
//�� $waitIntervalUs ����� ����
            usleep($waitIntervalUs);
 
        }
 
        return false;
    }
    
    /**

    * ����

    * @param  [type] $name [description]

    * @return [type]       [description]

    */
    public function unlock($name) {     
        //���ж��Ƿ���ڴ���
        if ($this->isLocking($name)) {
            //ɾ����
            if ($this->r->del("Lock:$name")) {

            //���lockedNames�������־
                unset($this->lockedNames[$name]);
                return true;
            }
        }
        return false;
    }
    
    /**

    * �жϵ�ǰ�Ƿ�ӵ��ָ�����ֵ���

    * @param  [type]  $name [description]

    * @return boolean       [description]

    */
    public function isLocking($name) {       
        //�ȿ�lonkedName[$name]�Ƿ���ڸ�����־��
        if (isset($this->lockedNames[$name])) {
            //��redis���ظ���������ʱ��
            return (string)$this->lockedNames[$name] = (string)$this->r->get("Lock:$name");
        }

        return false;
    }
    /**

    * �ͷŵ�ǰ���л�õ���

    * @return [type] [description]

    */
    public function unlockAll() {
        //�˱�־��������־�Ƿ��ͷ��������ɹ�
        $allSuccess = true;
        foreach ($this->lockedNames as $name => $expireAt) {
            if (false === $this->unlock($name)) {
                $allSuccess = false;    
            }
        }
        return $allSuccess;
    }

    /**

    * ���һ�� Task

    * @param  [type]  $name          ��������

    * @param  [type]  $id            ����id�����������飩

    * @param  integer $timeout       ��ӳ�ʱʱ��(��)

    * @param  integer $afterInterval [description]

    * @return [type]                 [description]

    */
    public function enqueue($name, $id, $timeout = 10, $afterInterval = 0) {       
        //�Ϸ��Լ��
        if (empty($name) || empty($id) || $timeout <= 0) return false;

        //����
        if (!$this->lock("Queue:{$name}", $timeout)) {
            Logger::get('queue')->error("enqueue faild becouse of lock failure: name = $name, id = $id");
            return false;
        }


        //���ʱ�Ե�ǰʱ�����Ϊ score
        $score = microtime(true) + $afterInterval;

        //���
        foreach ((array)$id as $item) {
            //���ж����Ƿ��Ѿ����ڸ�id��
            if (false === $this->r->zscore("Queue:$name", $item)) {
                $this->r->zadd("Queue:$name", $score, $item);
            }
        }

        //����
        $this->unlock("Queue:$name");

        return true;
    }
    
    /**

    * ����һ��Task����Ҫָ��$id �� $score

    * ���$score ������е�ƥ������ӣ�������Ϊ��Task�ѱ�������ӹ�����ǰ������ʧ�ܴ���

    * 

    * @param  [type]  $name    �������� 

    * @param  [type]  $id      �����ʶ

    * @param  [type]  $score   �����Ӧscore���Ӷ����л�ȡ����ʱ�᷵��һ��score��ֻ��$score�Ͷ����е�ֵƥ��ʱTask�Żᱻ����

    * @param  integer $timeout ��ʱʱ��(��)

    * @return [type]           Task�Ƿ�ɹ�������false������redis����ʧ�ܣ�Ҳ�п�����$score������е�ֵ��ƥ�䣨���ʾ��Task�Դӻ�ȡ������֮�������߳���ӹ���

    */
    public function dequeue($name, $id, $score, $timeout = 10) {       
        //�Ϸ��Լ��
        if (empty($name) || empty($id) || empty($score)) return false;

        //����
        if (!$this->lock("Queue:$name", $timeout)) {
            Logger:get('queue')->error("dequeue faild becouse of lock lailure:name=$name, id = $id");
            return false;
        }
         
        //����
        //��ȡ��redis��score
        $serverScore = $this->r->zscore("Queue:$name", $id);
        $result = false;

        //���жϴ�������score��redis��score�Ƿ���һ��
        if ($serverScore == $score) {
            //ɾ����$id
            $result = (float)$this->r->zrem("Queue:$name", $id);
            if ($result == false) {
                Logger::get('queue')->error("dequeue faild because of redis delete failure: name =$name, id = $id");
            }
        }

        //����
        $this->unlock("Queue:$name");

        return $result;
    }
    
    /**

    * ��ȡ���ж������ɸ�Task ���������

    * @param  [type]  $name    ��������

    * @param  integer $count   ����

    * @param  integer $timeout ��ʱʱ��

    * @return [type]           ��������[0=>['id'=> , 'score'=> ], 1=>['id'=> , 'score'=> ], 2=>['id'=> , 'score'=> ]]

    */
    public function pop($name, $count = 1, $timeout = 10) {        
        //�Ϸ��Լ��
        if (empty($name) || $count <= 0) return [];
        
        //����
        if (!$this->lock("Queue:$name")) {
            Logger::get('queue')->error("pop faild because of pop failure: name = $name, count = $count");
            return false;
        }
        
        //ȡ�����ɵ�Task
        $result = [];
        $array = $this->r->zRangeByScore("Queue:$name", '-inf', '+inf', array('withscores', $count)); 
        
    //�������$result������ �� ɾ����redis��Ӧ��id
        foreach ($array as $id => $score) {
            $result[] = ['id'=>$id, 'score'=>$score];
            $this->r->zrem("Queue:$name", $id);
        }
 
        
        //����
        $this->unlock("Queue:$name");
 
        return $count == 1 ? (empty($result) ? false : $result[0]) : $result;
    }
    
    /**

    * ��ȡ���ж��������ɸ�Task

    * @param  [type]  $name  ��������

    * @param  integer $count ����

    * @return [type]         ��������[0=>['id'=> , 'score'=> ], 1=>['id'=> , 'score'=> ], 2=>['id'=> , 'score'=> ]]

    */
    public function top($name, $count = 1) {
        
        //�Ϸ��Լ��
        if (empty($name) || $count < 1)  return [];
        
        //ȡ�����ɸ�Task
        $result = [];
        $array = $this->r->zRangeByScore("Queue:$name", "-inf", "+inf", array("withscores", $count)); 
        
        //��Task�����������
        foreach ($array as $id => $score) {
            $result[] = ['id'=>$id, 'score'=>$score];
        }
        
        //�������� 
        return $count == 1 ? (empty($result) ? false : $result[0]) : $result;       
    }
    
    
 
}
?>
