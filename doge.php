<?php
require_once('jsonRPCClient.php');
  
const RPC_USER = "dogecoinrpc";
const RPC_PASSWORD = "password"; //must match dogecoin RPC  settings
const RPC_ADDRESS = "localhost:22555";
const BALANCE_THRESHOLD = 20;
 
 
const WALLET_PASSWORD = "";
const WALLET_PASSWORD_TIMEOUT = 2;

$m=new Mongo();
$db=$m->selectDB("doge");
$doge = createDogeRPCClient();

if($_GET['method']=="pay") {
$new_addr= $doge->getnewaddress();
$amt=$_GET['amt'];
$db->transactions->save(Array('addr'=>$new_addr,'amt'=>$amt,'complete'=>False));
echo json_encode($new_addr);
}

if($_GET['method']=="verify") {
$addr=$_GET['addr'];
$to_ver=$db->transactions->findOne(Array('addr'=>$addr));
$complete=$to_ver['complete'];
$amt=$to_ver['amt'];

$recvd=$doge->getreceivedbyaddress($addr,0);
if(!$complete && ($recvd+0.1)>= $amt) {
 $to_ver['complete']=True;
 $db->transactions->save($to_ver);
 echo json_encode(1);
}
else {
 echo json_encode(0);
}

}





	 /*
	 $valid = $doge->validateaddress($address);
	 if($valid["isvalid"] != 1){
		echo "Doge Address '$address' appears invalid please try again!";
		return;
	 }
         */

/* 
	$amount = rand(1,10);
	$doge->walletpassphrase(WALLET_PASSWORD,WALLET_PASSWORD_TIMEOUT);
    $doge->sendtoaddress($address,$amount);
	$doge->walletlock();
    echo "You've got <strong> $amount </strong> DOGE!";	
});
*/
 
function createDogeRPCClient(){
	return new jsonRPCClient("http://".RPC_USER.":".RPC_PASSWORD."@".RPC_ADDRESS);
}
 
 
 
 
?>
