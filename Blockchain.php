<?php
/**
 * Created by PhpStorm.
 * User: ar414.com@gmail.com
 * Date: 2020/2/2
 * Time: 18:35
 */
require_once "./Block.php";
class Blockchain
{
    /**
     * @var array 区块列表
     */
    private $chain;

    /**
     * @var array 交易事务列表
     */
    private $currentTransactions;



    public function __construct()
    {
        $this->chain = [$this->createGenesisBlock()];
        $this->currentTransactions = [];
    }

    /**
     * 创建创世块
     * @return array
     */
    private function createGenesisBlock()
    {
        $block = [
            'index' => 1,
            'timestamp' => time(),
            'transactions' => [

            ],
            'proof' => 100,
            'previous_hash' => '0000000000000000000000000000000000000000000000000000000000000000',//参考BTC的第一个创世块
        ];
        $block['hash'] = (new Block($block['index'],$block['timestamp'],$block['transactions'],$block['previous_hash'],$block['proof']))->getHash();
        return $block;
    }

    /**
     * 新增交易事务
     * @param $senderPrivateKey
     * @param $senderAddress
     * @param $recipientAddress
     * @param $amount
     * @return bool
     */
    public function createTransaction($senderPrivateKey,$senderAddress,$recipientAddress,$amount)
    {
        $row = [
            'from'   => $senderAddress,
            'to'     => $recipientAddress,
            'amount' => $amount,
            'timestamp' => time()
        ];
        //TODO 私钥签名（就像支票签名）
        //TODO 区块链节点可以用发送者的签名来推导出公钥，再通过公钥验签并对比数据
        $this->currentTransactions[] = $row;
        return true;
    }

    /**
     * 增加新区块
     * @param int $proof
     * @return bool
     */
    public function addBlock(int $proof)
    {
        //上一个区块的信息
        $preBlockInfo = $this->chain[count($this->chain)-1];
        //验证工作证明
        if($this->checkProof($proof,$preBlockInfo['proof']) == false){
            return false;
        }
        //TODO 奖励矿工（在交易事务中）
        $block = [
            'index'        => count($this->chain) + 1,
            'timestamp'    => time(),
            'transactions' => $this->currentTransactions,
            'proof'        => $proof,
            'previous_hash' => $preBlockInfo['hash'],
            'hash'         => ''
        ];
        $block['hash'] = (new Block($block['index'],$block['timestamp'],$block['transactions'],$block['previous_hash'],$block['proof']))->getHash();
        //新增区块
        $this->chain[] = $block;
        //重置交易事务
        $this->currentTransactions = [];
        return true;
    }

    /**
     * 校验算力
     * @param string $proof
     * @param string $preProof
     * @return bool
     */
    private function checkProof(string $proof,string $preProof)
    {
        $string = $proof.$preProof;
        $hash   = hash('sha256',$string);
        if(substr($hash,0,4) == '0000'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 挖矿
     * @return void
     */
    public function mine()
    {
//        while (true)
//        {
            $proof = 0;
            //最新区块
            $blockInfo = $this->chain[count($this->chain)-1];
            $preProof  = $blockInfo['proof'];
            while (true)
            {
                $string = $proof.$preProof;
                $hash   = hash('sha256',$string);
                if(substr($hash,0,4) == '0000'){
                    //增加新区块
                    $this->addBlock($proof);
                    break;
                }
                $proof++;
            }

//        }
    }

    public function getChainList()
    {
        return $this->chain;
    }
}

$blockChainObj = new Blockchain();

//增加事务
$blockChainObj->createTransaction('','8527147fe1f5426f9dd545de4b27ee00',
    'a77f5cdfa2934df3954a5c7c7da5df1f',1);

//开启挖矿（挖到则生成新区块）
$blockChainObj->mine();

//查看当前区块列表
$blockList = $blockChainObj->getChainList();
var_dump($blockList);