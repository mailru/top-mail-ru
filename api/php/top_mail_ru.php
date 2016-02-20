<?php

class TopMailRu
{
  private $apikey;
  private $returnArray;
  private $session;

  public function __construct($apikey, $returnArray) {
    $this->apikey = $apikey;
    $this->returnArray = $returnArray;
  }
  private function bitxor($o1, $o2) {
    $res = '';
    $runs = strlen($o1);
    for($i=0; $i<$runs; $i++)
        $res .= $o1[$i] ^ $o2[$i];
    return $res;
  }
  private function request($path, $argsArray, $returnArray) {

    $url = 'http://top.mail.ru' . $path . '?' . http_build_query($argsArray);

    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    try {
      $data = curl_exec($ch);
      curl_close($ch);
//print($data);
      $data = json_decode($data, $returnArray);
//print_r($data);
    } catch(Exception $e) {
      print("\nException: " . $e->getCode() . " (" . $e->getMessage() . ")\n");
      $data = null;
    }
    return $data;
  }

  public function registerSite($args) {
    if ($this->apikey) $args['apikey'] = $this->apikey;
    return $this->request('/json/add', $args, $this->returnArray);
  }
  public function editSite($id, $password, $args) {
    if ($this->apikey) $args['apikey'] = $this->apikey;
    if ($this->session) $args['session'] = $this->session;
    $args['id'] = $id;
    $args['password'] = $password;
    return $this->request('/json/edit', $args, $this->returnArray);
  }
  public function getCode($id, $password, $args) {
    if ($this->apikey) $args['apikey'] = $this->apikey;
    if ($this->session) $args['session'] = $this->session;
    $args['id'] = $id;
    $args['password'] = $password;
    return $this->request('/json/code', $args, $this->returnArray);
  }

  public function setSession($session) {
    if ($session) $this->session = $session;
  }
  public function login($id, $password) {
    $args = array('id' => $id, 'password' => $password, 'action' => 'json');
    if ($this->apikey) $args['apikey'] = $this->apikey;
    if ($this->session) $args['session'] = $this->session;
    $res = $this->request('/json/login', $args, true);
    if ($res['session']) $this->session = $res['session'];
    return (isset($res['logged']) && $res['logged'] == 'yes');
  }
  public function loginByHash($id, $ph) {
    $args = array('id' => $id, 'action' => 'json');
    if ($this->apikey) $args['apikey'] = $this->apikey;
    if ($this->session) $args['session'] = $this->session;
    $res = $this->request('/json/login', $args, true);
    if ($res['session']) $this->session = $res['session'];
    if (isset($res['salt'], $res['seed'])) {
      $hash2 = sha1($res['salt'] . $ph, true);
      $hash3 = $this->bitxor($ph, sha1($res['seed'] . $hash2, true));
      $args = array('id' => $id, 'action' => 'json', 'ph' => bin2hex($hash3), 'seed' => $res['seed']);
      if ($this->apikey) $args['apikey'] = $this->apikey;
      if ($this->session) $args['session'] = $this->session;
      $res = $this->request('/json/login', $args, true);
    }
    return (isset($res['logged']) && $res['logged'] == 'yes');
  }

  public function getStat($id, $password, $type, $args) {
    if ($this->apikey) $args['apikey'] = $this->apikey;
    if ($this->session) $args['session'] = $this->session;
    $args['id'] = $id;
    $args['password'] = $password;
    return $this->request('/json/' . $type, $args, $this->returnArray);
  }

}
