require('CashAddress.php');

function PubFromAddr($addr) {
        $first = strtolower(substr($addr,0,1));
        if ($first == 'q' || $first == 'p') {
                $addr = CashAddress::new2old($addr, false);
        }
        print "$addr\n";

        $txs = json_decode(file_get_contents("https://bch-chain.api.btc.com/v3/address/{$addr}/tx"),true);

        foreach ($txs['data']['list'] as $t) {
                $txhash = $t['hash'];
                print " $txhash\n";
                $tx = json_decode(file_get_contents('https://bch-chain.api.btc.com/v3/tx/'.$txhash.'?verbose=3'),true);
                foreach ($tx['data']['inputs'] as $input) {
                        foreach ($input['prev_addresses'] as $a) {
                                if ($a == $addr) {
                                        $parts = preg_split("/\s+/",$input['script_asm']);
                                        $pubkey = array_pop($parts);
                                        return $pubkey;
                                }
                        }
                }
        }
}
