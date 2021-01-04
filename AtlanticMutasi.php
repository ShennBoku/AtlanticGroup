<?php
/*
.---------------------------------------------------------------------------.
|    Script: Atlantic Mutasi                                                |
|   Version: 1.5.6                                                          |
|   Release: November 24, 2019 (12:27 WIB)                                  |
|    Update: January 03, 2021 (15:27 WIB)                                   |
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
    private $url = 'https://atlantic-group.co.id/api/v1';
    public $id;
    public $key;
    public $sid;
    
    public $proxy_use;
    private $proxy_url = 'proxy.rapidplex.com:3128'; // URL:Port
    private $proxy_auth = 'user:domainesia'; // Username:Password

    public function __construct($punten) {
        $this->id = $punten['id'];
        $this->key = $punten['key'];
        $this->sid = $punten['sid'];
        $this->proxy_use = (isset($punten['proxy'])) ? $punten['proxy'] : false;
    }

    public function info($q = '') {
        $bank = strtoupper($q);
        if(in_array($bank,['BCA','BNI','GOPAY','OVO'])) {
            return $this->connect($this->url.'/mutasi/info',['payment' => $bank]);
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

    /* Tidak bisa digunakana lagi */
    // public function gopayTrf($act,$phone,$amount,$secure,$description = '') {
    //     if($act == 'detail') {
    //         return $this->connect($this->url.'/transfer/gopay/detail',['target' => $phone]);
    //     } else if($act == 'transfer') {
    //         return $this->connect($this->url.'/transfer/gopay/',['target' => $phone,'jumlah' => $amount,'pin' => $secure,'description' => $description]);
    //     } else {
    //         return ['result' => false,'data' => null,'message' => 'Invaliid Action!'];
    //     }
    // }

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
        if($this->proxy_use == true) curl_setopt($ch, CURLOPT_PROXY, $this->proxy_url);
        if($this->proxy_use == true) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_auth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $postdata['key'] = $this->key;
        $postdata['sid'] = $this->sid;
        $postdata['sign'] = md5($this->id.$this->key);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}

$AtlMutasi = new AtlanticMutasi([
    'id'    => '', // API ID
    'key'   => '', // API Key
    'sid'   => '', // Subscription ID
    'proxy' => false // Proxy Connection
]);
