<?php

class bitcoincz_blocks
{
    protected $db;

    public function __construct($DbManager)
    {
        $this->db = $DbManager;
    }

    public function regBitcoinczBlocks($statsData)
    {
        foreach($statsData['blocks'] as $blockNo => $blockData){
            $sql = "
                INSERT INTO bitcoincz_blocks
                    (block_no, is_mature, total_score, mining_duration, date_found, confirmations, total_shares, date_started, reward, nmc_reward, created)
                VALUES
                    (:block_no, :is_mature, :total_score, :mining_duration, :date_found, :confirmations, :total_shares, :date_started, :reward, :nmc_reward, now())
                ON DUPLICATE KEY UPDATE
                    is_mature       = VALUES(is_mature),
                    total_score     = VALUES(total_score),
                    mining_duration = VALUES(mining_duration),
                    date_found      = VALUES(date_found),
                    confirmations   = VALUES(confirmations),
                    total_shares    = VALUES(total_shares),
                    date_started    = VALUES(date_started),
                    reward          = VALUES(reward),
                    nmc_reward      = VALUES(nmc_reward);";

                $params = array(
                    ':block_no' => $blockNo,
                    ':is_mature' => $blockData['is_mature'],
                    ':total_score' => number_format($blockData['total_score'],5,'.',''),
                    ':mining_duration' => $blockData['mining_duration'],
                    ':date_found' => $blockData['date_found'],
                    ':confirmations' => $blockData['confirmations'],
                    ':total_shares' => $blockData['total_shares'],
                    ':date_started' => $blockData['date_started'],
                    ':reward' => isset($blockData['reward']) ? $blockData['reward'] : 0,
                    ':nmc_reward' => isset($blockData['nmc_reward']) ? $blockData['nmc_reward'] : 0,
                );

                $stmt = $this->db->execute($sql, $params);
        }
    }

    public function getTotalReward($block_no=0)
    {
        $sql = "SELECT SUM(reward) AS total FROM bitcoincz_blocks
                    WHERE block_no > :block_no";
        $params = array(':block_no' => $block_no,);

        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }

    public function getBitcoinczBlocks($block_no=0)
    {
        $sql = "SELECT * FROM bitcoincz_blocks
                    WHERE block_no > :block_no
                    ORDER BY block_no DESC";
        $params = array(':block_no' => $block_no,);

        $result = $this->db->fetchAll($sql, $params);
        return $result;
    }
}
?>