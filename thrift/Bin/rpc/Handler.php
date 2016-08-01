<?php
namespace Bin\rpc;

class Handler implements rpcIf
{
    public function sendMessage(\Bin\rpc\Message $msg)
    {
        return RetCode::SUCCESS;
    }
}