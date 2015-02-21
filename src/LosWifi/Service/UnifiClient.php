<?php
namespace LosWifi\Service;

final class UnifiClient
{

    private $username;

    private $password;

    private $site;

    private $baseurl;

    private $controller;

    private $loggedin = false;

    private $cookies;

    private $debug;

    public function __construct($username, $password, $baseurl = "https://127.0.0.1:8443", $site = "default", $controller = "4.0.0", $debug = false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->baseurl = $baseurl;
        $this->site = $site;
        if (strpos($controller, ".") !== false) {
            $controller = explode('.', $controller);
            $controller = $controller[0];
        }
        $this->controller = $controller;
        $this->debug = $debug;
    }

    public function __destruct()
    {
        if ($this->loggedin) {
            $this->logout();
        }
    }

    public function isDebug()
    {
        return $this->debug === true;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setSite($site)
    {
        $this->site = (string) $site;

        return $this;
    }

    public function getSites()
    {
        if (! $this->loggedin) {
            throw new \RuntimeException("Not logged in.");
        }
        if ($this->controller >= 4) {
            $content = $this->request($this->baseurl . "/api/self/sites");
        } else {
            $content = $this->request($this->baseurl . "/api/s/default/cmd/sitemgr",'json={"cmd":"get-sites"}');
        }

        return $this->getData($content);
    }

    private function getContent($site, $path)
    {
        if (! $this->loggedin) {
            throw new \RuntimeException("Not logged in.");
        }

        if ($site === null) {
            $site = $this->site;
        }

        $content = $this->request($this->baseurl . "/api/s/$site/$path");

        return $this->getData($content);
    }

    public function getDevices($site = null)
    {
        return $this->getContent($site, "stat/device");
    }

    public function getSta($site = null)
    {
        return $this->getContent($site, "stat/sta");
    }

    public function login()
    {
        $this->cookies = "";
        $ch = $this->createCurl();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if ($this->controller >= 4) {
            // Controller 4
            curl_setopt($ch, CURLOPT_REFERER, $this->baseurl . "/login");
            curl_setopt($ch, CURLOPT_URL, $this->baseurl . "/api/login");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "username" => $this->username,
                "password" => $this->password
            ]));
        } else {
            // Controller 3
            curl_setopt($ch, CURLOPT_URL, $this->baseurl . "/login");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "login=login&username=" . $this->username . "&password=" . $this->password);
        }
        $content = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = trim(substr($content, $header_size));
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        preg_match_all('|Set-Cookie: (.*);|U', substr($content, 0, $header_size), $results);
        if (isset($results[1])) {
            $this->cookies = implode(';', $results[1]);
            if (! empty($body) || $code == 302) {
                if (($code >= 200) && ($code < 400)) {
                    if (strpos($this->cookies, "unifises") !== FALSE) {
                        $this->loggedin = true;
                    }
                }
            }
        }

        return $this->loggedin;
    }

    public function logout()
    {
        if (! $this->loggedin) {
            return;
        }
        $this->request($this->baseurl . "/logout");
        $this->loggedin = false;
        $this->cookies = "";
    }

    private function request($url, $data = "")
    {
        if (is_array($data) && ! empty($data)) {
            $data = json_encode($data);
        }
        $ch = $this->createCurl();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (trim($data) != "") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
        } else {
            curl_setopt($ch, CURLOPT_POST, FALSE);
        }

        $content = curl_exec($ch);
        if ($this->debug === true) {
            echo "---------------------\n";
            echo "Url:  $url\n";
            echo "Data: $data\n";
            echo "---------------------\n";
            echo "Content: $content\n";
            echo "---------------------\n";
        }
        curl_close($ch);

        return json_decode($content, true);
    }

    private function isRequestOk($content)
    {
        if (array_key_exists('meta', $content) && array_key_exists('rc', $content['meta'])) {
            return $content['meta']['rc'] == "ok";
        }

        return false;
    }

    private function getData($content)
    {
        if ($this->isRequestOk($content)) {
            if (array_key_exists('data', $content)) {
                return $content['data'];
            }
        }

        return false;
    }

    private function createCurl()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->debug === true) {
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        }
        if ($this->cookies != "") {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookies);
        }

        return $ch;
    }

    private function handleMac($mac, $params)
    {
        $mac = strtolower($mac);
        if (! $this->loggedin) {
            return false;
        }
        $content = $this->request($this->baseurl . "/api/s/" . $this->site . "/cmd/stamgr", $params);

        return $this->isRequestOk($content);
    }

    public function authorizeGuest($mac, $minutes)
    {
        return $this->handleMac($mac, "{'cmd':'authorize-guest', 'mac':'" . $mac . "', 'minutes':" . $minutes . "}");
    }

    public function unauthorizeGuest($mac)
    {
        return $this->handleMac($mac, "{'cmd':'unauthorize-guest', 'mac':'" . $mac . "'}");
    }

    public function reconnectSta($mac)
    {
        return $this->handleMac($mac, "{'cmd':'kick-sta', 'mac':'" . $mac . "'}");
    }

    public function blockSta($mac)
    {
        return $this->handleMac($mac, "{'cmd':'block-sta', 'mac':'" . $mac . "'}");
    }

    public function unblockSta($mac)
    {
        return $this->handleMac($mac, "{'cmd':'unblock-sta', 'mac':'" . $mac . "'}");
    }

    public function listGuests()
    {
        if (! $this->loggedin) {
            return false;
        }
        $content = $this->request($this->baseurl . "/api/s/" . $this->site . "/stat/guest");

        return $this->getData($content);
    }

    public function getVouchers($create_time = "")
    {
        if (! $this->loggedin) {
            return false;
        }
        $json = "";
        if (trim($create_time) != "") {
            $json .= "'create_time':" . $create_time . "";
        }
        $content = $this->request($this->baseurl . "/api/s/" . $this->site . "/stat/voucher", "{" . $json . "}");

        return $this->getData($content);
    }

    public function createVoucher($minutes, $number_of_vouchers_to_create = 1, $note = "", $up = 0, $down = 0, $Mbytes = 0)
    {
        if (! $this->loggedin) {
            return false;
        }
        $json = "'cmd':'create-voucher','expire':" . $minutes . ",'n':" . $number_of_vouchers_to_create . "";
        if (trim($note) != "") {
            $json .= ",'note':'" . $note . "'";
        }
        if ($up > 0) {
            $json .= ",'up':" . $up . "";
        }
        if ($down > 0) {
            $json .= ", 'down':" . $down . "";
        }
        if ($Mbytes > 0) {
            $json .= ", 'bytes':" . $Mbytes . "";
        }
        $content = $this->request($this->baseurl . "/api/s/" . $this->site . "/cmd/hotspot", "{" . $json . "}");
        $data = $this->getData($content);
        if ($data !== false) {
            $obj = $content->data[0];
            $list = [];
            foreach ($this->getVouchers($obj->create_time) as $voucher) {
                $list[] = $voucher->code;
            }

            return $list;
        }

        return false;
    }
}
