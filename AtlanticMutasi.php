<?php
/*
.---------------------------------------------------------------------------.
|    Script: Atlantic Mutasi                                                |
|   Version: 1.4.1                                                          |
|   Release: November 24, 2019 (12:27 WIB)                                  |
|    Update: September 16, 2020 (13:02 WIB)                                 |
|                                                                           |
|                     Pasal 57 ayat (1) UU 28 Tahun 2014                    |
|      Copyright © 2019, Afdhalul Ichsan Yourdan. All Rights Reserved.      |
| ------------------------------------------------------------------------- |
| Hubungi Saya:                                                             |
| - Facebook    - Afdhalul Ichsan Yourdan   - https://s.id/ShennFacebook    |
| - Instagram   - ShennBoku                 - https://s.id/ShennInstagram   |
| - Telegram    - ShennBoku                 - https://t.me/ShennBoku        |
| - Twitter     - ShennBoku                 - https://s.id/ShennTwitter     |
| - WhatsApp    - 0857 7290 6190            - 0822 1158 2471                |
'---------------------------------------------------------------------------'
*/

class AtlanticMutasi
{
    private $url = 'https://api.atlantic-group.co.id';
    private $auth;
    private $key;
    
    private $proxy_use;
    private $proxy_url = 'proxy.rapidplex.com:3128'; // URL:Port
    private $proxy_auth = 'user:domainesia'; // Username:Password

    public function __construct($punten) {
        $this->auth = $punten['auth'];
        $this->key = $punten['key'];
        $this->proxy_use = (isset($punten['proxy'])) ? $punten['proxy'] : false;
    }

    public function info($q = '') {
        $bank = strtoupper($q);
        if(in_array($bank,['BCA','BNI','GOPAY','OVO'])) {
            return $this->connect($this->url.'/mutasi/info',['bank' => $bank]);
        } else {
            return $this->connect($this->url.'/mutasi/info');
        }
    }

    public function bca($from,$to,$qty = '',$desc = '') {
        return $this->connect($this->url.'/mutasi/bca',['from_date' => $from,'to_date' => $to,'quantity' => $qty,'description' => $desc]);
    }

    public function bni($from,$to,$qty = '',$desc = '') {
        return $this->connect($this->url.'/mutasi/bni',['from_date' => $from,'to_date' => $to,'quantity' => $qty,'description' => $desc]);
    }

    public function gopay($limit = 10,$qty = '',$desc = '') {
        return $this->connect($this->url.'/mutasi/gopay',['limit' => $limit,'quantity' => $qty,'description' => $desc]);
    }

    public function gopayTrf($act,$phone,$amount,$secure,$description = '') {
        if($act == 'detail') {
            return $this->connect($this->url.'/transfer/gopay/detail',['target' => $phone]);
        } else if($act == 'transfer') {
            return $this->connect($this->url.'/transfer/gopay/',['target' => $phone,'jumlah' => $amount,'pin' => $secure,'description' => $description]);
        } else {
            return ['result' => false,'data' => null,'message' => 'Invaliid Action!'];
        }
    }

    public function ovo($limit = 10,$qty = '',$desc = '') {
        return $this->connect($this->url.'/mutasi/ovo',['limit' => $limit,'quantity' => $qty,'description' => $desc]);
    }

    public function ovoBank() {
        return $this->connect($this->url.'/transfer/ovo/data-bank');
    }

    public function ovoTrf($act,$phone,$amount,$secure,$description = '') {
        if($act == 'detail') {
            return $this->connect($this->url.'/transfer/ovo/detail',['type' => 'OVO','target' => $phone]);
        } else if($act == 'transfer') {
            return $this->connect($this->url.'/transfer/ovo/',['type' => 'OVO','target' => $phone,'jumlah' => $amount,'pin' => $secure,'description' => $description]);
        } else {
            return ['result' => false,'data' => null,'message' => 'Invaliid Action!'];
        }
    }

    public function ovoTrfBank($act,$bank,$reknya,$amount,$secure,$description = '') {
        if($act == 'detail') {
            return $this->connect($this->url.'/transfer/ovo/detail',['type' => 'BANK','target' => $reknya,'bank_code' => $bank]);
        } else if($act == 'transfer') {
            return $this->connect($this->url.'/transfer/ovo/',['type' => 'BANK','target' => $reknya,'jumlah' => $amount,'pin' => $secure,'bank_code' => $bank,'description' => $description]);
        } else {
            return ['result' => false,'data' => null,'message' => 'Invaliid Action!'];
        }
    }

    # END POINT CONNECTION #

    private function connect($end_point,$postdata = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $end_point);
        if($this->proxy_use == true) curl_setopt($ch, CURLOPT_PROXY, $this->proxy_url);
        if($this->proxy_use == true) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_auth);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authentication: '.$this->auth]);
        curl_setopt($ch, CURLOPT_POST, 1);
        $postdata['api_key'] = $this->key;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $chresult = curl_exec($ch);
        $json_result = json_decode($chresult, true);
        return $json_result;
    }
}

$AtlMutasi = new AtlanticMutasi([
    'auth'  => '', // API Auth
    'key'   => '',  // API Key
    'proxy' => false // Proxy Connection
]);
