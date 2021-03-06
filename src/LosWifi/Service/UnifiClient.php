<?php
namespace LosWifi\Service;

use LosWifi\Entity\Controller;

final class UnifiClient
{

    private $site;

    private $controller;

    private $loggedin = false;

    private $cookies;

    private $debug;

    public function __construct(Controller $controller, $site = "default", $debug = false)
    {
        $this->site = $site;
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
        if (version_compare($this->controller->getVersion(), 4) >= 0) {
            $content = $this->request($this->controller->getBaseUrl(). "/api/self/sites");
        } else {
            $content = $this->request($this->controller->getBaseUrl() . "/api/s/default/cmd/sitemgr",'json={"cmd":"get-sites"}');
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

        $content = $this->request($this->controller->getBaseUrl() . "/api/s/$site/$path");

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
        if (version_compare($this->controller->getVersion(), 4) >= 0) {
            // Controller 4
            curl_setopt($ch, CURLOPT_REFERER, $this->controller->getBaseUrl() . "/login");
            curl_setopt($ch, CURLOPT_URL, $this->controller->getBaseUrl() . "/api/login");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "username" => $this->controller->getUsername(),
                "password" => $this->controller->getPassword()
            ]));
        } else {
            // Controller 3
            curl_setopt($ch, CURLOPT_URL, $this->controller->getBaseUrl() . "/login");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "login=login&username=" . $this->controller->getUsername() . "&password=" . $this->controller->getPassword());
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
        $this->request($this->controller->getBaseUrl() . "/logout");
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
        if (! $this->loggedin) {
            return false;
        }
        $params['mac'] = strtolower($mac);

        $content = $this->request($this->controller->getBaseUrl() . "/api/s/" . $this->site . "/cmd/stamgr", json_encode($params));

        return $this->isRequestOk($content);
    }

    public function authorizeGuest($mac, $minutes)
    {
        return $this->handleMac($mac, ['cmd' => 'authorize-guest', 'minutes' => $minutes]);
    }

    public function unauthorizeGuest($mac)
    {
        return $this->handleMac($mac, ['cmd' => 'unauthorize-guest']);
    }

    public function reconnectSta($mac)
    {
        return $this->handleMac($mac, ['cmd' => 'kick-sta']);
    }

    public function blockSta($mac)
    {
        return $this->handleMac($mac, ['cmd' => 'block-sta']);
    }

    public function unblockSta($mac)
    {
        return $this->handleMac($mac, ['cmd' => 'unblock-sta']);
    }

    public function listGuests()
    {
        if (! $this->loggedin) {
            return false;
        }
        $content = $this->request($this->controller->getBaseUrl() . "/api/s/" . $this->site . "/stat/guest");

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
        $content = $this->request($this->controller->getBaseUrl() . "/api/s/" . $this->site . "/stat/voucher", "{" . $json . "}");

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
        $content = $this->request($this->controller->getBaseUrl() . "/api/s/" . $this->site . "/cmd/hotspot", "{" . $json . "}");
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
