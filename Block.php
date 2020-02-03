<?php
/**
 * Created by PhpStorm.
 * User: ar414.com@gmail.com
 * Date: 2020/2/2
 * Time: 18:42
 */

class Block
{
    /**
     * @var integer 索引
     */
    private $index;

    /**
     * @var integer 时间戳
     */
    private $timestamp;

    /**
     * @var array 事务列表
     */
    private $transactions;

    /**
     * @var string 上一块的哈希值
     */
    private $previousHash;

    /**
     * @var integer 由工作证明算法生成的证明
     */
    private $proof;

    /**
     * @var string 当前块的哈希值
     */
    private $hash;

    /**
     * 通过调用方法返回新生成块的哈希
     * 防止外界改动
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    public function __construct($index,$timestamp,$transactions,$previousHash,$proof)
    {
        $this->index        = $index;
        $this->timestamp    = $timestamp;
        $this->transactions = $transactions;
        $this->previousHash = $previousHash;
        $this->proof        = $proof;
        $this->hash         = $this->blockHash();
    }

    /**
     * 当前块签名
     * @return string
     */
    private function blockHash()
    {
        //我们必须确保这个字典（区块）是经过排序的，否则我们将会得到不一致的哈希值
        $blockArray = [
            'index' => $this->index,
            'timestamp' => $this->timestamp,
            'transactions' => $this->transactions,
            'proof'        => $this->proof,
            'previous_hash' => $this->previousHash
        ];
        $blockString = json_encode($blockArray);
        return hash('sha256',$blockString);
    }


}