<?php
namespace Bin\rpc;

class Handler implements rpcIf
{
    public function sendMessage(\Bin\rpc\Message $msg)
    {
        print_r($msg);
        return RetCode::SUCCESS;
    }
}
