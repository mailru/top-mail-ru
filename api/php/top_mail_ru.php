<?php

/**
 * Class TopMailRu
 */
class TopMailRu
{
    const BASE_URL = 'http://top.mail.ru';

    private $apiKey;
    private $returnAsArray;
    private $session;

    /**
     * TopMailRu constructor.
     * @param $apiKey
     * @param $returnAsArray
     */
    public function __construct($apiKey, $returnAsArray)
    {
        $this->apiKey = $apiKey;
        $this->returnAsArray = $returnAsArray;
    }

    /**
     * @param $o1
     * @param $o2
     * @return string
     */
    protected function bitxor($o1, $o2)
    {
        $result = '';
        $runs = strlen($o1);
        for ($i = 0; $i < $runs; $i++)
            $result .= $o1[$i] ^ $o2[$i];
        return $result;
    }

    /**
     * @param $path
     * @param $argsArray
     * @param $returnAsArray
     * @return mixed|null
     */
    protected function request($path, $argsArray, $returnAsArray)
    {
        $url = static::BASE_URL . $path . '?' . http_build_query($argsArray);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = null;
        try {
            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data, $returnAsArray);
        } catch (Exception $e) {
            echo "Exception: {$e->getCode()} ({$e->getMessage()})", PHP_EOL;
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getKeyAndSession()
    {
        $args = array();
        if ($this->apiKey) {
            $args['apikey'] = $this->apiKey;
        }
        if ($this->session) {
            $args['session'] = $this->session;
        }
        return $args;
    }

    /**
     * @param $args
     * @return mixed|null
     */
    public function registerSite($args)
    {
        $args += $this->getKeyAndSession();
        return $this->request('/json/add', $args, $this->returnAsArray);
    }

    /**
     * @param $id
     * @param $password
     * @param $args
     * @return mixed|null
     */
    public function editSite($id, $password, $args)
    {
        $args += $this->getKeyAndSession();
        $args['id'] = $id;
        $args['password'] = $password;
        return $this->request('/json/edit', $args, $this->returnAsArray);
    }

    /**
     * @param $id
     * @param $password
     * @param $args
     * @return mixed|null
     */
    public function getCode($id, $password, $args)
    {
        $args += $this->getKeyAndSession();
        $args['id'] = $id;
        $args['password'] = $password;
        return $this->request('/json/code', $args, $this->returnAsArray);
    }

    /**
     * @param $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @param $id
     * @param $password
     * @return bool
     */
    public function login($id, $password)
    {
        $args = array_merge(
            array('id' => $id, 'password' => $password, 'action' => 'json'),
            $this->getKeyAndSession()
        );
        $res = $this->request('/json/login', $args, true);
        if ($res['session']) {
            $this->session = $res['session'];
        }
        return isset($res['logged']) && $res['logged'] === 'yes';
    }

    /**
     * @param $id
     * @param $ph
     * @return bool
     */
    public function loginByHash($id, $ph)
    {
        $args = array_merge(
            array('id' => $id, 'action' => 'json'),
            $this->getKeyAndSession()
        );
        $response = $this->request('/json/login', $args, true);
        if ($response['session']) {
            $this->session = $response['session'];
        }
        if (isset($response['salt'], $response['seed'])) {
            $hash2 = sha1($response['salt'] . $ph, true);
            $hash3 = $this->bitxor($ph, sha1($response['seed'] . $hash2, true));
            $args = array_merge(
                array('id' => $id, 'action' => 'json', 'ph' => bin2hex($hash3), 'seed' => $response['seed']),
                $this->getKeyAndSession()
            );
            $response = $this->request('/json/login', $args, true);
        }

        return (isset($response['logged']) && $response['logged'] === 'yes');
    }

    /**
     * @param $id
     * @param $password
     * @param $type
     * @param $args
     * @return mixed|null
     */
    public function getStat($id, $password, $type, $args)
    {
        $args += $this->getKeyAndSession();
        $args['id'] = $id;
        $args['password'] = $password;
        return $this->request('/json/' . $type, $args, $this->returnAsArray);
    }
}
