namespace php Bin.rpc

typedef i64 Int

enum RetCode
{
  SUCCESS     = 0,
  PARAM_ERROR = 1000,
  ACCESS_DENY = 1001,
}

struct Message
{
	1:Int send_uid,
	2:Int recv_uid,
	3:Int channel_id,
	4:string name,
	5:string result,
}

service rpc
{
	RetCode sendMessage(1:Message msg)
}
